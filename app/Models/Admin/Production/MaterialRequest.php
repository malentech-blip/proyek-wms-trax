<?php

namespace App\Models\Admin\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'material_requests';
    protected $primaryKey = 'id';

    public function pickingList()
    {
        return $this->hasOne(PickingList::class, 'mr_id', 'id');
    }
}
