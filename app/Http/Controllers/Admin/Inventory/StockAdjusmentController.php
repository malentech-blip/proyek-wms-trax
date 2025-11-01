<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class StockAdjusmentController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory.stock-adjustment.index');
    }
}
