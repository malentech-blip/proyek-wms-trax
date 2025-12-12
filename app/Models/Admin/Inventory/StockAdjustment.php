<?php

namespace App\Models\Admin\Inventory;

use App\Models\User;

/**
 * Stock Adjustment Model
 *
 * Handles inventory stock adjustments with approval workflow.
 *
 * Note: Email notification feature is currently disabled for the 2 PM demonstration.
 * The system now uses SweetAlert2 notifications instead of actual email sending.
 * TODO: Re-enable email notifications after demo - notifications should be sent when:
 * - Stock adjustment requires approval (requires_approval = true)
 * - Stock adjustment is approved/rejected (status changes)
 * - Stock adjustment is completed
 */
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'location_id',
        'system_quantity',
        'physical_quantity',
        'difference',
        'reason',
        'adjusted_by',
        'requires_approval',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function adjustedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
