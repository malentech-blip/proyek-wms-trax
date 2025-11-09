<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Outbound\OutboundPackingItem;
use App\Models\Admin\Outbound\PackingList;
use Illuminate\Http\Request;

class TransitInventoryController extends Controller
{
  public function index()
  {
    $packingLists = PackingList::with(['sales_order', 'items'])->where('status', '!=' , 'Packed')->get();
    return view('admin.outbound.transit-inventory.index', compact('packingLists'));
  }

  public function detail(int $packing_id) {
    $packingList = PackingList::where([
      ['id', $packing_id],
    ])->with(['sales_order', 'items'])->first();
    return view('admin.outbound.transit-inventory.detail', compact('packingList'));
  }
}
