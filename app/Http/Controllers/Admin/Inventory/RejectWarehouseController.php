<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\RejectProduction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RejectWarehouseController extends Controller
{
    public function index(Request $request): View
    {
        $query = RejectProduction::query();

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by item name
        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }

        $rejectProductions = $query->latest()->paginate(15);

        return view('admin.inventory.reject-warehouses.index', compact('rejectProductions'));
    }

    public function rework(RejectProduction $rejectProduction): RedirectResponse
    {
        $rejectProduction->update(['status' => RejectProduction::STATUS_REWORK]);

        return redirect()
            ->route('admin.inventory.reject-warehouses')
            ->with('success', 'Item berhasil diubah status menjadi rework.');
    }

    public function scrap(RejectProduction $rejectProduction): RedirectResponse
    {
        $rejectProduction->update(['status' => RejectProduction::STATUS_SCRAP]);

        return redirect()
            ->route('admin.inventory.reject-warehouses')
            ->with('success', 'Item berhasil diubah status menjadi scrap.');
    }
}
