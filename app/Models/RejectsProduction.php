<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RejectsProduction extends Model
{
    use HasFactory;
    protected $fillable = ['wip_id', 'reason', 'handled_by', 'date'];

    public function wip(): BelongsTo
    {
        return $this->belongsTo(WipsRecord::class);
    }
}
