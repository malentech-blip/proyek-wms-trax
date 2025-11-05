<?php

namespace App\Http\Controllers\Admin\Production;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\MaterialRequest;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Models\Admin\Production\WipRecord;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Pallet;
use App\Models\SuperAdmin\MasterData\Rack;
use App\Services\AccurateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinishedGoodsController extends Controller
{
  public function index(Request $request)
  {
    $finishedGoods = FinishedGood::with(["wip_record", "item", "production_item_label", "production_item_label.location", "production_item_label.rack", "production_item_label.pallet"])->orderBy("created_at", "desc")->get();

    return view("admin.production.finished-goods.index", compact("finishedGoods"));
  }

  public function detail(int $mr_id)
  {
    $wipRecord = WipRecord::where('mr_id', $mr_id)->first();
    if ($wipRecord) {
      $finishedGood = FinishedGood::with(["wip_record", "item", "production_item_label", "production_item_label.location", "production_item_label.rack", "production_item_label.pallet"])->where('wip_id', $wipRecord->id)->first();
    } else {
      return redirect()->route("admin.production.material-request.index");
    }
    $mrId = $mr_id;
    if ($wipRecord->status !== "Completed") {
      return redirect()->route("admin.production.wip.detail", $wipRecord->id);
    }
    $locations = Location::all();
    $racks = Rack::all();
    $pallets = Pallet::all();
    $items = Item::where("item_type", "Finished Good")->get();
    return view('admin.production.finished-goods.detail', compact('wipRecord', 'finishedGood', 'mrId', 'locations', 'racks', 'pallets', 'items'));
  }

  public function storeFG(Request $request, AccurateService $accurate)
  {
    $validated_data = $request->validate([
      "wip_id"      => ["required", "string", "exists:wip_records,id"],
      "item_id"     => ["required", "string", "exists:items,id"],
      "quantity"    => ["required", "integer", "min:1"],
      "location_id" => ["required", "string", "exists:locations,id"],
      "rack_id"     => ["required", "string", "exists:racks,id"],
      "pallet_id"   => ["required", "string", "exists:pallets,id"],
      "batch_no"    => ["required", "string", "max:50"]
    ]);

    DB::beginTransaction();
    try {
      $item = Item::findOrFail($validated_data['item_id']);
      $location = Location::findOrFail($validated_data['location_id']);
      $itemCode = $item->item_code;
      $warehouseNo = $location->code;

      $fgData = [
        "transDate" => now()->format('d/m/Y'),
        "warehouseNo" => "GUDANG UTAMA",
        "memo" => "Hasil produksi dari WIP #{$validated_data['wip_id']}",
        "detailItem" => [
          [
            "itemNo" => $itemCode,
            "quantity" => $validated_data["quantity"],
            "unit" => $item->uom ?? "PCS",
            "warehouseNo" => $warehouseNo,
            "memo" => "Batch {$validated_data['batch_no']}"
          ]
        ]
      ];


      $fgSlip = $accurate->saveFinishedGoodSlip($fgData);


      $label = ProductionItemLabel::create([
        "item_id"     => $validated_data['item_id'],
        "qr_code"     => Str::uuid(),
        "quantity"    => $validated_data["quantity"],
        "batch_no"    => $validated_data["batch_no"],
        "location_id" => $validated_data['location_id'],
        "rack_id"     => $validated_data['rack_id'],
        "pallet_id"   => $validated_data['pallet_id'],
        "status"      => "Approved"
      ]);

      $finishedGood = FinishedGood::create([
        "wip_id"   => $validated_data["wip_id"],
        "item_id"  => $validated_data["item_id"],
        "quantity" => $validated_data["quantity"],
        "label_id" => $label->id,
        "qc_status" => "OK"
      ]);

      DB::commit();
      return response()->json([
        'status'  => 'success',
        'message' => 'Finished goods berhasil dibuat.',
        'data'    => $fgSlip
      ], 201);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response()->json([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan server saat menyimpan data.',
      ], 500);
    }
  }

  public function storingInv(int $fg_id)
  {
    $finished_good = FinishedGood::where("id", $fg_id)->first();

    if (!$finished_good) {
      return response()->json([
        'status'  => 'error',
        'message' => 'Finished Good tidak ditemukan.',
      ], 400);
    }

    $finished_good->update([
      "stored_at" => now(),
      "status"    => "Stored"
    ]);

    return response()->json([
      'status' => 'success',
      'message' => 'Finished Good updated to stored.',
    ], 200);
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

    $pdf = Pdf::setOptions([
      'isHtml5ParserEnabled' => true, // wajib agar SVG kebaca
      'isPhpEnabled' => true,
      'isRemoteEnabled' => true,
    ])
      ->loadView('admin.production.finished-goods.label-pdf', compact('itemLabels'))
      ->setPaper([0, 0, $widthInPoints, $heightInPoints]);
    return $pdf->stream('labels-' . $labelId . '.pdf');
  }
}
