<?php

namespace App\Models\Admin\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectProduction extends Model
{
    use HasFactory;

    protected $table = 'rejects_production';

    protected $guarded = [];
}
