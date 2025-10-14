<?php

namespace App\Models\Admin\Inbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'goods_receipt_id',
        'item_name',
        'item_code',
        'expected_qty',
        'received_qty',
    ];
}
