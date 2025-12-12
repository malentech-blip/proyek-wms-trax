<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Exports\InventoryExport;
use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StockReportController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $this->logActivity('View Stock Reports', 'Inventory');

        // Livewire component handles all data fetching and filtering
        return view('admin.inventory.stock-reports.index');
    }

    public function export()
    {
        $this->logActivity('Export Stock Reports', 'Inventory');

        return Excel::download(new InventoryExport, 'stock-reports.xlsx');
    }
}
