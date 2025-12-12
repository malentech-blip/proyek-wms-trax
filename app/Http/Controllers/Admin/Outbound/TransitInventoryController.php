<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\OutboundPackingItem;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use App\Models\Admin\Outbound\TransitInventory;
use App\Services\AccurateService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\FacadesDB;

class TransitInventoryController extends Controller
{
  public function index()
  {
    $transitInventories = TransitInventory::with("packingList.sales_order")->whereHas("packingList", function ($query) {
      $query->whereIn("status", ["In Transit", "Ready to Ship", "Shipped"]);
    })->orderBy("created_at", "desc")->paginate(20);
    return view('admin.outbound.transit-inventory.index', compact('transitInventories'));
  }

  public function detail(int $packing_id, AccurateService $accurate)
  {
    $transitInventory = TransitInventory::with("packingList.sales_order")->where([
      ['packing_id', $packing_id],
    ])->first();
    $customer = $accurate->getCustomerDetail($transitInventory->packingList->sales_order->customer_id);
    return view('admin.outbound.transit-inventory.detail', compact('transitInventory', 'customer'));
  }

  public function validateBarcode(int $packing_id, Request $request, AccurateService $accurate)
  {
    $validated = $request->validate([
      'qr_code' => 'required|string',
    ]);
    try {
      $tiQuery = TransitInventory::with(['packingList.sales_order']);
      $transitInventory = $tiQuery->whereHas('packingList', function ($query) use ($validated) {
        $query->where('barcode', $validated['qr_code']);
      })->first();
      if (!$transitInventory) {
        return response()->json([
          'success' => false,
          'message' => 'Data transit inventory dengan barcode tersebut tidak ditemukan atau tidak dalam status In Transit.'
        ], 404);
      }

      $packingList = $transitInventory->packingList;
      $so = $packingList->sales_order;
      if (!$so) {
        return response()->json([
          'success' => false,
          'message' => 'Sales order tidak ditemukan untuk packing list ini.'
        ], 404);
      }
      try {
        $customer = $accurate->getCustomerDetail($so->customer_id);
        if (!$customer || !isset($customer['name'])) {
          throw new \Exception('Data customer tidak lengkap');
        }
      } catch (\Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Gagal mengambil data customer dari Accurate: ' . $e->getMessage()
        ], 500);
      }
      return response()->json([
        'success' => true,
        'data' => [
          'so_number' => $so->so_number,
          'customer_name' => $customer['name'],
          'total_items' => $packingList->items->count(),
          'packed_date' => $packingList->packed_at,
          'status' => $packingList->status
        ]
      ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Validasi gagal',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memvalidasi barcode.',
        'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
      ], 500);
    }
  }

  public function validateBarcodes(Request $request, AccurateService $accurate)
  {
    try {
      $validated = $request->validate([
        'barcode' => 'required|string',
      ]);
      $transitInventory = TransitInventory::with("packingList.sales_order")->whereHas('packingList', function ($query) use ($validated) {
        $query->where('barcode', $validated['barcode'])
          ->whereIn('status', ['In Transit', 'Ready to Ship']);
      })->first();

      if (!$transitInventory) {
        return response()->json([
          'success' => false,
          'message' => 'Data transit inventory dengan barcode tersebut tidak ditemukan atau tidak dalam status In Transit.'
        ], 404);
      }

      $packingList = $transitInventory->packingList;
      $so = $packingList->sales_order;
      if (!$so) {
        return response()->json([
          'success' => false,
          'message' => 'Sales order tidak ditemukan untuk packing list ini.'
        ], 404);
      }
      try {
        $customer = $accurate->getCustomerDetail($so->customer_id);
        if (!$customer || !isset($customer['name'])) {
          throw new \Exception('Data customer tidak lengkap');
        }
      } catch (\Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Gagal mengambil data customer dari Accurate: ' . $e->getMessage()
        ], 500);
      }
      return response()->json([
        'success' => true,
        'data' => [
          'so_number' => $so->so_number,
          'transit_id' => $transitInventory->id,
          'customer_name' => $customer['name'],
          'total_items' => $packingList->items->count(),
          'packed_date' => $packingList->packed_at,
          'barcode' => $validated['barcode'],
          'status' => $packingList->status
        ]
      ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Validasi gagal',
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memvalidasi barcode.',
        'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
      ], 500);
    }
  }

  public function updateStatus(int $transit_id, Request $request)
  {
    $validated = $request->validate([
      'status' => 'required|string'
    ]);
    $transitInventory = TransitInventory::find($transit_id);
    if (!$transitInventory) {
      return response()->json([
        'success' => false,
        'message' => 'Data transit inventory tidak ditemukan.'
      ], 404);
    }
    $packingList = PackingList::where("id", $transitInventory->packing_id)->first();
    if (!$packingList) {
      return response()->json([
        'success' => false,
        'message' => 'Data packing list tidak ditemukan.'
      ], 404);
    }
    $packingList->status = $validated['status'];
    $packingList->save();
    return response()->json([
      'success' => true,
      'message' => 'Status transit inventory berhasil diubah menjadi Delivered.'
    ], 200);
  }


  public function createDeliveryOrderFromTransit(
    Request $request,
    int $packing_id,
    AccurateService $accurate
  ) {
    try {
      // ===== 1. Validasi =====
      $validated = $request->validate([
        'delivery_date' => 'required|date',
        'driver_name'   => 'required|string|max:255',
      ]);

      // ===== 2. Ambil Packing List =====
      $packingList = PackingList::with(['sales_order'])->findOrFail($packing_id);

      if ($packingList->status !== 'Ready to Ship') {
        return response()->json([
          'success' => false,
          'message' => 'Packing List harus dalam status "Ready to Ship" untuk membuat Delivery Order.'
        ], 422);
      }

      // Cek apakah DO sudah pernah dibuat
      $existingDO = DeliveryOrder::where('packing_id', $packingList->id)->first();
      if ($existingDO) {
        return response()->json([
          'success' => false,
          'message' => 'Delivery Order sudah pernah dibuat untuk Packing List ini.'
        ], 422);
      }

      DB::beginTransaction();

      try {
        // ===== 3. Ambil Data Customer Accurate =====
        $customerId = $packingList->sales_order->customer_id;
        $customer   = $accurate->getCustomerDetail($customerId);
        $customerNo = $customer['customerNo'];

        // ===== 4. Ambil Data Sales Order Accurate =====
        $soNumber   = $packingList->sales_order->so_number;
        $salesOrder = $soNumber ? $accurate->getSalesOrderByNumber($soNumber) : null;
        $branchId   = $salesOrder['branchId'] ?? null;
        $items      = $salesOrder['detailItem'] ?? [];

        // ===== 5. Buat Delivery Order di Database Lokal =====
        $deliveryOrder = DeliveryOrder::create([
          'packing_id'    => $packing_id,
          'delivery_date' => $validated['delivery_date'],
          'driver_name'   => $validated['driver_name'],
          'status'        => 'In Delivery',
        ]);

        // ===== 6. Create / Find Shipment =====
        $shipmentName = $accurate->findOrCreateShipment($validated['driver_name']);

        // ===== 7. Payload untuk Accurate =====
        $payload = [
          "customerNo"   => $customerNo,
          "number"       => $deliveryOrder->delivered_no,
          "salesOrderNo" => $soNumber,
          "transDate"    => Carbon::parse($validated['delivery_date'])->format('d/m/Y'),
          "branchId"     => $branchId,
          "shipmentName" => $shipmentName,
          "detailItem"   => collect($items)->map(function ($item) {
            return [
              "itemNo"        => $item["item"]["no"],
              "itemUnitName"  => $item["item"]["unitName"]["name"] ?? "PCS",
              "quantity"      => $item["quantity"] ?? 0,
              "warehouseName" => "Utama",
            ];
          })->values()->toArray(),
        ];

        // ===== 8. Simpan DO ke Accurate =====
        $accurate->saveDeliveryOrder($payload);

        // ===== 9. Update status PackingList =====
        $packingList->update(['status' => 'Shipped']);

        // ===== 10. Update status LocalSalesOrder (SO) =====
        SalesOrder::where("so_number", $soNumber)
          ->update(['status' => 'Shipped']);

        // ===== 11. Update Transit Inventory =====
        $transitInventory = TransitInventory::where('packing_id', $packingList->id)->first();
        if ($transitInventory) {
          $transitInventory->update([
            'transit_out_at' => now(),
          ]);
        }

        DB::commit();

        // ===== 12. Response =====
        return response()->json([
          'success'    => true,
          'message'    => 'Delivery Order berhasil dibuat',
          'do_number'  => $deliveryOrder->delivered_no,
          'data' => [
            'id'              => $deliveryOrder->id,
            'do_number'       => $deliveryOrder->delivered_no,
            'packing_list_id' => $packingList->id,
            'so_number'       => $soNumber ?? 'N/A',
            'driver_name'     => $validated['driver_name'],
            'delivery_date'   => $validated['delivery_date'],
          ]
        ], 201);
      } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
      }
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'success'  => false,
        'message'  => 'Validasi gagal',
        'errors'   => $e->errors()
      ], 422);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Packing List tidak ditemukan.'
      ], 404);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat membuat Delivery Order.',
        'error'   => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
      ], 500);
    }
  }
}
