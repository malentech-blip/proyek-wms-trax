<?php

namespace App\Models\Admin\Outbound;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_number',
        'status',
        'sync_status',
    ];

    public function packingLists()
    {
        return $this->hasMany(PackingList::class);
    }
}
