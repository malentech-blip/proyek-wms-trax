<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Models\Admin\Production\WipRecord;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Pallet;
use App\Models\SuperAdmin\MasterData\Rack;
use App\Services\AccurateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinishedGoodsController extends Controller
{
  public function index(Request $request)
  {
    $fgQuery = FinishedGood::query();
    if ($request->has('create_date') && $request->get('create_date') !== null) {
      $fgQuery = $fgQuery->whereDate('created_at', $request->get('create_date'));
    }
    if ($request->has('search') && $request->get('search') !== null) {
      $fgQuery = $fgQuery
        ->whereHas('item', function ($query) use ($request) {
          $query->where('item_name', 'like', '%' . $request->get('search') . '%')
            ->orWhere('item_code', 'like', '%' . $request->get('search') . '%');
        })
        ->orWhereHas('production_item_label', function ($query) use ($request) {
          $query->where('batch_no', 'like', '%' . $request->get('search') . '%');
        });
    }
    $finishedGoods = $fgQuery->with(["wip_record", "item", "production_item_label", "production_item_label.location", "production_item_label.rack", "production_item_label.pallet"])->orderBy("created_at", "desc")->get();

    return view("admin.production.finished-goods.index", compact("finishedGoods"));
  }

  public function detail(int $mr_id)
  {
    $wipRecord = WipRecord::where('mr_id', $mr_id)->first();
    
    if (!$wipRecord) {
      return redirect()->route("admin.production.material-request.index");
    }
    
    if ($wipRecord->status !== "Completed") {
      return redirect()->route("admin.production.wip.detail", $wipRecord->id);
    }

    $finishedGood = FinishedGood::with([
      "wip_record", 
      "wip_record.manufactureCost",
      "item", 
      "production_item_label", 
      "production_item_label.location", 
      "production_item_label.rack", 
      "production_item_label.pallet"
    ])->where('wip_id', $wipRecord->id)->first();

    // Ambil data HPP dari manufacture_cost
    $manufactureCost = $wipRecord->manufactureCost ?? null;

    $mrId = $mr_id;
    $locations = Location::all();
    $racks = Rack::all();
    $pallets = Pallet::all();
    
    return view('admin.production.finished-goods.detail', compact('wipRecord', 'finishedGood', 'mrId', 'locations', 'racks', 'pallets', 'manufactureCost'));
  }


  // CREATE PRODUCTION LABELS AND STORING FG TO ACCURATE
  public function storingInv(Request $request, AccurateService $accurate)
  {
    $validated_data = $request->validate([
      "fg_id" => ["required", "exists:finished_goods,id"],
      "item_id"     => ["required", "exists:items,id"],
      "quantity"    => ["required", "integer", "min:1"],
      "location_id" => ["required", "exists:locations,id"],
      "rack_id"     => ["required", "exists:racks,id"],
      "pallet_id"   => ["required", "exists:pallets,id"],
    ]);

    DB::beginTransaction();
    try {
      $finished_good = FinishedGood::with('production_item_label')->where("id", (int)$request['fg_id'])->first();

      if (!$finished_good) {
        return response()->json([
          'status'  => 'error',
          'message' => 'Finished Good tidak ditemukan.',
        ], 400);
      }

      if ($finished_good->stored_at) {
        return response()->json([
          'status'  => 'error',
          'message' => 'Finished Good sudah disimpan ke inventory.',
        ], 400);
      }

      $item = Item::findOrFail($validated_data['item_id']);

      // Create production label
      $label = ProductionItemLabel::create([
        "item_id"     => $validated_data['item_id'],
        "quantity"    => $validated_data["quantity"],
        "location_id" => $validated_data['location_id'],
        "rack_id"     => $validated_data['rack_id'],
        "pallet_id"   => $validated_data['pallet_id'],
        "status"      => "Approved"
      ]);

      // Get Work Order details from Accurate
      $selectedWorkOrderDetail = $accurate->getWorkOrderDetailByNumber($finished_good->wip_record->material_request->wo_no);
      $portion = $selectedWorkOrderDetail['portion'];

      // Prepare Finished Good Slip payload
      $fgData = [
        "workOrderNumber" => $finished_good->wip_record->material_request->wo_no,
        "transDate" => now()->format('d/m/Y'),
        "detailItem" => [
          [
            "itemNo" => $item->item_code,
            "quantity" => (float) $validated_data["quantity"],
            "portion" => $portion,
            "detailSerialNumber" => [
              "transDate" => now()->format('d/m/Y'),
              "workOrderNumber" => $finished_good->wip_record->material_request->wo_no,
            ]
          ]
        ]
      ];

      // Send Finished Good Slip to Accurate
      try {
        $fgSlip = $accurate->saveFinishedGoodSlip($fgData);
        Log::info('Finished Good Slip berhasil dikirim ke Accurate', ['response' => $fgSlip]);
      } catch (\Exception $e) {
        Log::error('Gagal mengirim Finished Good Slip ke Accurate', ['error' => $e->getMessage()]);
        // Continue even if Accurate fails
      }

      // Update Finished Good status
      $finished_good->update([
        "stored_at" => now(),
        "status"    => "Stored",
        "label_id"  => $label->id
      ]);
      
      DB::commit();
      
      return response()->json([
        'status' => 'success',
        'message' => 'Finished Good berhasil dipindahkan ke inventory.',
      ], 200);
      
    } catch (\Exception $e) {
      DB::rollBack();
      
      return response()->json([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
      ], 500);
    }
  }

  public function getRacksByLocation($locationId)
  {
    $racks = Rack::where('location_id', $locationId)->get(['id', 'code']);
    return response()->json($racks);
  }

  public function getPalletsByRack($rackId)
  {
    $pallets = Pallet::where('rack_id', $rackId)->get(['id', 'code']);
    return response()->json($pallets);
  }

  public function printLabel(int $labelId)
  {
    $itemLabels = ProductionItemLabel::where('id', $labelId)->get();

    $widthInPoints = 52 * 2.83465;
    $heightInPoints = 32 * 2.83465;

    $pdf = Pdf::loadView('admin.production.finished-goods.label-pdf', compact('itemLabels'))
      ->setPaper("a7", "portrait");
    return $pdf->stream('labels-' . $labelId . '.pdf');
  }
}
