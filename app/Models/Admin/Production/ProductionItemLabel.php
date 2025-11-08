<?php

namespace App\Models\Admin\Production;

use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Pallet;
use App\Models\SuperAdmin\MasterData\Rack;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;


class ProductionItemLabel extends Model
{
  use HasFactory;

  protected $guarded = [];

  public function item(): BelongsTo
  {
    return $this->belongsTo(Item::class, 'item_id');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo(Location::class, 'location_id');
  }

  public function rack(): BelongsTo
  {
    return $this->belongsTo(Rack::class, 'rack_id');
  }

  public function pallet(): BelongsTo
  {
    return $this->belongsTo(Pallet::class, 'pallet_id');
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($label) {
      if (empty($label->barcode)) {
        // Ambil 7 karakter pertama dari UUID (tanpa tanda strip)
        $uuid = str_replace('-', '', (string) Str::uuid());
        $label->barcode = strtoupper(substr($uuid, 0, 7));
      }
    });
  }
}
