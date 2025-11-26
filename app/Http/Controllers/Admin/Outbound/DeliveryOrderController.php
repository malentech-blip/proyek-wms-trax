<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\OutboundPackingItem;
use App\Models\Admin\Outbound\PackingList;
use App\Services\AccurateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryOrderController extends Controller
{
  public function index(Request $request): View
  {
    $query = DeliveryOrder::with(['packingList.sales_order'])
      ->latest();

    if ($request->filled('search')) {
      $searchTerm = '%' . $request->search . '%';
      $query->where(function ($q) use ($searchTerm) {
        $q->where('delivered_no', 'like', $searchTerm)
          ->orWhere('driver_name', 'like', $searchTerm);
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    $deliveryOrders = $query->paginate(15)->withQueryString();

    return view('admin.outbound.delivery-orders.index', compact('deliveryOrders'));
  }

  public function store(Request $request, AccurateService $accurate)
  {
    $validated = $request->validate([
      'packing_id' => 'required|string|exists:packing_lists,id',
      'delivery_date' => 'required|string',
      'driver_name' => 'required|string|max:100',
    ]);

    $packingListId = $validated['packing_id'];
    DB::beginTransaction();
    try {
      Log::info('=== START: Membuat Delivery Order ===', [
        'timestamp' => now()->toDateTimeString(),
        'packing_id' => $packingListId,
        'driver_name' => $validated['driver_name'],
        'delivery_date' => $validated['delivery_date']
      ]);

      $packingList = PackingList::with(['sales_order', 'items'])->findOrFail($packingListId);

      if ($packingList->status !== 'Packed') {
        Log::warning('❌ Status Packing List tidak valid', [
          'expected' => 'Packed',
          'actual' => $packingList->status
        ]);
        DB::rollBack();
        return response()->json([
          'message' => 'Gagal membuat Delivery Order. Status Packing List harus "Packed".',
          'packing_list_status' => $packingList->status
        ], 400);
      }

      $customerId = $packingList->sales_order->customer_id;
      Log::info('Mengambil data customer dari Accurate', ['customer_id' => $customerId]);
      
      $customer = $accurate->getCustomerDetail($customerId);
      $customerNo = $customer['customerNo'];

      Log::info('Customer berhasil diambil', [
        'customer_no' => $customerNo,
        'customer_name' => $customer['name'] ?? 'N/A'
      ]);

      $soNumber = $packingList->sales_order->so_number;
      Log::info('Mengambil data Sales Order dari Accurate', ['so_number' => $soNumber]);
      
      $salesOrder = $soNumber ? $accurate->getSalesOrderByNumber($soNumber) : null;
      $branchId = $salesOrder['branchId'] ?? null;
      $items = $salesOrder['detailItem'] ?? [];

      Log::info('Sales Order berhasil diambil', [
        'so_number' => $soNumber,
        'branch_id' => $branchId,
        'total_items' => count($items),
        'items_data' => collect($items)->map(function ($item) {
          return [
            'item_no' => $item['item']['no'] ?? 'N/A',
            'quantity' => $item['quantity'] ?? 0,
            'unit_price' => $item['unitPrice'] ?? 0,
          ];
        })->toArray()
      ]);


      $payload = [
        "customerNo" => $customerNo,
        "salesOrderNo" => $packingList->sales_order->so_number,
        "transDate" => Carbon::parse($request->delivery_date)->format('d/m/Y'),
        "branchId" => $branchId,
        "detailItem" => collect($items)->map(function ($item) {
          return [
            "itemNo"   => $item["item"]["no"],
            "quantity" => $item["quantity"] ?? 0,
            "unitPrice" => $item["item"]["unitPrice"] ?? 0,
          ];
        })->values()->toArray(),
      ];

      $accurateResult = $accurate->saveDeliveryOrder($payload);

      Log::info('Delivery Order berhasil disimpan ke Accurate', [
        'accurate_result' => $accurateResult
      ]);


      $deliveryOrder = DeliveryOrder::create([
        'packing_id' => $packingListId,
        'driver_name' => $validated['driver_name'],
        'delivery_date' => $validated['delivery_date'],
        'status' => "In Delivery",
      ]);

      Log::info('✅ BERHASIL: Delivery Order tersimpan ke database lokal', [
        'do_id' => $deliveryOrder->id,
        'do_number' => $deliveryOrder->delivered_no,
        'status' => $deliveryOrder->status
      ]);

      $packingList->update([
        'status' => 'Shipped',
      ]);
      DB::commit();

      Log::info('=== END: Delivery Order berhasil dibuat ===');

      return response()->json([
        'message' => 'Delivery Order berhasil dibuat.',
        'do_number' => $deliveryOrder->delivered_no,
        'driver_name' => $deliveryOrder->driver_name,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      
      Log::error('❌ EXCEPTION: Gagal membuat Delivery Order', [
        'timestamp' => now()->toDateTimeString(),
        'error_message' => $e->getMessage(),
        'error_file' => $e->getFile(),
        'error_line' => $e->getLine(),
        'error_trace' => $e->getTraceAsString(),
        'packing_id' => $packingListId
      ]);

      return response()->json([
        'message' => 'Terjadi kesalahan saat memproses Delivery Order. Transaksi dibatalkan.',
        'error_detail' => $e->getMessage()
      ], 500);
    }
  }

  public function getDetails($id, AccurateService $accurate)
  {
    try {
      $deliveryOrder = DeliveryOrder::with([
        'packingList.sales_order',
      ])->findOrFail($id);

      // Prepare items data
      $items = $deliveryOrder->packingList->items->map(function ($item) {
        return [
          'product_code' => $item->finished_good->item->item_code ?? 'N/A',
          'product_name' => $item->finished_good->item->item_name ?? 'N/A',
          'label_code' => $item->finished_good->production_item_label->barcode,
          'qr_code' => $item->finished_good->production_item_label->barcode,
          'quantity' => $item->quantity,
        ];
      });

      $customer = $accurate->getCustomerDetail($deliveryOrder->packingList->sales_order->customer_id);

      $data = [
        'delivered_no' => $deliveryOrder->delivered_no,
        'status' => $deliveryOrder->status,
        'so_number' => $deliveryOrder->packingList->sales_order->so_number ?? 'N/A',
        'customer_name' => $customer['name'] ?? 'N/A',
        'customer_id' => $deliveryOrder->packingList->sales_order->customer_id ?? null,
        'driver_name' => $deliveryOrder->driver_name,
        'delivery_date' => $deliveryOrder->delivery_date
          ? Carbon::parse($deliveryOrder->delivery_date)->format('d M Y, H:i')
          : 'N/A',
        'created_at' => $deliveryOrder->created_at->format('d M Y, H:i'),
        'items' => $items,
      ];

      return response()->json($data);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Delivery order tidak ditemukan',
        'error' => $e->getMessage()
      ], 404);
    }
  }

  public function detail(int $packing_id, AccurateService $accurate)
  {
    $deliveryOrder = DeliveryOrder::with(["packingList.sales_order"])->where("packing_id", $packing_id)->first();
    if (!$deliveryOrder) {
      return redirect()->route("admin.outbound.packing-lists.detail", $packing_id);
    }
    $so = $deliveryOrder->packingList->sales_order;
    $customer_id = $so->customer_id;
    $customer = $accurate->getCustomerDetail($customer_id);
    return view("admin.outbound.delivery-orders.detail", compact("deliveryOrder", "customer"));
  }

  public function markDelivered($id)
  {
    try {
      DB::beginTransaction();
      $deliveryOrder = DeliveryOrder::findOrFail($id);

      // Check if already delivered
      if ($deliveryOrder->status === 'Delivered') {
        return response()->json([
          'message' => 'Delivery order sudah dalam status Delivered'
        ], 400);
      }

      // Update status to Delivered
      $deliveryOrder->update([
        'status' => 'Delivered',
      ]);

      // Update packing list status to Shipped
      $deliveryOrder->packingList()->update([
        'status' => 'Shipped',
      ]);

      DB::commit();

      return response()->json([
        'message' => 'Delivery order berhasil ditandai sebagai Delivered',
        'data' => [
          'delivery_no' => $deliveryOrder->delivered_no,
          'status' => $deliveryOrder->status,
          'delivered_at' => $deliveryOrder->delivery_at
        ]
      ]);
    } catch (\Exception $e) {
      DB::rollBack();

      return response()->json([
        'message' => 'Gagal memperbarui status delivery order',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  public function printPdf($id, AccurateService $accurate)
  {
    try {
      $deliveryOrder = DeliveryOrder::with([
        'packingList.sales_order',
      ])->findOrFail($id);

      $items = OutboundPackingItem::with(["finished_good.item", "finished_good.production_item_label"])->where("packing_id", $deliveryOrder->packing_id)->get();

      $customer = $accurate->getCustomerDetail($deliveryOrder->packingList->sales_order->customer_id);
      $data = [
        'deliveryOrder' => $deliveryOrder,
        'salesOrder' => $deliveryOrder->packingList->sales_order,
        'customer' => $customer,
        'items' => $items,
        'generatedAt' => now()->format('d M Y H:i:s'),
      ];

      // Generate PDF
      $pdf = Pdf::loadView('admin.outbound.delivery-orders.pdf', $data);

      // Set paper size and orientation
      $pdf->setPaper('a4', 'portrait');

      // Download with filename
      $filename = 'DO_' . $deliveryOrder->delivery_no . '_' . now()->format('YmdHis') . '.pdf';

      return $pdf->download($filename);
    } catch (\Exception $e) {
      return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
    }
  }
}
