<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\PickingList;
use App\Models\Admin\Production\WipRecord;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Outbound\SalesOrder;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Services\AccurateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialRequestController extends Controller
{
  public function index(Request $request, AccurateService $accurate)
  {
    $workOrders = $accurate->getWorkOrders($request);
    $materialRequests = MaterialRequest::all();
    $filteredWorkOrders = $workOrders->filter(function ($wo) use ($materialRequests) {
      return !$materialRequests->contains('wo_no', $wo['number']);
    });
    $items = [];
    $selectedWorkOrderDetail = null;

    if($request->has('wo_no') && $request->query('wo_no') != null){
      $selectedWorkOrderDetail = $accurate->getWorkOrderDetailByNumber($request->query('wo_no'));
      if(isset($selectedWorkOrderDetail['billOfMaterialId'])){
        $bom = $accurate->getBillOfMaterialDetail($selectedWorkOrderDetail['billOfMaterialId']);
        $items = $bom['detailMaterial'] ?? [];
      }
    }
    return view("admin.production.material-request.index", [
      "workOrders" => $filteredWorkOrders,
      "items" => $items,
      "fgQuantity" => $selectedWorkOrderDetail["quantity"] ?? 1
    ]);
  }

  public function detail(int $mr_id, AccurateService $accurate)
  {
    $mr = MaterialRequest::with(['pickingList.item'])->where("id", $mr_id)->first();
    if (!$mr) {
      return redirect()->route("admin.production.material-request.index");
    }

    $wo = null;
    if ($mr->wo_no) {
      $wo = $accurate->getWorkOrderDetailByNumber($mr->wo_no);
    }
    
    $wip = WipRecord::where("mr_id", $mr_id)->first() ?? null;    
    return view("admin.production.material-request.detail", compact("mr", "wo", "wip"));
  }

  public function changeStatus(Request $request)
  {
    $request->validate([
      'mr_id' => 'required|integer',
    ]);
    try {
      MaterialRequest::where("id", $request->mr_id)->update([
        'status' => 'Picked',
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Material Request berhasil dikonfirmasi sebagai Picked.',
      ]);
    } catch (\Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
      ], 500);
    }
  }



  public function listMR(Request $request, AccurateService $accurate)
  {
    $materialRequestsQuery = MaterialRequest::query();
    if ($request->has('search') && $request->get('search') != null) {
      $materialRequestsQuery = $materialRequestsQuery->where("mr_no", "like", "%" . $request->search . "%");
    }
    if ($request->has('request_date') && $request->get('request_date') != null) {
      $materialRequestsQuery = $materialRequestsQuery->whereDate("request_date", $request->request_date);
    }
    if ($request->has("status") && $request->get("status") != null) {
      $materialRequestsQuery = $materialRequestsQuery->where("status", $request->status);
    }
    $materialRequests = $materialRequestsQuery->with([
      'pickingList.item',
      'pickingList'
    ])->whereNot("status", "Completed")->orderBy('created_at', 'desc')->paginate(10);
    // dd($materialRequests);
    return view("admin.production.material-request.list-material-request", compact("materialRequests"));
  }

  public function storeMR(Request $request, AccurateService $accurate)
  {
    $data = $request->validate([
      "wo_no" => ["required"],
      "requested_by" => ["required", "string"],
      "request_date" => ["required", "date"],
      "picked_by" => ["required", "string"],
    ]);

    $woNo = $data['wo_no'];
    $requestedBy = $data['requested_by'];
    $requestDate = $data['request_date'];
    $pickedBy = $data['picked_by'];

    DB::beginTransaction();

    try {
      // 1. Ambil detail Work Order dari Accurate berdasarkan number
      $woDetail = $accurate->getWorkOrderDetailByNumber($woNo);
      
      if (!isset($woDetail['billOfMaterialId'])) {
        return response()->json([
          "success" => false,
          "message" => "Work Order tidak memiliki Bill of Material."
        ], 400);
      }

      // 2. Ambil BOM detail untuk mendapatkan items
      $bom = $accurate->getBillOfMaterialDetail($woDetail['billOfMaterialId']);
      $bomItems = $bom['detailMaterial'] ?? [];

      if (empty($bomItems)) {
        return response()->json([
          "success" => false,
          "message" => "Bill of Material tidak memiliki item."
        ], 400);
      }
      $mr = MaterialRequest::create([
        "wo_no" => $data["wo_no"], 
        "requested_by" => $requestedBy,
        "request_date" => $requestDate,
        "status" => "Requested"
      ]);

      // 4. Buat Picking List untuk setiap item dari BOM
      foreach ($bomItems as $bomItem) {
        $itemNo = $bomItem['item']['no'] ?? null;
        
        if (!$itemNo) {
          Log::warning("Item No tidak ditemukan pada BOM item", ['bom_item' => $bomItem]);
          continue;
        }

        // 5. Cari item_id berdasarkan item_code (no) di tabel items
        $item = Item::where('item_code', $itemNo)->first();
        
        if (!$item) {
          Log::warning("Item dengan code {$itemNo} tidak ditemukan di database");
          continue;
        }

        PickingList::create([
          "mr_id" => $mr->id,
          "item_id" => $item->id,
          "quantity" => ($bomItem['quantity'] * $woDetail["quantity"]) ?? 0,
          "picked_by" => $pickedBy,
        ]);
      }

      DB::commit();
      return response()->json([
        "success" => true,
        "message" => "Material Request (MR) dan Picking List berhasil dibuat.",
        "mr_id" => $mr->id
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error("MR Creation Failed: " . $e->getMessage());
      return response()->json([
        "success" => false,
        "message" => "Gagal membuat MR/PL. Terjadi kesalahan server.",
        "error_details" => $e->getMessage()
      ], 500);
    }
  }

  public function complete(int $mr_id)
  {
    $materialRequest = MaterialRequest::where("id", $mr_id)->first();
    if (!$materialRequest) {
      return response()->json([
        'status'  => 'error',
        'message' => 'Material request tidak ditemukan.',
      ], 400);
    }
    $materialRequest->update([
      "status" => "Completed"
    ]);

    return response()->json([
      'status' => 'success',
      'message' => 'Production is completed.',
    ], 200);
  }
}
