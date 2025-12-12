<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Ringkasan Cards
        $pendingSalesOrders = SalesOrder::where('status', 'PENDING')->count();
        $packingInProgress = PackingList::where('status', 'Packed')->count();
        $todayShipments = DeliveryOrder::whereDate('delivery_date', today())->count();

        // Chart Data - Outbound Trend (Last 7 Days)
        $chartData = $this->getOutboundTrendData();

        return view('admin.outbound.dashboard', compact(
            'pendingSalesOrders',
            'packingInProgress',
            'todayShipments',
            'chartData'
        ));
    }

    /**
     * Get Outbound Trend Data for Chart
     * 
     * @return array
     */
    private function getOutboundTrendData(): array
    {
        $days = 7; // Last 7 days
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Count Packing Lists created
            $packingListsCreated = PackingList::whereDate('created_at', $date)->count();
            
            // Count Delivery Orders created
            $deliveryOrdersCreated = DeliveryOrder::whereDate('created_at', $date)->count();
            
            // Count Delivered (status = Delivered)
            $delivered = DeliveryOrder::with("packingList")->where("status", "Delivered")->whereDate('delivery_date', $date)->count();
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('D, M j'), // Mon, Jan 15
                'packing_lists' => $packingListsCreated,
                'delivery_orders' => $deliveryOrdersCreated,
                'delivered' => $delivered,
            ];
        }

        return $data;
    }

    /**
     * Get Chart Data via API (optional, untuk dynamic update)
     */
    public function getChartData()
    {
        try {
            $chartData = $this->getOutboundTrendData();
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data'
            ], 500);
        }
    }
}