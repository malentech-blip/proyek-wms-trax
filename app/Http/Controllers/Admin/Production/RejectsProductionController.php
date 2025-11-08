<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\RejectProduction;
use App\Models\Admin\Production\WipRecord;
use App\Services\AccurateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RejectsProductionController extends Controller
{
  public function index(Request $request)
  {
    $rpQuery = RejectProduction::query();
    if ($request->has('create_date') && $request->get('create_date') !== null) {
      $rpQuery = $rpQuery->whereDate('date', $request->get('create_date'));
    }
    if ($request->has('search') && $request->get('search') !== null) {
      $rpQuery = $rpQuery->where('reason', 'like', '%' . $request->get('search') . '%')
        ->orWhere('handled_by', 'like', '%' . $request->get('search') . '%');
    }
    if ($request->has('action') && $request->get('action') !== null) {
      $rpQuery = $rpQuery->where('action', $request->get('action'));
    }
    $rejectsProduction = $rpQuery->with("wip_record")
      ->orderBy("created_at", "desc")
      ->get();
    $wipRecords = WipRecord::query()
      ->where("status", "Completed")
      ->orderBy("created_at", "desc")
      ->paginate(10);
    return view('admin.production.rejects-production.index', compact('rejectsProduction', "wipRecords"));
  }

  public function detail(int $mr_id)
  {
    $wipRecord = WipRecord::where('mr_id', $mr_id)->first();
    if ($wipRecord) {
      $rejectProduction = RejectProduction::where('wip_id', $wipRecord->id)->first();
    } else {
      return redirect()->route("admin.production.material-request.index");
    }
    $mrId = $mr_id;
    if ($wipRecord->status !== "Completed") {
      return redirect()->route("admin.production.wip.detail", $wipRecord->id);
    }
    return view('admin.production.rejects-production.detail', compact('rejectProduction', 'wipRecord', "mrId"));
  }

  public function store(Request $request)
  {
    $validated_data = $request->validate([
      'wip_id' => 'required|exists:wip_records,id',
      "reason" => ["required", "string", "max:255"],
      "action" => ["required", "string", "max:255"],
      "handled_by" => ["required", "string", "max:100"],
      "date" => ["required", "date"],
    ]);

    try {
      DB::beginTransaction();
      $issue = RejectProduction::create([
        'wip_id' => $validated_data['wip_id'],
        'reason' => $validated_data['reason'],
        'action' => $validated_data['action'],
        'handled_by' => $validated_data['handled_by'],
        'date' => $validated_data['date'],
      ]);
      DB::commit();
      return response()->json([
        'status' => 'success',
        'message' => 'Laporan masalah WIP berhasil disimpan.',
        'data' => $issue,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'status' => 'error',
        'message' => 'Gagal menyimpan laporan masalah WIP.',
      ], 500);
    }
  }
}
