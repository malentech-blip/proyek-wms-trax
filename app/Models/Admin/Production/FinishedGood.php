<?php

namespace App\Models\Admin\Production;

use App\Models\SuperAdmin\MasterData\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGood extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wip_record(){
        return $this->belongsTo(WipRecord::class, "wip_id");
    }

    public function item() {
      return $this->belongsTo(Item::class, "item_id");
    }

    public function production_item_label() {
      return $this->belongsTo(ProductionItemLabel::class, "label_id");
    }
}
