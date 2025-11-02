<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $pendingSalesOrders = SalesOrder::where('status', 'PENDING')->count();
        $packingInProgress = PackingList::where('status', 'WIP')->count();
        $todayShipments = DeliveryOrder::whereDate('delivery_date', today())->count();

        return view('admin.outbound.dashboard', compact(
            'pendingSalesOrders',
            'packingInProgress',
            'todayShipments'
        ));
    }
}
