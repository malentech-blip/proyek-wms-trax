<?php

namespace App\Models\SuperAdmin\MasterData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan semua field diisi

    public function inventories(): HasMany
    {
        return $this->hasMany(\App\Models\Admin\Inventory\Inventory::class);
    }
}
