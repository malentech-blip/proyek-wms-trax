<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // PENYESUAIAN: Import class

class QuotationItem extends Model
{
    use HasFactory;
    
    // PENYESUAIAN: Menambahkan guarded untuk mass assignment
    protected $guarded = [];

    // Relasi ke item custom yang ada di penawaran
    public function customItem(): BelongsTo
    {
        return $this->belongsTo(CustomItem::class);
    }
}