<?php

namespace App\Models\SuperAdmin\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// TAMBAHKAN INI
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rack extends Model
{
    use HasFactory;

    protected $guarded = [];

    // TAMBAHKAN METHOD-METHOD INI
    // Satu Rak dimiliki oleh satu Lokasi
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    // Satu Rak memiliki banyak Pallet
    public function pallets(): HasMany
    {
        return $this->hasMany(Pallet::class);
    }
}