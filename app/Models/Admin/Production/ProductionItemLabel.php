<?php

namespace App\Models\Admin\Production;

use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Pallet;
use App\Models\SuperAdmin\MasterData\Rack;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionItemLabel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function location() {
      return $this->belongsTo(Location::class, "location_id");
    }

    public function rack() {
      return $this->belongsTo(Rack::class, "rack_id");
    }

    public function pallet() {
      return $this->belongsTo(Pallet::class, "pallet_id");
    }
}
