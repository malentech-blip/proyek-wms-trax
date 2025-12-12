<?php

namespace App\Models\Admin\Inbound;

use App\Models\User; // <-- Pastikan import ini ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $guarded = []; 

    // Relasi ke Barang
    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    // --- INI BAGIAN YANG HILANG DI FILE ANDA ---
    // Relasi ke User (Penerima)
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }
    // -------------------------------------------
}