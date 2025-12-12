<?php

namespace App\Models\Admin\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectProduction extends Model
{
    use HasFactory;

    protected $table = 'rejects_production';

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function wip_record() {
      return $this->belongsTo(WipRecord::class, "wip_id");
    }

    public function finishedGoods() {
      return $this->hasManyThrough(
        \App\Models\Admin\Production\FinishedGood::class,
        WipRecord::class,
        'id', // Foreign key on wip_records table
        'wip_id', // Foreign key on finished_goods table
        'wip_id', // Local key on rejects_production table
        'id' // Local key on wip_records table
      );
    }
}
