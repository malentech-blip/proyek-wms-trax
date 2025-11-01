<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\RejectProduction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RejectWarehouseController extends Controller
{
    public function index(): View
    {
        // Livewire component handles all data fetching and filtering
        return view('admin.inventory.reject-warehouses.index');
    }

    public function rework(RejectProduction $rejectProduction): RedirectResponse
    {
        $rejectProduction->update([
            'action' => 'rework',
            'status' => 'rework',
        ]);

        return redirect()
            ->route('admin.inventory.reject-warehouses')
            ->with('success', 'Item berhasil diubah status menjadi rework.');
    }

    public function scrap(RejectProduction $rejectProduction): RedirectResponse
    {
        $rejectProduction->update([
            'action' => 'scrap',
            'status' => 'scrap',
        ]);

        return redirect()
            ->route('admin.inventory.reject-warehouses')
            ->with('success', 'Item berhasil diubah status menjadi scrap.');
    }
}
