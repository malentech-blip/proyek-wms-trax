<?php

namespace App\Models\Admin\Inbound;

use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Pallet;
use App\Models\SuperAdmin\MasterData\Rack;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemLabel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'goods_receipt_item_id',
        'item_name',
        'item_code',
        'quantity',
        'qr_code',
        'location_id',
        'rack_id',
        'pallet_id',
        'status',
    ];

    /**
     * Get the GoodsReceiptItem that owns the ItemLabel.
     */
    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    /**
     * Get the Location that owns the ItemLabel.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the Rack that owns the ItemLabel.
     */
    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }

    /**
     * Get the Pallet that owns the ItemLabel.
     */
    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }
}
