<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;
    protected $fillable = ['item_id', 'quantity', 'from_location', 'to_location', 'moved_by', 'movement_type', 'date'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
