<?php

namespace App\Models\Admin\Production;

use App\Models\Admin\Inbound\ItemLabel;
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

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $year = now()->format('Y');
      $month = now()->format('m');

      // Ambil nomor terakhir di bulan & tahun yang sama
      $lastPl = self::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderBy('id', 'desc')
        ->first();

      $lastNumber = 0;
      if ($lastPl && preg_match('/PL-\d{4}-\d{2}-(\d+)/', $lastPl->pl_no, $matches)) {
        $lastNumber = (int) $matches[1];
      }

      $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
      $model->pl_no = "PL-{$year}-{$month}-{$nextNumber}";
    });
  }
}
