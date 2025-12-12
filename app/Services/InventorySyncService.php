<?php

namespace App\Services;

use App\Models\Admin\Inventory\Inventory;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventorySyncService
{
    protected AccurateService $accurateService;

    public function __construct(AccurateService $accurateService)
    {
        $this->accurateService = $accurateService;
    }

    /**
     * Sync inventory data with Accurate
     *
     * @param  bool  $dryRun  Whether to perform a dry run (no actual changes)
     * @return array Sync results and statistics
     */
    public function syncWithAccurate(bool $dryRun = false): array
    {
        $results = [
            'success' => false,
            'synced_count' => 0,
            'skipped_count' => 0,
            'errors' => [],
            'logs' => [],
            'dry_run' => $dryRun,
        ];

        try {
            Log::info('Starting inventory sync with Accurate', ['dry_run' => $dryRun]);

            // Get raw materials from Accurate
            $rawMaterials = $this->accurateService->getRawMaterials(request());

            if ($rawMaterials->isEmpty()) {
                $results['logs'][] = '⚠️ No raw materials found in Accurate. Skipping sync.';
                Log::warning('No raw materials found in Accurate during sync');

                return $results;
            }

            $results['logs'][] = "📊 Found {$rawMaterials->count()} raw materials in Accurate";

            // Get default location for inventory
            $defaultLocation = Location::where('is_active', true)->first();
            if (! $defaultLocation) {
                throw new Exception('No active location found. Please create at least one location first.');
            }

            $results['logs'][] = "🏢 Using default location: {$defaultLocation->name}";

            DB::beginTransaction();

            foreach ($rawMaterials as $accurateItem) {
                try {
                    $syncResult = $this->syncInventoryItem($accurateItem, $defaultLocation, $dryRun);

                    if ($syncResult['action'] === 'synced') {
                        $results['synced_count']++;
                        $results['logs'][] = $syncResult['message'];
                    } elseif ($syncResult['action'] === 'skipped') {
                        $results['skipped_count']++;
                        $results['logs'][] = $syncResult['message'];
                    }

                } catch (Exception $e) {
                    $errorMsg = "Error syncing item {$accurateItem['no']}: {$e->getMessage()}";
                    $results['errors'][] = $errorMsg;
                    $results['logs'][] = "❌ {$errorMsg}";
                    Log::error('Inventory sync error', [
                        'item_code' => $accurateItem['no'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (! $dryRun) {
                DB::commit();
                $results['logs'][] = '✅ Transaction committed successfully';
            } else {
                DB::rollBack();
                $results['logs'][] = '🔄 Dry run completed - transaction rolled back';
            }

            $results['success'] = true;
            $results['logs'][] = "📈 Sync Summary: {$results['synced_count']} synced, {$results['skipped_count']} skipped";

            Log::info('Inventory sync completed', [
                'dry_run' => $dryRun,
                'synced_count' => $results['synced_count'],
                'skipped_count' => $results['skipped_count'],
                'errors_count' => count($results['errors']),
            ]);

        } catch (Exception $e) {
            if (! $dryRun) {
                DB::rollBack();
            }

            $results['errors'][] = $e->getMessage();
            $results['logs'][] = "❌ Sync failed: {$e->getMessage()}";

            Log::error('Inventory sync command failed', [
                'error' => $e->getMessage(),
                'dry_run' => $dryRun,
            ]);
        }

        return $results;
    }

    /**
     * Sync a single inventory item
     *
     * @param  array  $accurateItem  Item data from Accurate
     * @param  Location  $defaultLocation  Default location for inventory
     * @param  bool  $dryRun  Whether this is a dry run
     * @return array Sync result for this item
     */
    protected function syncInventoryItem(array $accurateItem, Location $defaultLocation, bool $dryRun): array
    {
        // Find or create item in master data
        $item = Item::where('item_code', $accurateItem['no'])->first();

        if (! $item) {
            // Create item if it doesn't exist
            if (! $dryRun) {
                $item = Item::create([
                    'item_code' => $accurateItem['no'],
                    'item_name' => $accurateItem['name'],
                    'item_type' => 'Raw Material', // Default to Raw Material
                    'uom' => $accurateItem['unit'] ?? 'PCS',
                    'is_active' => true,
                ]);
                $message = "➕ Created item: {$accurateItem['no']} - {$accurateItem['name']}";
            } else {
                $message = "➕ Would create item: {$accurateItem['no']} - {$accurateItem['name']}";
                // For dry run, create a temporary item object
                $item = (object) [
                    'id' => null,
                    'item_code' => $accurateItem['no'],
                    'item_name' => $accurateItem['name'],
                ];
            }
        }

        $accurateStock = (float) ($accurateItem['stock'] ?? 0);

        // Prepare accurate item data for sync method
        $accurateItemData = array_merge($accurateItem, [
            'item_id' => $item->id,
            'stock' => $accurateStock,
        ]);

        if (! $dryRun) {
            // Use the model's sync method
            $inventory = Inventory::syncWithAccurate($accurateItemData, $defaultLocation);

            // Determine if it was created or updated
            $wasCreated = $inventory->wasRecentlyCreated;
            if ($wasCreated) {
                $message = "🆕 Created inventory: {$accurateItem['no']} - stock: {$accurateStock}";
            } else {
                $message = "📝 Updated inventory: {$accurateItem['no']} - stock: {$accurateStock}";
            }
            $action = 'synced';
        } else {
            // For dry run, just check what would happen
            $existingInventory = Inventory::where('item_id', $item->id)
                ->where('location_id', $defaultLocation->id)
                ->first();

            if ($existingInventory) {
                if ($existingInventory->quantity != $accurateStock) {
                    $message = "📝 Would update inventory: {$accurateItem['no']} - {$existingInventory->quantity} → {$accurateStock}";
                    $action = 'synced';
                } else {
                    $message = "⏭️ Would skip (no change): {$accurateItem['no']} - stock: {$accurateStock}";
                    $action = 'skipped';
                }
            } else {
                $message = "🆕 Would create inventory: {$accurateItem['no']} - stock: {$accurateStock}";
                $action = 'synced';
            }
        }

        return [
            'action' => $action,
            'message' => $message,
            'item_code' => $accurateItem['no'],
            'stock' => $accurateStock,
        ];
    }

    /**
     * Get inventory sync statistics
     *
     * @return array Current sync statistics
     */
    public function getSyncStatistics(): array
    {
        try {
            $lastSyncedItems = Inventory::with(['item', 'location'])
                ->whereNotNull('last_synced_at')
                ->orderBy('last_synced_at', 'desc')
                ->take(10)
                ->get();

            $itemsNeedingSync = Inventory::getItemsNeedingSync(24)->count();

            return [
                'success' => true,
                'total_inventory_items' => Inventory::count(),
                'last_synced_count' => $lastSyncedItems->count(),
                'needing_sync_count' => $itemsNeedingSync,
                'last_synced_items' => $lastSyncedItems->map(function ($inventory) {
                    return [
                        'item_code' => $inventory->item->item_code,
                        'item_name' => $inventory->item->item_name,
                        'location' => $inventory->location->name,
                        'quantity' => $inventory->quantity,
                        'last_synced_at' => $inventory->last_synced_at?->diffForHumans(),
                    ];
                }),
            ];

        } catch (Exception $e) {
            Log::error('Error getting sync statistics', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate if Accurate connection is available
     *
     * @return bool True if connection is available
     */
    public function validateAccurateConnection(): bool
    {
        try {
            return session()->has('accurate_access_token') && session()->has('accurate_database');
        } catch (Exception $e) {
            Log::error('Error validating Accurate connection', ['error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Get items that need synchronization
     *
     * @param  int  $hoursOld  Hours since last sync to consider as needing sync
     * @return Collection Items needing sync
     */
    public function getItemsNeedingSync(int $hoursOld = 24): Collection
    {
        return Inventory::getItemsNeedingSync($hoursOld);
    }
}
