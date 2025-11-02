<?php

namespace App\Models\Admin\Outbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'packing_list_id',
        'delivered_no',
        'driver_name',
        'delivery_date',
        'status',
    ];

    public function packingList()
    {
        return $this->belongsTo(PackingList::class);
    }
}
