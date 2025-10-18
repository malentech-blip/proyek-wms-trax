<?php

namespace App\Models\Admin\Production;

use App\Models\Inventory;
use App\Models\SuperAdmin\MasterData\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingList extends Model
{
  use HasFactory;

  protected $guarded = [];
  public function materialRequest()
  {
    return $this->belongsTo(MaterialRequest::class);
  }

  public function item()
  {
    return $this->belongsTo(Item::class);
  }
  public function inventory()
  {
    return $this->hasOne(Inventory::class, 'item_id', 'item_id');
  }
}
