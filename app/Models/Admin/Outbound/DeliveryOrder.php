<?php

namespace App\Models\Admin\Outbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
  use HasFactory;

  protected $guarded = [];

  public function packingList()
  {
    return $this->belongsTo(PackingList::class, "packing_id");
  }

  public static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (empty($model->delivered_no)) {
        $model->delivered_no = self::generateNumber();
      }
    });
  }

  public static function generateNumber()
  {
    $prefix = 'DO-' . now()->format('Y-m');

    $last = self::where('delivered_no', 'like', "{$prefix}%")
      ->latest('id')
      ->value('delivered_no');

    $nextNumber = $last ? (int) substr($last, -4) + 1 : 1;

    return sprintf('%s-%04d', $prefix, $nextNumber);
  }
}
