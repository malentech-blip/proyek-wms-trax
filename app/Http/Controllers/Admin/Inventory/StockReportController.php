<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Exports\InventoryExport;
use App\Http\Controllers\Controller;
use App\Models\Admin\Inventory\Inventory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockReportController extends Controller
{
    public function index(): View
    {
        // Livewire component handles all data fetching and filtering
        return view('admin.inventory.stock-reports.index');
    }

    public function export()
    {
        return Excel::download(new InventoryExport, 'stock-reports.xlsx');
    }
}

