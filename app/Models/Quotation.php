<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Pastikan ini di-import

class Quotation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'accurate_customer_id',
        'customer_no', 
        'status',
        'notes',
        'company_name',
        'phone',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function getGrandTotalAttribute(): float
    {
        return $this->items->reduce(function ($carry, $item) {
            return $carry + ($item->quantity * $item->unit_price);
        }, 0);
    }
}