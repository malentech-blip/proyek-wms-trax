<?php

namespace App\Models\Admin\Outbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransitInventory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function packingList()
    {
        return $this->belongsTo(PackingList::class, "packing_id");
    }
}
