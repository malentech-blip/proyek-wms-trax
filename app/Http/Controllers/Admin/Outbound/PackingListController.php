<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Outbound\OutboundPackingItem;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Models\SuperAdmin\MasterData\Item;
use App\Services\AccurateService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PackingListController extends Controller
{

  public function index()
  {
    $packingLists = PackingList::with(['sales_order'])->get();
    return view('admin.outbound.packing-lists.index', compact('packingLists'));
  }

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
    $label = ProductionItemLabel::with(['location'])->where("id", $fg->label_id)->first();

    $mockItemData = [
      'so_id' => $validated['so_id'],
      'fg_id' => $validated['fg_id'],
      'fg_code' => $fg->item->item_code,
      'fg_name' => $fg->item->item_name,
      'quantity' => $validated['quantity'],
      'quantity_ready' => $fg->quantity,
      'location' => $label->location->name
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

    $label = ProductionItemLabel::with(['location'])->where('qr_code', $qrCode)->first();
    if (!$label) {
      return response()->json(['success' => false, 'message' => 'QR Code tidak ditemukan.'], 404);
    }

    // 2. Cari Finished Good terkait dengan Label
    $fg = FinishedGood::with('production_item_label')->find($label->id);
    if (!$fg) {
      return response()->json(['success' => false, 'message' => 'Item terkait tidak ditemukan.'], 404);
    }

    $fgInventory = Inventory::where('item_id', $fg->item_id)->first();
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
      'item_code' => $fg->item->item_code,
      'item_name' => $fg->item->item_name,
      'quantity' => $fg->quantity,
      'quantity_inventory' => $fg->quantity,
      'qr_code' => $qrCode,
      'location' => $label->location->name
    ];

    return response()->json([
      'success' => true,
      'message' => 'QR Code Valid.',
      'data' => $itemData
    ]);
  }

  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'so_number' => 'required|string|exists:sales_orders,so_number',
        'items' => 'required|array|min:1',
        'items.*.fg_id' => 'required|string|exists:finished_goods,id',
        'items.*.quantity' => 'required|integer|min:1',
      ]);
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Data input tidak valid.',
        'errors' => $e->errors()
      ], 422);
    }

    DB::beginTransaction();

    try {
      $itemsData = $validated['items'];
      $salesOrder = SalesOrder::where("so_number", $validated['so_number'])->first();
      $packingList = PackingList::create([
        'so_id' => $salesOrder->id,
        'status' => 'Packed',
        'packed_by' => "-",
        'packed_at' => now(),
      ]);

      foreach ($itemsData as $item) {
        OutboundPackingItem::create([
          'packing_id' => $packingList->id,
          'fg_id' => $item['fg_id'],
          'quantity' => $item['quantity'],
        ]);
      }

      if ($salesOrder) {
        $salesOrder->update(['status' => 'Packed']);
      }

      DB::commit();
      return response()->json([
        'success' => true,
        'message' => 'Packing List berhasil dibuat dan item tersimpan.',
        'packing_list_id' => $packingList->id,
        'packing_list_number' => $packingList->packing_list_number,
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'success' => false,
        'message' => 'Gagal memproses Packing List. Silakan cek log server.',
        'error_detail' => $e->getMessage()
      ], 500);
    }
  }

  public function getItems(PackingList $packingList)
  {
    $items = OutboundPackingItem::with(['packing_list', 'finished_good'])->where("packing_id", $packingList->id)->get();

    return response()->json([
      'pl_number' => $packingList->id,
      'so_number' => $packingList->sales_order->so_number ?? 'N/A',
      'customer_name' => $packingList->sales_order->customer_id ?? 'N/A',
      'packed_by' => $packingList->packed_by ?? 'System',
      'packed_at' => $packingList->packed_at ? Carbon::parse($packingList->packed_at)->format('d/m/Y H:i') : 'N/A',
      'items' => $items->map(function ($item) {
        return [
          'product_code' => $item->finished_good->item->item_code ?? 'N/A',
          'product_name' => $item->finished_good->item->item_name ?? 'N/A',
          'label_code' => $item->finished_good->production_item_label->qr_code,
          'qr_code' => $item->finished_good->production_item_label->qr_code,
          'quantity' => $item->quantity,
        ];
      })
    ]);
  }


  public function transit(PackingList $packingList)
  {
    try {
      // Validasi status - hanya Packed atau Ready to Ship yang bisa di-transit
      if (!in_array($packingList->status, ['Packed', 'Ready to Ship'])) {
        return response()->json([
          'success' => false,
          'message' => 'Packing list dengan status "' . $packingList->status . '" tidak dapat dilanjutkan ke transit'
        ], 400);
      }

      DB::beginTransaction();

      // Update packing list status
      $packingList->update([
        'status' => 'In Transit', // atau 'In Transit' sesuai kebutuhan
      ]);

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Packing list berhasil dilanjutkan ke tahap transit',
        'data' => [
          'id' => $packingList->id,
          'status' => $packingList->status,
        ]
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'success' => false,
        'message' => 'Gagal memproses transit: ' . $e->getMessage()
      ], 500);
    }
  }
}
