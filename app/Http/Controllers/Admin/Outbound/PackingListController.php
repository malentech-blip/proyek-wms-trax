<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Outbound\OutboundPackingItem;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use App\Models\Admin\Outbound\TransitInventory;
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

  public function index(Request $request)
  {
    $plQuery = PackingList::query();
    if($request->has('packed_date') && $request->get('packed_date') !== null) {
      $plQuery = $plQuery->whereDate('packed_at', $request->packed_date);
    }
    if($request->has('status') && $request->get('status') !== null) {
      $plQuery = $plQuery->where('status', $request->status);
    }
    if($request->has('search') && $request->get('search') !== null) {
      $plQuery = $plQuery->whereHas('sales_order', function ($query) use ($request) {
        $query->where('so_number', 'like', '%' . $request->get('search') . '%');
      });
    }
    $packingLists = $plQuery->with(['sales_order'])->orderBy("created_at", "desc")->paginate(20);
    return view('admin.outbound.packing-lists.index', compact('packingLists'));
  }

  public function detail(int $packing_id, AccurateService $accurate)
  {
    $packingList = PackingList::with(['sales_order'])->where("id", $packing_id)->first();
    if (!$packingList) {
      return redirect()->route("admin.outbound.packing-lists.index");
    }
    $items = OutboundPackingItem::where('packing_id', $packing_id)->with(['finished_good.item', 'finished_good.production_item_label'])->get();
    $soNumber = $packingList->sales_order->so_number;
    $so = $accurate->getSalesOrderByNumber($soNumber);
    return view("admin.outbound.packing-lists.detail", compact("packingList", "items", "so"));
  }

  public function create(Request $request, AccurateService $accurate)
  {
    $soId = (int) $request->get('so_id');
    $salesOrder = $soId ? $accurate->getSalesOrderDetail($soId) : null;
    // dd($salesOrder);
    $finishedGoods = FinishedGood::with(['item', 'production_item_label'])->where("status", "Stored")->get();
    if (!$request->filled('so_id')) {
      return redirect()->route('admin.outbound.sales-orders.index'); 
    }
    if($salesOrder && isset($salesOrder[0]) && $salesOrder[0] == "Pesanan Penjualan tidak tepat") {
      return redirect()->route('admin.outbound.sales-orders.index');
    }
    $existedSo = SalesOrder::where("so_number", $salesOrder['number'])->first();
    if(!$existedSo) {
      return redirect()->route('admin.outbound.sales-orders.index'); 
    }
    if($existedSo->status !== 'Pending') {
      return redirect()->route('admin.outbound.sales-orders.index'); 
    }
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

    $label = ProductionItemLabel::with(['location'])->where('barcode', $qrCode)->first();
    if (!$label) {
      return response()->json(['success' => false, 'message' => 'QR Code tidak ditemukan.'], 404);
    }

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
      'message' => 'Barcode Valid.',
      'data' => $itemData
    ]);
  }

  public function store(Request $request)
  {
    try {
      $validated = $request->validate([
        'so_number' => 'required|string|exists:sales_orders,so_number',
        'items' => 'required|array|min:1',
        'packed_by' => 'required|string',
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
        'packed_by' => $validated['packed_by'],
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
        'packingId' => $packingList->id,
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

  public function getItems(PackingList $packingList, AccurateService $accurate)
  {
    $items = OutboundPackingItem::with(['packing_list', 'finished_good'])->where("packing_id", $packingList->id)->get();

    $customer = $accurate->getCustomerDetail($packingList->sales_order->customer_id);

    return response()->json([
      'pl_number' => $packingList->id,
      'so_number' => $packingList->sales_order->so_number ?? 'N/A',
      'customer_name' => $customer['name'] ?? 'N/A',
      'packed_by' => $packingList->packed_by ?? 'System',
      'packed_at' => $packingList->packed_at ? Carbon::parse($packingList->packed_at)->format('d/m/Y H:i') : 'N/A',
      'items' => $items->map(function ($item) {
        return [
          'product_code' => $item->finished_good->item->item_code ?? 'N/A',
          'product_name' => $item->finished_good->item->item_name ?? 'N/A',
          'label_code' => $item->finished_good->production_item_label->barcode,
          'qr_code' => $item->finished_good->production_item_label->barcode,
          'quantity' => $item->quantity,
        ];
      })
    ]);
  }

  public function transit(int $packing_id)
  {
    try {
      $packingList = PackingList::where([
        'id' => $packing_id 
      ])->first();
      if (!in_array($packingList->status, ['Packed'])) {
        return response()->json([
          'success' => false,
          'message' => 'Packing list dengan status "' . $packingList->status . '" tidak dapat dilanjutkan ke transit'
        ], 400);
      }

      DB::beginTransaction();
      $packingList->update([
        'status' => 'In Transit',
      ]);
      TransitInventory::create([
        "packing_id" => $packingList->id,
        "transit_in_at" => now()
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
