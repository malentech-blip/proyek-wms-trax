<?php

namespace App\Models\SuperAdmin\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// TAMBAHKAN INI
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $guarded = [];

    // TAMBAHKAN METHOD INI
    // Satu Lokasi memiliki banyak Rak
    public function racks(): HasMany
    {
        return $this->hasMany(Rack::class);
    }
}