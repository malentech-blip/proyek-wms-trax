<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Production\RejectProduction;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RejectWarehouseController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $this->logActivity('View Reject Warehouse', 'Inventory');

        // Livewire component handles all data fetching and filtering
        return view('admin.inventory.reject-warehouses.index');
    }

    public function rework(RejectProduction $rejectProduction): RedirectResponse
    {
        $rejectProduction->update([
            'action' => 'rework',
            'status' => 'rework',
        ]);

        $this->logActivity('Mark Reject as Rework', 'Inventory');

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

        $this->logActivity('Mark Reject as Scrap', 'Inventory');

        return redirect()
            ->route('admin.inventory.reject-warehouses')
            ->with('success', 'Item berhasil diubah status menjadi scrap.');
    }
}
