<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // PENYESUAIAN: Import class

class CustomItemManualCost extends Model
{
    use HasFactory;

    // PENYESUAIAN: Menambahkan guarded untuk mass assignment
    protected $guarded = [];

    // PENYESUAIAN: Memperbaiki relasi yang salah
    // Relasi sebelumnya (hasMany ke diri sendiri) tidak benar.
    // Seharusnya, sebuah biaya manual dimiliki oleh satu CustomItem.
    public function customItem(): BelongsTo
    {
        return $this->belongsTo(CustomItem::class);
    }
}