<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\ItemLabel;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Models\SuperAdmin\MasterData\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Calculate Total Stock
        $totalStock = Inventory::sum('quantity');

        // Calculate Low Stock Alert (items with quantity <= 10 or less than 5% of average)
        $lowStockThreshold = 10;
        $lowStockItems = Inventory::with('item')
            ->get()
            ->filter(function ($inventory) use ($lowStockThreshold) {
                return $inventory->quantity <= $lowStockThreshold;
            })
            ->count();

        // Calculate Stock Value per Category (by item_type)
        $stockValueByCategory = Inventory::with('item')
            ->get()
            ->groupBy(function ($inventory) {
                return $inventory->item->item_type ?? 'Unknown';
            })
            ->map(function ($group) {
                // Calculate total value (using quantity as value since unit_price doesn't exist)
                // In real scenario, this would be: quantity * unit_price
                return $group->sum('quantity');
            });

        // Calculate total for percentage calculation
        $totalStockValue = $stockValueByCategory->sum();

        // Convert to percentages
        $stockPercentageByCategory = $stockValueByCategory->map(function ($value) use ($totalStockValue) {
            if ($totalStockValue > 0) {
                return round(($value / $totalStockValue) * 100, 2);
            }
            return 0;
        });

        // Prepare chart data
        $chartData = [
            'categories' => $stockPercentageByCategory->keys()->toArray(),
            'percentages' => $stockPercentageByCategory->values()->toArray(),
            'values' => $stockValueByCategory->values()->toArray(),
        ];

        return view('admin.inventory.dashboard', [
            'totalStock' => $totalStock,
            'lowStockAlert' => $lowStockItems,
            'chartData' => $chartData,
        ]);
    }

    public function scanQR(Request $request): JsonResponse
    {
        $qrCode = $request->input('qr_code');

        if (empty($qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR code tidak ditemukan.',
            ], 400);
        }

        // Search in ItemLabel (inbound)
        $itemLabel = ItemLabel::with(['location', 'rack', 'pallet'])
            ->where('qr_code', $qrCode)
            ->first();

        if ($itemLabel) {
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'inbound',
                    'item_code' => $itemLabel->item_code,
                    'item_name' => $itemLabel->item_name,
                    'quantity' => $itemLabel->quantity,
                    'batch_no' => $itemLabel->batch_no,
                    'location' => $itemLabel->location ? $itemLabel->location->name : 'N/A',
                    'rack' => $itemLabel->rack ? $itemLabel->rack->code : 'N/A',
                    'pallet' => $itemLabel->pallet ? $itemLabel->pallet->code : 'N/A',
                    'status' => $itemLabel->status,
                ],
            ]);
        }

        // Search in ProductionItemLabel
        $productionLabel = ProductionItemLabel::with(['item', 'location', 'rack', 'pallet'])
            ->where('qr_code', $qrCode)
            ->first();

        if ($productionLabel) {
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'production',
                    'item_code' => $productionLabel->item?->item_code ?? 'N/A',
                    'item_name' => $productionLabel->item?->item_name ?? 'N/A',
                    'quantity' => $productionLabel->quantity,
                    'batch_no' => $productionLabel->batch_no,
                    'location' => $productionLabel->location ? $productionLabel->location->name : 'N/A',
                    'rack' => $productionLabel->rack ? $productionLabel->rack->code : 'N/A',
                    'pallet' => $productionLabel->pallet ? $productionLabel->pallet->code : 'N/A',
                    'status' => $productionLabel->status,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'QR code tidak ditemukan dalam sistem.',
        ], 404);
    }
}
