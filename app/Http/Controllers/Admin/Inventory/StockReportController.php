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
    public function index(Request $request): View
    {
        // Get all stock reports with item and location
        $query = Inventory::query()->with('item', 'location')->latest();

        // Filtering by location
        if ($request->filled('location_id') && $request->location_id !== 'all') {
            $query->where('location_id', $request->location_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        $stockReports = $query->paginate(15)->appends($request->query());

        // Get unique locations for the filter dropdown
        $locations = \App\Models\SuperAdmin\MasterData\Location::all();

        return view('admin.inventory.stock-reports.index', compact('stockReports', 'locations'));
    }

    public function export()
    {
        return Excel::download(new InventoryExport, 'stock-reports.xlsx');
    }
}

