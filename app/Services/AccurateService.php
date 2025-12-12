<?php

namespace App\Services;

use App\Models\CustomItem;
use App\Models\Quotation;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class AccurateService
{

  public function getDatabaseList(): array
  {
    if (!session()->has('accurate_access_token')) {
      throw new Exception('Tidak bisa mengambil daftar database tanpa Access Token.');
    }

    $response = Http::withToken(session('accurate_access_token'))
      ->get(env('ACCURATE_API_URL') . '/api/db-list.do');

    if ($response->failed()) {
      Log::error('ACCURATE_ERROR - Gagal mengambil daftar database', $response->json() ?? ['body' => $response->body()]);
      throw new Exception("Gagal mendapatkan daftar database dari Accurate.");
    }
    return $response->json()['d'] ?? [];
  }

  public function getDatabaseHost()
  {
    $response = $this->client()->post('/api/api-token.do');
    if ($response->failed() || !isset($response->json()['d']['database']['host'])) {
      Log::error('ACCURATE_ERROR - Gagal mendapatkan host database', $response->json() ?? ['body' => $response->body()]);
      throw new Exception("Gagal mendapatkan host database dari Accurate.");
    }
    $host = $response->json()['d']['database']['host'];
    session(['accurate_host' => $host]);
    return $host;
  }


  protected function dataClient()
  {
    if (!session()->has('accurate_access_token')) {
      throw new Exception('Token Akses Accurate tidak ditemukan di session.');
    }
    if (!session()->has('accurate_database')) {
      throw new Exception('Database Accurate belum dipilih.');
    }

    $dbInfo = session('accurate_database');
    $host = $dbInfo['host'];
    $sessionId = $dbInfo['session']; // <-- Pastikan baris ini ada
    $accessToken = session('accurate_access_token');

    return Http::withToken($accessToken)
      ->withHeaders([
        'X-Session-ID' => $sessionId, // <-- Pastikan header ini ada
      ])
      ->acceptJson()
      ->baseUrl($host . '/accurate');
  }

  public function openDatabaseById(int $dbId): ?array
  {
    if (!session()->has('accurate_access_token')) {
      throw new Exception('Tidak bisa membuka database tanpa Access Token.');
    }

    try {
      // --- PERUBAHAN DIMULAI DI SINI ---
      // Tambahkan opsi untuk melacak pengalihan (redirect)
      $response = Http::withOptions([
        'track_redirects' => true
      ])->withToken(session('accurate_access_token'))
        ->post(env('ACCURATE_API_URL') . '/api/open-db.do', ['id' => $dbId]);

      if ($response->failed()) {
        return null;
      }

      $responseData = $response->json();

      // Cek apakah ada riwayat pengalihan
      $redirectHistory = $response->handlerStats()['redirect_history'] ?? [];
      if (!empty($redirectHistory)) {
        // Ambil URL terakhir (yang paling baru) dari riwayat
        $lastUrl = end($redirectHistory);

        // Ekstrak host baru dari URL tersebut
        $parsedUrl = parse_url($lastUrl);
        $newHost = ($parsedUrl['scheme'] ?? 'https') . '://' . $parsedUrl['host'];

        // Ganti host di data respons dengan host yang baru
        $responseData['host'] = $newHost;
        Log::info('Accurate host redirected and updated.', ['old_host' => session('accurate_database.host'), 'new_host' => $newHost]);
      }
      // --- AKHIR PERUBAHAN ---

      return $responseData;
    } catch (Exception $e) {
      Log::error('ACCURATE_ERROR - Gagal membuka database ID: ' . $dbId, ['error' => $e->getMessage()]);
      return null;
    }
  }

  public function getPurchaseOrders(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,number,transDate,vendor', // Data yang kita perlukan
        'sort'   => 'transDate desc'              // Urutkan dari yang terbaru
      ];

      // Terapkan filter tanggal jika diisi
      if ($request->filled('start_date') && $request->filled('end_date')) {
        $params['filter.transDate.op']    = 'RANGE';
        $params['filter.transDate.val[0]'] = $request->start_date;
        $params['filter.transDate.val[1]'] = $request->end_date;
      }

      // Terapkan filter pencarian jika diisi
      if ($request->filled('search')) {
        $params['filter.keywords.op']  = 'CONTAIN';
        $params['filter.keywords.val'] = $request->search;
      }

      $response = $this->dataClient()->get('/api/purchase-order/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mengambil daftar PO dari Accurate', $response->json());
        return collect([]);
      }

      return collect($response->json()['d'] ?? []);
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil daftar PO', ['message' => $e->getMessage()]);
      return collect([]);
    }
  }

  public function getPurchaseOrderDetail(int $poId)
  {
    try {
      // Endpoint untuk detail PO
      $response = $this->dataClient()->get('/api/purchase-order/detail.do', ['id' => $poId]);

      if ($response->failed()) {
        Log::error('Gagal mengambil detail PO dari Accurate', ['po_id' => $poId, 'response' => $response->json()]);
        return null;
      }

      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil detail PO', ['po_id' => $poId, 'message' => $e->getMessage()]);
      return null;
    }
  }


  // SALES ORDER ACCURATE API
  public function getSalesOrders(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,number,transDate,customer,totalAmount,status',
        'sort' => 'transDate desc',
        'sp.page' => $request->get('page', 1),
        'sp.pageSize' => 20,
      ];

      // Filter tanggal (opsional)
      if ($request->filled(['start_date', 'end_date'])) {
        $params['filter.transDate.op'] = 'BETWEEN';
        $params['filter.transDate.val[0]'] = $request->start_date;
        $params['filter.transDate.val[1]'] = $request->end_date;
      }

      // Filter pencarian (opsional)
      if ($request->filled('search')) {
        $params['filter.keywords.op'] = 'CONTAIN';
        $params['filter.keywords.val'] = $request->search;
      }

      // Request ke Accurate
      $response = $this->dataClient()->get('/api/sales-order/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mengambil daftar Sales Order dari Accurate', [
          'response' => $response->json()
        ]);
        return collect([]);
      }

      return collect($response->json()['d'] ?? []);
    } catch (\Throwable $e) {
      Log::error('Exception saat mengambil daftar Sales Order', [
        'message' => $e->getMessage()
      ]);
      return collect([]);
    }
  }
  public function getSalesOrderDetail(int $soId)
  {
    try {
      $response = $this->dataClient()->get('/api/sales-order/detail.do', ['id' => $soId]);

      if ($response->failed()) {
        Log::error('Gagal mengambil detail Sales Order dari Accurate', [
          'so_id' => $soId,
          'response' => $response->json(),
        ]);
        return null;
      }

      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil detail Sales Order', [
        'so_id' => $soId,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }
  public function getSalesOrderByNumber(string $soNumber)
  {
    try {
      // 1️⃣ Cari sales order berdasarkan nomor
      $params = [
        'fields' => 'id,number,transDate,customer,totalAmount,status',
        'filter.number' => 'EQUALS',
        'filter.number.val' => $soNumber,
      ];

      $response = $this->dataClient()->get('/api/sales-order/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mencari Sales Order berdasarkan nomor', [
          'so_number' => $soNumber,
          'response' => $response->json(),
        ]);
        return null;
      }

      $list = collect($response->json()['d'] ?? []);

      // 2️⃣ Pastikan ada hasil
      if ($list->isEmpty()) {
        Log::warning('Sales Order tidak ditemukan berdasarkan nomor', [
          'so_number' => $soNumber,
        ]);
        return null;
      }

      // 3️⃣ Ambil ID pertama yang ditemukan
      $soId = $list->first()['id'] ?? null;

      if (!$soId) {
        Log::warning('Sales Order ditemukan tapi tidak punya ID', [
          'so_number' => $soNumber,
          'data' => $list->first(),
        ]);
        return null;
      }

      // 4️⃣ Ambil detail berdasarkan ID
      return $this->getSalesOrderDetail((int) $soId);
    } catch (\Throwable $e) {
      Log::error('Exception saat mengambil Sales Order berdasarkan nomor', [
        'so_number' => $soNumber,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }



  // FINISHED GOOD ACCURATE API
  public function getFinishedGoodSlips(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,number,transDate,warehouse,totalQuantity,status,detailItem',
        'sort' => 'transDate desc',
        'sp.page' => $request->get('page', 1),
        'sp.pageSize' => 20,
      ];

      if ($request->filled(['start_date', 'end_date'])) {
        $params['filter.transDate.op'] = 'BETWEEN';
        $params['filter.transDate.val[0]'] = $request->start_date;
        $params['filter.transDate.val[1]'] = $request->end_date;
      }

      if ($request->filled('search')) {
        $params['filter.keywords.op'] = 'CONTAIN';
        $params['filter.keywords.val'] = $request->search;
      }

      $response = $this->dataClient()->get('/api/finished-good-slip/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal ambil daftar Finished Good Slip', $response->json());
        return collect([]);
      }

      return collect($response->json()['d'] ?? []);
    } catch (\Throwable $e) {
      Log::error('Exception saat ambil Finished Good Slip', ['message' => $e->getMessage()]);
      return collect([]);
    }
  }

  public function getFinishedGoodSlipDetail(int $id)
  {
    try {
      $response = $this->dataClient()->get('/api/finished-good-slip/detail.do', ['id' => $id]);

      if ($response->failed()) {
        Log::error('Gagal ambil detail Finished Good Slip', ['id' => $id, 'response' => $response->json()]);
        return null;
      }

      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception ambil detail Finished Good Slip', ['id' => $id, 'message' => $e->getMessage()]);
      return null;
    }
  }

  public function saveFinishedGoodSlip(array $data)
  {
    try {
      $response = $this->dataClient()->post('/api/finished-good-slip/save.do', $data);
      $result = $response->json();

      if ($response->failed()) {
        throw new Exception('HTTP Error ' . $response->status());
      }

      // Cek error dari Accurate
      if (is_array($result) && !isset($result['d']) && !isset($result['s'])) {
        throw new Exception(is_array($result) ? implode(', ', $result) : json_encode($result));
      }

      if (isset($result['s']) && $result['s'] === false) {
        $errorMsg = $result['m'] ?? (isset($result['d'])
          ? (is_array($result['d']) ? implode(', ', $result['d']) : $result['d'])
          : 'Unknown error');
        throw new Exception($errorMsg);
      }

      return $result['d'] ?? $result;
    } catch (\Exception $e) {
      throw new Exception('Accurate Error: ' . $e->getMessage());
    }
  }



  // WORK ORDER ACCURATE API
  public function getWorkOrders(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,number,transDate,item,quantity,warehouse,status,bom',
        'sort' => 'transDate desc',
        'sp.page' => $request->get('page', 1),
        'sp.pageSize' => 20,
      ];

      // Filter tanggal (opsional)
      if ($request->filled(['start_date', 'end_date'])) {
        $params['filter.transDate.op'] = 'BETWEEN';
        $params['filter.transDate.val[0]'] = $request->start_date;
        $params['filter.transDate.val[1]'] = $request->end_date;
      }

      // Filter pencarian (opsional)
      if ($request->filled('search')) {
        $params['filter.keywords.op'] = 'CONTAIN';
        $params['filter.keywords.val'] = $request->search;
      }

      // Request ke Accurate
      $response = $this->dataClient()->get('/api/work-order/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mengambil daftar Work Order dari Accurate', [
          'response' => $response->json()
        ]);
        return collect([]);
      }

      return collect($response->json()['d'] ?? []);
    } catch (\Throwable $e) {
      Log::error('Exception saat mengambil daftar Work Order', [
        'message' => $e->getMessage()
      ]);
      return collect([]);
    }
  }

  public function getWorkOrderDetail(int $id)
  {
    try {
      $response = $this->dataClient()->get('/api/work-order/detail.do', ['id' => $id]);

      if ($response->failed()) {
        Log::error('Gagal mengambil detail Work Order dari Accurate', [
          'id' => $id,
          'response' => $response->json(),
        ]);
        return null;
      }

      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil detail Work Order', [
        'id' => $id,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }

  public function getWorkOrderDetailByNumber(string $number)
  {
    try {
      // Cari work order berdasarkan nomor
      $params = [
        'fields' => 'id,number,transDate,item,quantity,warehouse,status,bom',
        'filter.number.op' => 'EQUAL',
        'filter.number.val' => $number,
      ];

      $response = $this->dataClient()->get('/api/work-order/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mencari Work Order berdasarkan nomor', [
          'wo_number' => $number,
          'response' => $response->json(),
        ]);
        return null;
      }

      $list = collect($response->json()['d'] ?? []);

      if ($list->isEmpty()) {
        Log::warning('Work Order tidak ditemukan', ['wo_number' => $number]);
        return null;
      }

      // Ambil yang exact match
      $exactMatch = $list->firstWhere('number', $number);

      if (!$exactMatch) {
        Log::warning('Work Order number tidak exact match', [
          'requested' => $number,
          'found' => $list->pluck('number')->toArray()
        ]);
        return null;
      }

      $woId = $exactMatch['id'] ?? null;
      if (!$woId) {
        Log::warning('Work Order tidak punya ID', ['wo_number' => $number]);
        return null;
      }

      // Ambil detail berdasarkan ID
      return $this->getWorkOrderDetail((int) $woId);
    } catch (\Throwable $e) {
      Log::error('Exception saat mengambil Work Order', [
        'wo_number' => $number,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }

  public function getCustomerDetail(int $id)
  {
    try {
      // Panggil endpoint Accurate untuk ambil detail customer
      $response = $this->dataClient()->get('/api/customer/detail.do', ['id' => $id]);

      // Jika gagal, log error dan kembalikan null
      if ($response->failed()) {
        Log::error('Gagal mengambil detail Customer dari Accurate', [
          'id' => $id,
          'response' => $response->json(),
        ]);
        return null;
      }

      // Ambil hasil dari response
      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil detail Customer', [
        'id' => $id,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }


  // MATERIAL SLIP ACCURATE API
  /**
   * Mengambil form Material Slip berdasarkan type
   * 
   * @param string $type - Type material slip (e.g., 'FINISHED_GOOD')
   * @return array|null
   */
  public function getMaterialSlipForm(string $type)
  {
    try {
      $response = $this->dataClient()->get('/api/material-slip/form.do', ['type' => $type]);

      if ($response->failed()) {
        Log::error('Gagal mengambil form Material Slip dari Accurate', [
          'type' => $type,
          'response' => $response->json(),
        ]);
        return null;
      }

      // Log full response untuk debugging
      Log::info('Material Slip Form Response', [
        'type' => $type,
        'full_response' => $response->json()
      ]);

      return $response->json()['d'] ?? $response->json();
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil form Material Slip', [
        'type' => $type,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }

  /**
   * Menyimpan Material Slip ke Accurate
   * 
   * @param array $data
   * @return array
   * @throws Exception
   */
  public function saveMaterialSlip(array $data)
  {
    try {
      $response = $this->dataClient()->post('/api/material-slip/save.do', $data);
      $result = $response->json();

      if ($response->failed()) {
        throw new Exception('HTTP Error ' . $response->status());
      }

      // Cek error dari Accurate
      if (is_array($result) && !isset($result['d']) && !isset($result['s'])) {
        throw new Exception(is_array($result) ? implode(', ', $result) : json_encode($result));
      }

      if (isset($result['s']) && $result['s'] === false) {
        $errorMsg = $result['m'] ?? (isset($result['d'])
          ? (is_array($result['d']) ? implode(', ', $result['d']) : $result['d'])
          : 'Unknown error');
        throw new Exception($errorMsg);
      }

      return $result;
    } catch (\Exception $e) {
      throw new Exception('Accurate Error: ' . $e->getMessage());
    }
  }



  // SHIPMENT
  public function findOrCreateShipment(string $name)
  {
    try {
      // 1. Check apakah shipment sudah ada
      $list = $this->dataClient()->get('/api/shipment/list.do', [
        'filter.name' => 'EQUAL',
        'filter.name.val' => $name
      ])->json();

      if (!empty($list['d']['data'])) {
        Log::info("Shipment '$name' sudah ada, reuse.");
        return $name;
      }

      // 2. Jika tidak ada → create baru
      $result = $this->saveShipment(['name' => $name]);
      Log::info("Shipment '$name' berhasil dibuat.", $result);

      return $name;
    } catch (\Throwable $e) {

      // 3. Jika error karena duplikat → abaikan, tetap pakai nama itu
      if (str_contains($e->getMessage(), 'Sudah ada data lain dengan Nama')) {
        Log::warning("Shipment '$name' duplicate error, tapi aman → gunakan existing.");
        return $name;
      }

      // 4. Error lain tetap dilempar
      throw $e;
    }
  }

  public function saveShipment(array $data)
  {
    try {
      Log::info('Menyimpan Shipment ke Accurate', [
        'data' => $data
      ]);

      $response = $this->dataClient()->post('/api/shipment/save.do', $data);
      $result = $response->json();

      Log::info('Response Shipment Accurate', $result);

      if ($response->failed()) {
        throw new Exception('Gagal menyimpan shipment: ' . json_encode($result));
      }

      // Jika Accurate s = false
      if (isset($result['s']) && $result['s'] === false) {
        $msg = $result['m']
          ?? (isset($result['d']) ? implode(', ', (array)$result['d']) : 'Unknown error');

        throw new Exception("Gagal menyimpan shipment: $msg");
      }

      // Transaksi returned in "r"
      return $result['r'] ?? $result;
    } catch (\Throwable $e) {
      Log::error('Error save shipment', [
        'message' => $e->getMessage(),
        'data' => $data
      ]);

      throw $e;
    }
  }





  // DELIVERY ORDERS
  // DELIVERY ORDER ACCURATE API
  public function saveDeliveryOrder(array $data)
  {
    try {
      // Log request yang akan dikirim
      Log::info('=== START: Menyimpan Delivery Order ke Accurate ===', [
        'timestamp' => now()->toDateTimeString(),
        'request_data' => $data
      ]);

      $response = $this->dataClient()->post('/api/delivery-order/save.do', $data);

      // Log response dari Accurate
      Log::info('Response dari Accurate API', [
        'status_code' => $response->status(),
        'response_body' => $response->json(),
        'is_successful' => $response->successful()
      ]);

      if ($response->failed()) {
        $responseData = $response->json();

        Log::error('❌ GAGAL: Menyimpan Delivery Order ke Accurate', [
          'timestamp' => now()->toDateTimeString(),
          'status_code' => $response->status(),
          'error_message' => $responseData['s']['m'] ?? $responseData['m'] ?? 'Error tidak diketahui',
          'full_response' => $responseData,
          'request_data' => $data
        ]);

        $errorMessage = $responseData['s']['m'] ?? $responseData['m'] ?? 'Error tidak diketahui';
        throw new Exception('Gagal menyimpan Delivery Order: ' . $errorMessage);
      }

      $result = $response->json()['d'] ?? null;

      // Log sukses dengan detail hasil
      Log::info('✅ BERHASIL: Delivery Order tersimpan ke Accurate', [
        'timestamp' => now()->toDateTimeString(),
        'do_number' => $result['number'] ?? 'N/A',
        'do_id' => $result['id'] ?? 'N/A',
        'customer' => $data['customer']['name'] ?? 'N/A',
        'driver_name' => $data['driverName'] ?? 'N/A',
        'delivery_date' => $data['transDate'] ?? 'N/A',
        'total_items' => count($data['detailItem'] ?? []),
        'result_data' => $result
      ]);

      Log::info('=== END: Proses Delivery Order Selesai ===');

      return $result;
    } catch (\Exception $e) {
      Log::error('❌ EXCEPTION: Error saat menyimpan Delivery Order', [
        'timestamp' => now()->toDateTimeString(),
        'error_message' => $e->getMessage(),
        'error_trace' => $e->getTraceAsString(),
        'request_data' => $data,
        'line' => $e->getLine(),
        'file' => $e->getFile()
      ]);

      throw $e;
    }
  }


  // RAW MATERIAL (ITEM) ACCURATE API
  /**
   * Mengambil daftar item/raw material dari Accurate
   * 
   * @param Request $request
   * @return Collection
   */
  public function getRawMaterials(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,no,name,itemType,unitPrice,stock,unit',
        'sort' => 'name asc',
        'sp.page' => $request->get('page', 1),
        'sp.pageSize' => $request->get('pageSize', 1000),
      ];

      // Filter berdasarkan itemType (INVENTORY untuk raw material/persediaan)
      if ($request->filled('item_type')) {
        $params['filter.itemType.op'] = 'EQUAL';
        $params['filter.itemType.val'] = $request->item_type;
      } else {
        // Default filter untuk INVENTORY (persediaan/raw material)
        $params['filter.itemType.op'] = 'EQUAL';
        $params['filter.itemType.val'] = 'INVENTORY';
      }

      // Filter pencarian (opsional)
      if ($request->filled('search')) {
        $params['filter.keywords.op'] = 'CONTAIN';
        $params['filter.keywords.val'] = $request->search;
      }

      // Request ke Accurate
      $response = $this->dataClient()->get('/api/item/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mengambil daftar Raw Material dari Accurate', [
          'response' => $response->json()
        ]);
        return collect([]);
      }

      return collect($response->json()['d'] ?? []);
    } catch (\Throwable $e) {
      Log::error('Exception saat mengambil daftar Raw Material', [
        'message' => $e->getMessage()
      ]);
      return collect([]);
    }
  }


  // BILL OF MATERIAL (BOM) ACCURATE API
  /**
   * Mengambil daftar Bill of Material dari Accurate
   * 
   * @param Request $request
   * @return Collection
   */
  public function getBillOfMaterials(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,no,name,item,quantity,unit,status',
        'sort' => 'name asc',
        'sp.page' => $request->get('page', 1),
        'sp.pageSize' => $request->get('pageSize', 20),
      ];

      // Filter pencarian (opsional)
      if ($request->filled('search')) {
        $params['filter.keywords.op'] = 'CONTAIN';
        $params['filter.keywords.val'] = $request->search;
      }

      // Request ke Accurate
      $response = $this->dataClient()->get('/api/bom/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mengambil daftar Bill of Material dari Accurate', [
          'response' => $response->json()
        ]);
        return collect([]);
      }

      return collect($response->json()['d'] ?? []);
    } catch (\Throwable $e) {
      Log::error('Exception saat mengambil daftar Bill of Material', [
        'message' => $e->getMessage()
      ]);
      return collect([]);
    }
  }

  /**
   * Mengambil detail Bill of Material berdasarkan ID
   * 
   * @param int $id
   * @return array|null
   */
  public function getBillOfMaterialDetail(int $id)
  {
    try {
      $response = $this->dataClient()->get('/api/bill-of-material/detail.do', ['id' => $id]);

      if ($response->failed()) {
        Log::error('Gagal mengambil detail Bill of Material dari Accurate', [
          'id' => $id,
          'response' => $response->json(),
        ]);
        return null;
      }

      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception saat mengambil detail Bill of Material', [
        'id' => $id,
        'message' => $e->getMessage(),
      ]);
      return null;
    }
  }
}
