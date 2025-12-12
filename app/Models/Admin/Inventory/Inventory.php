<?php

namespace App\Models\Admin\Inventory;

use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Inventory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['item_id', 'location_id', 'quantity', 'last_synced_at'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Relationship with Item (Master Data)
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship with Location (Master Data)
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Sync inventory with Accurate data
     */
    public static function syncWithAccurate(array $accurateItem, Location $location): self
    {
        // Find existing inventory record
        $inventory = self::where('item_id', $accurateItem['item_id'] ?? null)
            ->where('location_id', $location->id)
            ->first();

        $accurateStock = (float) ($accurateItem['stock'] ?? 0);

        if ($inventory) {
            // Update existing inventory
            $oldQuantity = $inventory->quantity;
            $inventory->update([
                'quantity' => $accurateStock,
                'last_synced_at' => now(),
            ]);

            Log::info('Inventory updated via Accurate sync', [
                'item_code' => $accurateItem['no'] ?? 'unknown',
                'location' => $location->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $accurateStock,
            ]);

            return $inventory;
        } else {
            // Create new inventory record
            $inventory = self::create([
                'item_id' => $accurateItem['item_id'],
                'location_id' => $location->id,
                'quantity' => $accurateStock,
                'last_synced_at' => now(),
            ]);

            Log::info('Inventory created via Accurate sync', [
                'item_code' => $accurateItem['no'] ?? 'unknown',
                'location' => $location->name,
                'quantity' => $accurateStock,
            ]);

            return $inventory;
        }
    }

    /**
     * Get total inventory quantity for an item across all locations
     */
    public static function getTotalQuantityForItem(int $itemId): float
    {
        return self::where('item_id', $itemId)->sum('quantity');
    }

    /**
     * Get inventory quantity for an item at a specific location
     */
    public static function getQuantityAtLocation(int $itemId, int $locationId): float
    {
        return self::where('item_id', $itemId)
            ->where('location_id', $locationId)
            ->value('quantity') ?? 0;
    }

    /**
     * Check if item has sufficient stock at location
     */
    public static function hasSufficientStock(int $itemId, int $locationId, float $requiredQuantity): bool
    {
        $availableQuantity = self::getQuantityAtLocation($itemId, $locationId);

        return $availableQuantity >= $requiredQuantity;
    }

    /**
     * Adjust inventory quantity
     */
    public function adjustQuantity(float $adjustment, ?string $reason = null): bool
    {
        $oldQuantity = $this->quantity;
        $newQuantity = $oldQuantity + $adjustment;

        // Prevent negative inventory
        if ($newQuantity < 0) {
            Log::warning('Inventory adjustment would result in negative quantity', [
                'inventory_id' => $this->id,
                'item_id' => $this->item_id,
                'location_id' => $this->location_id,
                'old_quantity' => $oldQuantity,
                'adjustment' => $adjustment,
                'reason' => $reason,
            ]);

            return false;
        }

        $this->update(['quantity' => $newQuantity]);

        // Log the adjustment
        $this->logActivity(
            "Inventory adjusted: {$oldQuantity} → {$newQuantity} ({$adjustment})",
            'Inventory',
            ['reason' => $reason]
        );

        return true;
    }

    /**
     * Get inventory items that need syncing (not synced recently)
     */
    public static function getItemsNeedingSync(int $hoursOld = 24): \Illuminate\Database\Eloquent\Collection
    {
        return self::where(function ($query) use ($hoursOld) {
            $query->whereNull('last_synced_at')
                ->orWhere('last_synced_at', '<', now()->subHours($hoursOld));
        })->with(['item', 'location'])->get();
    }

    /**
     * Scope for active inventories (quantity > 0)
     */
    public function scopeActive($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query, float $threshold = 10)
    {
        return $query->where('quantity', '<=', $threshold);
    }
}
