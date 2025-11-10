<?php

namespace App\Models\Admin\Production;

use App\Models\Admin\Outbound\SalesOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $table = 'material_requests';
  protected $primaryKey = 'id';

  public function pickingList()
  {
    return $this->hasMany(PickingList::class, 'mr_id', 'id');
  }
  public function salesOrder() {
    return $this->belongsTo(SalesOrder::class, 'so_id', 'id');
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $year = now()->format('Y');
      $month = now()->format('m');

      // Ambil nomor terakhir di bulan & tahun yang sama
      $lastMr = self::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderBy('id', 'desc')
        ->first();

      $lastNumber = 0;
      if ($lastMr && preg_match('/MR-\d{4}-\d{2}-(\d+)/', $lastMr->mr_no, $matches)) {
        $lastNumber = (int) $matches[1];
      }

      $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
      $model->mr_no = "MR-{$year}-{$month}-{$nextNumber}";
    });
  }
}
