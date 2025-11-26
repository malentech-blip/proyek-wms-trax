<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\WipRecord;
use App\Models\Admin\Production\ManufactureCost;
use App\Models\Admin\Production\RejectProduction;
use App\Models\SuperAdmin\MasterData\Item;
use App\Services\AccurateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WIPController extends Controller
{
  public function index(Request $request)
  {
    $materialRequests = MaterialRequest::where("status", "Picked")->get();
    $wipQuery = WipRecord::query();
    
    if($request->has('start_date') && $request->get('start_date') !== null) {
      $wipQuery = $wipQuery->where('started_at', '>=', $request->start_date);
    }
    if($request->has('status') && $request->get('status') !== null) {
      $wipQuery = $wipQuery->where('status', $request->status);
    }
    if($request->has('search') && $request->get('search') !== null) {
      $wipQuery = $wipQuery->where('wip_no', 'like', '%'.$request->search.'%');
    }
    $wipRecords = $wipQuery->with("material_request")
      ->whereHas('material_request', function ($query) {
        $query->where('status', '!=', 'Completed');
      })
      ->orderBy("created_at", "desc")
      ->get();
    return view('admin.production.wip.index', compact(
      "wipRecords"
    ));
  }

  public function detail(int $mr_id)
  {
    $mr = MaterialRequest::where("id", $mr_id)->first();
    if (!$mr) {
      return redirect()->route("admin.production.material-request.index");
    }
    $wipRecord = WipRecord::query()
      ->where("mr_id", $mr_id)
      ->with("material_request")
      ->first();
    $mrId = $mr_id;
    return view("admin.production.wip.detail", compact("wipRecord", "mrId"));
  }

  public function storeWIP(Request $request)
  {
    $request->validate([
      'mr_id' => 'required|exists:material_requests,id',
    ]);

    try {
      WipRecord::create([
        'mr_id' => $request->mr_id,
        'produced_qty' => 0,
        'rejected_qty' => 0,
      ]);

      MaterialRequest::where('id', $request->mr_id)->update([
        'status' => 'Delivered to WIP',
      ]);

      return response()->json([
        'success' => true,
        'message' => 'Berhasil deliver Picking List ke WIP.',
      ]);
    } catch (\Throwable $e) {
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
      ], 500);
    }
  }

  public function start($id)
  {
    $wip = WipRecord::findOrFail($id);

    if (in_array($wip->status, ['Pending', 'Paused'])) {
      $wip->update([
        'status' => 'Running',
        'started_at' => $wip->started_at ?? now(),
        'paused_at' => null,
      ]);
    }

    return back()->with('success', 'WIP dimulai.');
  }

  public function pause($id)
  {
    $wip = WipRecord::findOrFail($id);

    if ($wip->status === 'Running') {
      $elapsed = now()->diffInSeconds($wip->started_at) + $wip->elapsed_seconds;

      $wip->update([
        'status' => 'Paused',
        'paused_at' => now(),
        'elapsed_seconds' => $elapsed,
      ]);
    }

    return back()->with('success', 'WIP dijeda.');
  }

  public function resume($id)
  {
    $wip = WipRecord::findOrFail($id);
    $wip->update([
      'status' => 'Running',
      'started_at' => now(),
    ]);
    return back()->with('success', 'WIP resumed.');
  }

  public function finish(Request $request, $id, AccurateService $accurate)
  {
    $request->validate([
      'finished_qty' => 'required|integer|min:0',
      'rejects_qty' => 'required|integer|min:0',
      'labor_cost' => 'nullable|numeric|min:0',
      'overhead_cost' => 'nullable|numeric|min:0',
    ]);

    try {
      DB::beginTransaction();
      $wip = WipRecord::findOrFail($id);
      if ($wip->status === 'Completed') {
        DB::rollBack();
        return response()->json([
          'status' => 'error',
          'message' => 'Record WIP sudah diselesaikan sebelumnya.'
        ], 400);
      }
      $materialRequest = $wip->material_request;
      $wo_no = $materialRequest->wo_no;
      $selectedWorkOrderDetail = $accurate->getWorkOrderDetailByNumber($wo_no);
      $bom = $accurate->getBillOfMaterialDetail($selectedWorkOrderDetail['billOfMaterialId']);
      $items = $bom['detailMaterial'] ?? [];
      $fgQuantity = $selectedWorkOrderDetail["quantity"];
      $finishedQty = $request->input('finished_qty');


      $totalMaterialCost = 0;
      foreach ($items as $material) {
        $materialCost = ($material['totalStandardCost'] ?? 0) * $fgQuantity;
        $totalMaterialCost += $materialCost;
      }
      $laborCost = $request->input('labor_cost', 0);
      $overheadCost = $request->input('overhead_cost', 0);
      $totalCost = $totalMaterialCost + $laborCost + $overheadCost;
      $hppStandard = $fgQuantity > 0 ? $totalCost / $fgQuantity : 0;
      
      $hppActual = $finishedQty > 0 ? $totalCost / $finishedQty : 0;

      $varianceNominal = $hppActual - $hppStandard;
      $variancePercentage = $hppStandard > 0 ? (($varianceNominal / $hppStandard) * 100) : 0;
      
      $varianceStatus = 'On Budget';
      if ($variancePercentage > 5) {
        $varianceStatus = 'Over Budget';
      } elseif ($variancePercentage < -5) {
        $varianceStatus = 'Under Budget';
      }

      $elapsed = $wip->elapsed_seconds;
      if ($wip->status === 'Running' && $wip->started_at) {
        $elapsed += Carbon::parse($wip->started_at)->diffInSeconds(now());
      }

      // Buat Finished Good jika qty > 0
      if($finishedQty > 0) {
        $itemNo = $selectedWorkOrderDetail['item']['no'] ?? null;
        $item = Item::where('item_code', $itemNo)->first();
        FinishedGood::create([
          'item_id' => $item ? $item->id : null,
          'wip_id' => $wip->id,
          'quantity' => $finishedQty,
          'qc_status' => 'OK',
          'status' => 'Not Stored'
        ]);
      }

      // Buat Reject Production jika ada rejects_qty
      $rejectsQty = $request->input('rejects_qty');
      if($rejectsQty > 0) {
        RejectProduction::create([
          'wip_id' => $wip->id,
          'date' => now(),
        ]);
      }

      // Update WIP status
      $wip->update([
        'status' => 'Completed',
        'finished_at' => now(),
        'elapsed_seconds' => $elapsed,
        'produced_qty' => $finishedQty,
        'rejected_qty' => $request->input('rejects_qty'),
      ]);

      // Simpan Manufacture Cost
      ManufactureCost::create([
        'wip_id' => $wip->id,
        'raw_material_cost' => $totalMaterialCost,
        'labor_cost' => $laborCost,
        'overhead_cost' => $overheadCost,
        'total_cost' => $totalCost,
        'hpp_actual_per_unit' => $hppActual,
        'variance_nominal' => (string) $varianceNominal,
        'variance_percentage' => $variancePercentage,
        'variance_status' => $varianceStatus,
      ]);

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => 'Produksi WIP berhasil diselesaikan.',
        'data' => $wip,
      ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Record WIP tidak ditemukan.'
      ], 404);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Terjadi kesalahan server saat menyelesaikan WIP.',
        'error_details' => $e->getMessage()
      ], 500);
    }
  }


  public function changeQuantity(int $id, Request $request)
  {
    $request->validate([
      'produced_qty' => 'required|integer|min:0',
      'rejected_qty' => 'required|integer|min:0',
    ]);

    $wipRecord = WipRecord::findOrFail($id);
    if ($wipRecord->status !== 'Completed') {
      return response()->json([
        'message' => 'Kuantitas hanya bisa diedit untuk WIP dengan status Completed.'
      ], 400);
    }

    if ($request->produced_qty == 0 && $request->rejected_qty == 0) {
      return response()->json([
        'message' => 'Produced Qty atau Rejected Qty harus lebih dari 0.',
      ], 422);
    }

    try {
      $wipRecord->update([
        'produced_qty' => $request->produced_qty,
        'rejected_qty' => $request->rejected_qty,
      ]);

      return response()->json([
        'message' => 'Kuantitas produksi berhasil diperbarui.',
        'wip' => $wipRecord
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Gagal mengupdate quantity WIP: ' . $e->getMessage()
      ], 500);
    }
  }
}
