<?php

namespace App\Models\SuperAdmin\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// TAMBAHKAN INI
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pallet extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    // TAMBAHKAN METHOD INI
    // Satu Pallet dimiliki oleh satu Rak
    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }
}