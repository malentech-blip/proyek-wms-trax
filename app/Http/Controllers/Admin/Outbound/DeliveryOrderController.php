<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\PackingList;
use App\Services\AccurateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
      $packingList = PackingList::findOrFail($packingListId);

      if ($packingList->status !== 'Packed') {
        DB::rollBack();
        return response()->json([
          'message' => 'Gagal membuat Delivery Order. Status Packing List harus "Packed".',
          'packing_list_status' => $packingList->status
        ], 400);
      }

      $deliveryOrder = DeliveryOrder::create([
        'packing_id' => $packingListId,
        'driver_name' => $validated['driver_name'],
        'delivery_date' => $validated['delivery_date'],
        'status' => "In Transit",
      ]);
      $packingList->update([
        'status' => 'Shipped',
      ]);

      DB::commit();
      return response()->json([
        'message' => 'Delivery Order berhasil dibuat.',
        'do_number' => $deliveryOrder->do_number,
        'driver_name' => $deliveryOrder->driver_name,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'Terjadi kesalahan saat memproses Delivery Order. Transaksi dibatalkan.',
        'error_detail' => $e->getMessage()
      ], 500);
    }
  }

  public function getDetails($do_id)
  {
    try {
      // Cari Delivery Order dengan relasi yang dibutuhkan
      $deliveryOrder = DeliveryOrder::with([
        'packingList.sales_order'
      ])->findOrFail($do_id);

      // Ambil data relasi
      $packingList = $deliveryOrder->packingList;
      $salesOrder = $packingList->sales_order ?? null;

      // Format items
      $items = $packingList->items->map(function ($item) {
        return [
          'id' => $item->id,
          'item_code' => $item->finishedGood->item->item_code ?? 'N/A',
          'item_name' => $item->finishedGood->item->item_name ?? 'N/A',
          'quantity' => $item->quantity,
          'unit' => $item->finishedGood->item->unit ?? 'pcs',
        ];
      });

      // Prepare response data
      $response = [
        'success' => true,
        'do_number' => $deliveryOrder->delivered_no,
        'status' => $deliveryOrder->status,
        'so_number' => $salesOrder->so_number ?? 'N/A',
        'customer_id' => $salesOrder->customer_id ?? 'N/A',
        'customer_name' => $salesOrder->customer->name ?? 'N/A',

        // Delivery Information
        'driver_name' => $deliveryOrder->driver_name,
        'vehicle_number' => $deliveryOrder->vehicle_number,
        'phone_number' => $deliveryOrder->phone_number ?? '-',
        'delivery_date' => $deliveryOrder->delivery_date ?
          \Carbon\Carbon::parse($deliveryOrder->delivery_date)->format('d/m/Y H:i') : 'N/A',
        'notes' => $deliveryOrder->notes,

        // Delivered Information (jika sudah delivered)
        'received_by' => $deliveryOrder->received_by ?? null,
        'delivered_at' => $deliveryOrder->delivered_at ?
          \Carbon\Carbon::parse($deliveryOrder->delivered_at)->format('d/m/Y H:i') : null,

        // Items
        'items' => $items,

        // Timestamps
        'created_at' => $deliveryOrder->created_at->format('d/m/Y H:i'),
        'updated_at' => $deliveryOrder->updated_at->format('d/m/Y H:i'),
      ];

      return response()->json($response, 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Delivery Order tidak ditemukan.'
      ], 404);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat detail delivery order.',
        'error' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
      ], 500);
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

  public function markDelivered(int $do_id): RedirectResponse
  {
    $deliveryOrder = DeliveryOrder::findOrFail($do_id);

    $deliveryOrder->update([
      'status' => 'Delivered',
    ]);

    return redirect()->route('admin.outbound.delivery-orders.index')
      ->with('success', 'Delivery Order berhasil ditandai sebagai Delivered.');
  }

  public function printPDF(int $do_id)
  {
    $deliveryOrder = DeliveryOrder::with(['packingList.salesOrder'])->findOrFail($do_id);

    $pdf = Pdf::loadView('admin.outbound.delivery-orders.pdf', compact('deliveryOrder'));

    return $pdf->stream('delivery-order-' . $deliveryOrder->delivered_no . '.pdf');
  }
}
