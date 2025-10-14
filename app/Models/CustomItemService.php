<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomItemService extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_item_id',
        'service_sku',
        'service_name',
        'cost',
        'quantity',
    ];
}