<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;

class MoveStockController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $this->logActivity('View Move Stock', 'Inventory');

        return view('admin.inventory.move-stock.index');
    }
}
