<?php

namespace App\Http\Controllers\Admin\Outbound;

use App\Http\Controllers\Controller;
use App\Services\AccurateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PackingListController extends Controller
{
    public function create(Request $request, AccurateService $accurate): View
    {
        $soId = (int) $request->get('so_id');
        $salesOrder = $soId ? $accurate->getSalesOrderDetail($soId) : null;

        return view('admin.outbound.packing-lists.create', [
            'salesOrder' => $salesOrder,
        ]);
    }
}


