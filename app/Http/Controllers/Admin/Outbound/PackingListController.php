<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Models\SuperAdmin\MasterData\Item;
use App\Services\AccurateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PackingListController extends Controller
{
  public function create(Request $request, AccurateService $accurate): View
  {
    $soId = (int) $request->get('so_id');
    $salesOrder = $soId ? $accurate->getSalesOrderDetail($soId) : null;
    $finishedGoods = FinishedGood::with(['item', 'production_item_label'])->where("status", "Stored")->get();

    return view('admin.outbound.packing-lists.create', [
      'salesOrder' => $salesOrder,
      'finishedGoods' => $finishedGoods
    ]);
  }

  public function addTempItem(Request $request)
  {
    $validated = $request->validate([
      'so_id' => 'required|string',
      'fg_id' => 'required|string|exists:finished_goods,id',
      'quantity' => 'required|integer|min:1',
    ]);

    $fg = FinishedGood::where("id", $validated['fg_id'])->first();

    $mockItemData = [
      'so_id' => $validated['so_id'],
      'fg_id' => $validated['fg_id'],
      'fg_name' => $fg->item->item_name,
      'quantity' => $validated['quantity'],
      'qr_code' => "tes"
    ];
    return response()->json([
      'success' => true,
      'item' => $mockItemData
    ]);
  }

  public function validateQr(Request $request, AccurateService $accurate)
  {
    $validated = $request->validate([
      'qr_code' => 'required|string',
      'so_id' => 'required|string',
    ]);

    $qrCode = $validated['qr_code'];

    $label = ProductionItemLabel::where('qr_code', $qrCode)->first();
    if (!$label) {
      return response()->json(['success' => false, 'message' => 'QR Code tidak ditemukan.'], 404);
    }

    // 2. Cari Finished Good terkait dengan Label
    $fg = FinishedGood::with('production_item_label')->find($label->id);
    if (!$fg) {
      return response()->json(['success' => false, 'message' => 'Item terkait tidak ditemukan.'], 404);
    }

    $fgInventory = Inventory::where('item_id', $fg->id)->first();
    $item = Item::where('id', $fg->item_id)->first();
    $itemCode = $item->item_code;

    $soDetail = $accurate->getSalesOrderDetail($validated['so_id']);
    if (!$soDetail || empty($soDetail['detailItem'])) {
      return response()->json([
        'success' => false,
        'message' => 'Sales Order tidak memiliki detail item atau tidak ditemukan.'
      ], 404);
    }

    // $matchedItem = collect($soDetail['detailItem'])->first(function ($detail) use ($itemCode) {
    //   return isset($detail['item']['no']) && $detail['item']['no'] === $itemCode;
    // });

    // if (!$matchedItem) {
    //   return response()->json([
    //     'success' => false,
    //     'message' => "Item dengan kode {$itemCode} tidak termasuk dalam Sales Order ini."
    //   ], 400);
    // }

    // if ($fgInventory->quantity < $matchedItem['quantity']) {
    //   return response()->json(['success' => false, 'message' => 'Qty di inventory kurang']);
    // }

    // $itemData = [
    //   'item_id' => $fg->id, // ID Finished Good
    //   'item_name' => $fg->item->item_name,
    //   'quantity' => $matchedItem['quantity'],
    //   'qr_code' => $qrCode,
    // ];
    $itemData = [
      'fg_id' => $fg->id, // ID Finished Good
      'item_name' => $fg->item->item_name,
      'quantity' => $fgInventory->quantity,
      'qr_code' => $qrCode,
    ];

    return response()->json([
      'success' => true,
      'message' => 'QR Code Valid.',
      'data' => $itemData
    ]);
  }
}
