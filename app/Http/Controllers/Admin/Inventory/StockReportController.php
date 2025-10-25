<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\Admin\Inventory\Inventory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;

class StockReportController extends Controller
{
    public function index(Request $request): View
    {
        // Get all stock reports with item and location
        $query = Inventory::with('item', 'location')->latest();

        // Filtering by location
        if ($request->filled('search')) {
            $query->where('location_id', $request->location_id);
        }

        $stockReports = $query->paginate(15)->withQueryString();



        return view('admin.inventory.stock-reports.index', compact('stockReports'));
    }

    public function export()
    {
        return Excel::download(new InventoryExport, 'stock-reports.xlsx');
    }
}
