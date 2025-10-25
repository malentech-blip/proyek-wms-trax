<?php

namespace App\Models\Admin\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'location_id', 'quantity'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
