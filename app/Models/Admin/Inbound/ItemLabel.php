<?php

namespace App\Models\Admin\Inbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemLabel extends Model
{
    use HasFactory;

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
