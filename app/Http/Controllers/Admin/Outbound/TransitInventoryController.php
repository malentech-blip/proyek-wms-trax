<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\OutboundPackingItem;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\TransitInventory;
use App\Services\AccurateService;
use Illuminate\Http\Request;

class TransitInventoryController extends Controller
{
  public function index()
  {
    $deliveryOrders = DeliveryOrder::with(['packingList.sales_order'])->where('status', 'In Transit')->get();
    return view('admin.outbound.transit-inventory.index', compact('deliveryOrders'));
  }

  public function detail(int $packing_id)
  {
    $packingList = PackingList::where([
      ['id', $packing_id],
    ])->with(['sales_order', 'items'])->first();
    return view('admin.outbound.transit-inventory.detail', compact('packingList'));
  }

  public function validateBarcode(int $packing_id, AccurateService $accurate)
  {
    try {
      $tiQuery = TransitInventory::with(['packingList.sales_order']);
      $transitInventory = $tiQuery->where('packing_id', $packing_id)
                                  ->where('status', 'In Transit')->first();
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
      $tiQuery = DeliveryOrder::with(['packingList.sales_order'])->where("status", "In Transit");
      $transitInventories = $tiQuery->whereHas('packingList', function ($query) use ($validated) {
        $query->where('barcode', $validated['barcode'])
          ->where('status', 'Shipped');
      })->first();

      if (!$transitInventories) {
        return response()->json([
          'success' => false,
          'message' => 'Data transit inventory dengan barcode tersebut tidak ditemukan atau tidak dalam status In Transit.'
        ], 404);
      }

      $packingList = $transitInventories->packingList;
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
}
