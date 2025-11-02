<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;

class StockAdjusmentController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $this->logActivity('View Stock Adjustment', 'Inventory');

        return view('admin.inventory.stock-adjustment.index');
    }
}
