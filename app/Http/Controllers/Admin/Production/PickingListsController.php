<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\ItemLabel;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\PickingList;
use App\Models\Admin\Production\WipRecord;
use App\Services\AccurateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PickingListsController extends Controller
{
  public function listPL(Request $request)
  {
    $query = PickingList::query()
      ->join('items', 'items.id', '=', 'picking_lists.item_id')
      ->join("material_requests", "material_requests.id", "=", "picking_lists.mr_id")
      ->leftJoin('item_labels', 'item_labels.item_code', '=', 'items.item_code')
      ->leftJoin('inventories', 'inventories.item_id', '=', 'items.id')
      ->leftJoin('locations', 'locations.id', '=', 'inventories.location_id')
      ->leftJoin('racks', 'racks.id', '=', 'item_labels.rack_id')
      ->select(
        'picking_lists.*',
        'items.item_name as item_name',
        'racks.code as rack_code',
        'inventories.quantity as item_quantity',
        'locations.name as location_name',
        'material_requests.status as status_mr'
      )
      ->whereNot("material_requests.status", "Completed");

    if ($request->filled('date_picked')) {
      $query->whereDate('picking_lists.date_picked', $request->date_picked);
    }

    if ($request->filled('search')) {
      $searchTerm = '%' . $request->search . '%';

      $query->where(function ($q) use ($searchTerm) {
        $q->where('items.item_name', 'like', $searchTerm)
          ->orWhere('locations.name', 'like', $searchTerm);
      });
    }

    $pickingList = $query
      ->orderBy('picking_lists.created_at')
      ->paginate(10)
      ->withQueryString();

    return view("admin.production.picking-list.index", compact("pickingList"));
  }

  public function detail(int $mr_id)
  {
    $mr = MaterialRequest::where("id", $mr_id)->first();
    if (!$mr) {
      return redirect()->route("admin.production.material-request.index");
    }
    $query = PickingList::query()
      ->join('items', 'items.id', '=', 'picking_lists.item_id')
      ->join("material_requests", "material_requests.id", "=", "picking_lists.mr_id")
      ->leftJoin('item_labels', 'item_labels.item_code', '=', 'items.item_code')
      ->leftJoin('inventories', 'inventories.item_id', '=', 'items.id')
      ->leftJoin('locations', 'locations.id', '=', 'inventories.location_id')
      ->leftJoin('racks', 'racks.id', '=', 'item_labels.rack_id')
      ->select(
        'picking_lists.*',
        'items.item_name as item_name',
        'items.item_code as item_code',
        'racks.code as rack_code',
        'inventories.quantity as item_quantity',
        'locations.name as location_name',
        'material_requests.status as status_mr'
      );

    $pickingList = $query
      ->where('mr_id', $mr_id)
      ->get();
    $mrId = $mr_id;
    $wip = WipRecord::where("mr_id", $mr_id)->first() ?? null;
    return view("admin.production.picking-list.detail", compact('pickingList', 'mr', 'mrId', 'wip'));
  }

  public function scanItem(Request $request)
  {
    try {
      $itemLabel = ItemLabel::where("qr_code", $request["qr_code"])->first();
      $itemPickingList = PickingList::query()
        ->join('items', 'items.id', '=', 'picking_lists.item_id')
        ->leftJoin('item_labels', 'item_labels.item_code', '=', 'items.item_code')
        ->select(
          'picking_lists.*',
          'items.item_name as item_name',
          'items.item_code as item_code',
          'item_labels.qr_code as qr_code',
        )
        ->where("date_picked", null)
        ->where("mr_id", $request["mr_id"])
        ->first();

      // Jika tidak ditemukan
      if ($itemLabel->qr_code !== $itemPickingList->qr_code) {
        return response()->json([
          'success' => false,
          'message' => '❌ QR Code tidak ditemukan dalam daftar picking list.',
          'data' => $itemPickingList
        ], 404);
      }
      return response()->json([
        'success' => true,
        'message' => '✅ Item berhasil diverifikasi.',
        'data' => $itemPickingList
      ]);
    } catch (\Illuminate\Database\QueryException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Kesalahan query database: ' . $e->getMessage(),
      ], 500);
    } catch (\Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Scan QR code tidak valid',
      ], 500);
    }
  }





  public function confirmPick(Request $request, AccurateService $accurate)
  {
    DB::beginTransaction();
    try {
      $picking = PickingList::with(['materialRequest', 'item'])->find($request->picking_id);
      if (!$picking) {
        return response()->json([
          'success' => false,
          'message' => 'Data Picking List tidak ditemukan.',
        ], 404);
      }


      try {
        $woDetail = $accurate->getWorkOrderDetailByNumber($picking->materialRequest->wo_no);
        if (!$woDetail) {
          throw new \Exception("Work Order {$picking->materialRequest->wo_no} tidak ditemukan di Accurate");
        }
        $branchId = $woDetail['branch']['id'];
        $materialSlipData = [
          "workOrderNumber" => $picking->materialRequest->wo_no,
          "transDate" => now()->format('d/m/Y'),
          "memo" => "Pemakaian bahan",
          "materialSlipType" => "ITEM_PICK",
          "branchId" => $branchId,
          "detailItem" => [
            [
              "itemNo" => $picking->item->item_code,
              "quantity" => (float) $picking->quantity,
              "unit" => $picking->item->uom ?? "PCS",
              "detailSerialNumber" => [
                "transDate" => now()->format('d/m/Y'),
                "workOrderNumber" => $picking->materialRequest->wo_no,
              ]
            ]
          ]
        ];


        $result = $accurate->saveMaterialSlip($materialSlipData);
        $picking->update([
          'date_picked' => now(),
        ]);
        DB::commit();

        return response()->json([
          'success' => true,
          'message' => 'Item berhasil dikonfirmasi dan Material Slip dikirim ke Accurate.',
          'accurate_result' => $result
        ]);
      } catch (\Exception $accurateError) {
        // Jika Accurate gagal, tetap commit perubahan lokal tapi beri warning
        DB::commit();

        return response()->json([
          'success' => true,
          'message' => 'Item berhasil dikonfirmasi, tetapi gagal mengirim ke Accurate: ' . $accurateError->getMessage(),
          'warning' => true,
        ]);
      }
    } catch (\Throwable $e) {
      DB::rollBack();
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
      ], 500);
    }
  }
}
