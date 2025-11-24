<?php

namespace App\Models\Admin\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'wip_id',
        'raw_material_cost',
        'labor_cost',
        'overhead_cost',
        'total_cost',
        'hpp_actual_per_unit',
        'variance_nominal',
        'variance_percentage',
        'variance_status',
    ];

    protected $casts = [
        'raw_material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'hpp_actual_per_unit' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
    ];

    /**
     * Relasi ke WIP Record
     */
    public function wipRecord()
    {
        return $this->belongsTo(WipRecord::class, 'wip_id');
    }
}
