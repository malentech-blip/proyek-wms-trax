<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // PENYESUAIAN: Import class

class ItemMaterial extends Model
{
    use HasFactory;

    // PENYESUAIAN: Menambahkan guarded untuk mass assignment
    protected $guarded = [];

    // PENYESUAIAN: Menambahkan relasi ke CustomItem
    // Sebuah baris material pasti dimiliki oleh satu CustomItem
    public function customItem(): BelongsTo
    {
        return $this->belongsTo(CustomItem::class);
    }
}