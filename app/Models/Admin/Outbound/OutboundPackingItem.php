<?php

namespace App\Models\Admin\Outbound;

use App\Models\Admin\Production\FinishedGood;
use App\Models\SuperAdmin\MasterData\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutboundPackingItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function packing_list() {
      return $this->belongsTo(PackingList::class, 'packing_id');  
    }

    public function finished_good() {
      return $this->belongsTo(FinishedGood::class, 'fg_id');
    }
}
