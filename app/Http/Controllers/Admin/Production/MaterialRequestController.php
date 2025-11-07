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
    $salesOrders = SalesOrder::where("status", 'Pending')->get();
    $soNumber = $request->query('so_id');
    $selectedSalesOrderDetail = null;

    $rawItems = Item::where("item_type", "Raw Material")->get();
    return view("admin.production.material-request.index", compact("salesOrders", "selectedSalesOrderDetail", "rawItems"));
  }

  public function detail(int $mr_id, AccurateService $accurate)
  {
    $mr = MaterialRequest::with(['salesOrder'])->where("id", $mr_id)->first();
    if (!$mr) {
      return redirect()->route("admin.production.material-request.index");
    }
    $so = $accurate->getSalesOrderDetail($mr->so_id);
    $wip = WipRecord::where("mr_id", $mr_id)->first() ?? null;
    return view("admin.production.material-request.detail", compact("mr", "so", "wip"));
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

  public function addTempItem(Request $request)
  {
    $validated = $request->validate([
      'so_id' => 'required|string',
      'item_id' => 'required|integer',
      'quantity' => 'required|integer|min:1',
      'picked_by' => 'required|string',
    ]);

    $item = Item::where("id", $validated['item_id'])->first();
    $inventory = Inventory::where("item_id", $validated['item_id'])->first();
    $location = Location::where("id", $inventory->location_id)->first();

    $mockItemData = [
      'so_id' => $validated['so_id'],
      'item_id' => $validated['item_id'],
      'quantity' => $validated['quantity'],
      'picked_by' => $validated['picked_by'],
      'item_name' => $item->item_name,
      'quantity_ready' => $inventory->quantity,
      'location' => $location->name,
    ];
    return response()->json([
      'success' => true,
      'item' => $mockItemData
    ]);
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

  public function storeMR(Request $request)
  {
    // Karena kita mengirim JSON, gunakan $request->validate() pada array yang diterima
    $data = $request->validate([
      "so_id" => ["required"],
      "requested_by" => ["required", "string"],
      "request_date" => ["required", "date"],
      "items" => ["required", "array", "min:1"],
      "items.*.item_id" => ["required", "integer"],
      "items.*.quantity" => ["required", "integer"],
      "items.*.picked_by" => ["required", "string"],
    ]);

    // Ambil data form utama
    $soId = $data['so_id'];
    $requestedBy = $data['requested_by'];
    $requestDate = $data['request_date'];
    $items = $data['items'];

    DB::beginTransaction();

    try {
      // 1. Buat Header Material Request (MR)
      $mr = MaterialRequest::create([
        "so_id" => $soId,
        "requested_by" => $requestedBy,
        "request_date" => $requestDate,
        "status" => "Requested"
      ]);

      foreach ($items as $item) {
        PickingList::create([
          "mr_id" => $mr->id,
          "item_id" => $item["item_id"],
          "quantity" => $item["quantity"],
          "picked_by" => $item["picked_by"],
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
