<?php

namespace App\Models\Admin\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WipRecord extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['started_at', 'finished_at'];

    public function material_request()
    {
        return $this->belongsTo(MaterialRequest::class, 'mr_id');
    }

    public function finishedGoods()
    {
        return $this->hasMany(FinishedGood::class, 'wip_id');
    }

    public function manufactureCost()
    {
        return $this->hasOne(ManufactureCost::class, 'wip_id');
    }

    protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $year = now()->format('Y');
      $month = now()->format('m');

      // Ambil nomor terakhir di bulan & tahun yang sama
      $lastWIP = self::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->orderBy('id', 'desc')
        ->first();

      $lastNumber = 0;
      if ($lastWIP && preg_match('/WIP-\d{4}-\d{2}-(\d+)/', $lastWIP->wip_no, $matches)) {
        $lastNumber = (int) $matches[1];
      }

      $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
      $model->wip_no = "WIP-{$year}-{$month}-{$nextNumber}";
    });
  }
}
