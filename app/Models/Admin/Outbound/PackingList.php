<?php

namespace App\Models\Admin\Outbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sales_order()
    {
        return $this->belongsTo(SalesOrder::class, 'so_id');
    }

    public function items() {
      return $this->hasMany(OutboundPackingItem::class, 'packing_id');
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
}
