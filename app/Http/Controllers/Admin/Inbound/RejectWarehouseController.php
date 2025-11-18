<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\RejectInbound;
use Illuminate\Http\Request;

class RejectWarehouseController extends Controller
{
    public function index()
    {
        $rejectedItems = RejectInbound::with('goodsReceiptItem')->latest()->paginate(15);

        return view('admin.inbound.reject-warehouse.index', compact('rejectedItems'));
    }
}