<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomItem extends Model
{
    use HasFactory;
    
    // Memastikan semua field bisa diisi secara massal (mass assignable)
    protected $guarded = [];

    // Relasi ke bahan-bahan yang menyusun item ini
    public function materials(): HasMany
    {
        return $this->hasMany(ItemMaterial::class);
    }

    // PENYESUAIAN: Menambahkan relasi ke biaya manual
    public function manualCosts(): HasMany
    {
        return $this->hasMany(CustomItemManualCost::class);
    }

    public function services()
    {
        return $this->hasMany(CustomItemService::class);
    }
}