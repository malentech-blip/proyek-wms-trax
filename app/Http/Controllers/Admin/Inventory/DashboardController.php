<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\ItemLabel;
use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Services\InventorySyncService;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    use LogsActivity;

    protected InventorySyncService $inventorySyncService;

    public function __construct(InventorySyncService $inventorySyncService)
    {
        $this->inventorySyncService = $inventorySyncService;
    }

    public function index(): View
    {
        $this->logActivity('View Inventory Dashboard', 'Inventory');
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
        $this->logActivity('Scan QR Code', 'Inventory');

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

    /**
     * Sync inventory with Accurate master data
     */
    public function syncWithAccurate(Request $request): JsonResponse
    {
        $this->logActivity('Sync Inventory with Accurate', 'Inventory');

        try {
            // Validate session has Accurate connection
            if (! $this->inventorySyncService->validateAccurateConnection()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi Accurate belum diatur. Silakan hubungkan Accurate terlebih dahulu.',
                ], 400);
            }

            // Determine if this is a dry run
            $isDryRun = $request->has('dry_run') && $request->boolean('dry_run');

            // Perform the sync using the service
            $results = $this->inventorySyncService->syncWithAccurate($isDryRun);

            if ($results['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sinkronisasi inventory dengan Accurate berhasil.',
                    'results' => $results,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Sinkronisasi inventory gagal.',
                    'results' => $results,
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Inventory sync error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get inventory sync status
     */
    public function syncStatus(Request $request): JsonResponse
    {
        try {
            $syncStats = $this->inventorySyncService->getSyncStatistics();

            return response()->json([
                'success' => true,
                'data' => $syncStats,
            ]);
        } catch (\Exception $e) {
            Log::error('Sync status error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status sinkronisasi: '.$e->getMessage(),
            ], 500);
        }
    }
}
