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

<<<<<<< HEAD
    // --- BAGIAN INI YANG KURANG ---
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
    // ------------------------------

    // Relasi (Pastikan ini juga ada agar Cetak Label tidak error nantinya)
    public function goodsReceiptItem()
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function pallet()
    {
        return $this->belongsTo(Pallet::class);
    }
}
=======
    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SuperAdmin\MasterData\Location::class);
    }

    public function rack(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SuperAdmin\MasterData\Rack::class);
    }

    public function pallet(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SuperAdmin\MasterData\Pallet::class);
    }
}
>>>>>>> f29551d0f0ba841ddfaf1592c962d87598634beb
