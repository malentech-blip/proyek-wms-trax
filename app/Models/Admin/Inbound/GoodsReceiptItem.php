<?php

namespace App\Models\Admin\Inbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'passed_qty', // <--- TAMBAHKAN INI
    ];
    
    // Opsional: Tambahkan relasi jika belum ada
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }
public function itemLabels(): HasMany
    {
        return $this->hasMany(ItemLabel::class);
    }
    
}