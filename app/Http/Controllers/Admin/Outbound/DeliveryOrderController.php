<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = DeliveryOrder::with(['packingList.salesOrder'])
            ->latest();

        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
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

        return $pdf->stream('delivery-order-'.$deliveryOrder->delivered_no.'.pdf');
    }
}
