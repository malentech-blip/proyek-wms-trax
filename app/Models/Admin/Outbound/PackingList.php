<?php

namespace App\Models\Admin\Outbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_id',
        'packed_by',
        'packed_at',
        'status',
    ];

    public function sales_order()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
}
