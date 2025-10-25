<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RejectWarehouseController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory.reject-warehouses.index');
    }
}
