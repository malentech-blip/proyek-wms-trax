<?php

namespace App\Models\Admin\Inbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectInbound extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Opsional: Definisikan relasi jika perlu
    public function goodsReceiptItem()
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }
}