<?php

namespace App\Models\Admin\Inbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $guarded = []; // Agar semua field bisa diisi

    public function items(): HasMany
    {
        // 2. HAPUS "Related:" yang salah dari sini
        return $this->hasMany(GoodsReceiptItem::class);
    }
}