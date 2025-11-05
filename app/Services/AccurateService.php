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


  public function getFinishedGoodSlips(Request $request)
  {
    try {
      $params = [
        'fields' => 'id,number,transDate,warehouse,totalQuantity,status',
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
      $response = $this->dataClient()->asForm()->post('/api/finished-good-slip/save.do', $data);

      if ($response->failed()) {
        Log::error('Gagal menyimpan Finished Good Slip', [
          'data' => $data,
          'response' => $response->json()
        ]);
        throw new Exception('Gagal menyimpan Finished Good Slip.');
      }

      return $response->json()['d'] ?? null;
    } catch (\Exception $e) {
      Log::error('Exception saat menyimpan Finished Good Slip', [
        'message' => $e->getMessage()
      ]);
      throw $e;
    }
  }


  public function deleteFinishedGoodSlip(int $id)
  {
    try {
      $response = $this->dataClient()->post('/api/finished-good-slip/delete.do', ['id' => $id]);

      if ($response->failed()) {
        Log::error('Gagal menghapus Finished Good Slip', [
          'id' => $id,
          'response' => $response->json()
        ]);
        throw new Exception('Gagal menghapus Finished Good Slip.');
      }

      return $response->json()['d'] ?? true;
    } catch (\Exception $e) {
      Log::error('Exception saat hapus Finished Good Slip', [
        'id' => $id,
        'message' => $e->getMessage()
      ]);
      throw $e;
    }
  }


  /**
   * Cari Finished Good Slip berdasarkan itemNo
   */
  public function findFinishedGoodSlipByItemNo(string $itemNo)
  {
    try {
      $params = [
        'fields' => 'id,number,transDate,warehouse,status,detailItem',
        'filter.detailItem.itemNo.op' => 'EQUAL',
        'filter.detailItem.itemNo.val' => $itemNo,
        'sort' => 'transDate desc',
        'sp.pageSize' => 1 // ambil hanya 1 yang terbaru
      ];

      $response = $this->dataClient()->get('/api/finished-good-slip/list.do', $params);

      if ($response->failed()) {
        Log::error('Gagal mencari Finished Good Slip berdasarkan itemNo', [
          'itemNo' => $itemNo,
          'response' => $response->json()
        ]);
        return null;
      }

      $data = $response->json()['d'] ?? [];
      if (empty($data)) {
        return null;
      }

      // Ambil yang pertama (hasil paling baru)
      return $data[0];
    } catch (\Exception $e) {
      Log::error('Exception saat mencari Finished Good Slip by itemNo', [
        'itemNo' => $itemNo,
        'message' => $e->getMessage()
      ]);
      return null;
    }
  }
}
