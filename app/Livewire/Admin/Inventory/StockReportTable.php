<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Inventory\Inventory;
use App\Models\SuperAdmin\MasterData\Location;
use Livewire\Component;
use Livewire\WithPagination;

class StockReportTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locationId = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocationId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Inventory::query()->with('item', 'location')->latest();

        // Filtering by location
        if ($this->locationId !== 'all') {
            $query->where('location_id', $this->locationId);
        }

        // Search functionality
        if (! empty($this->search)) {
            $query->whereHas('item', function ($q) {
                $q->where('item_name', 'like', "%{$this->search}%")
                    ->orWhere('item_code', 'like', "%{$this->search}%");
            });
        }

        $stockReports = $query->paginate(15);
        $locations = Location::all();

        return view('livewire.admin.inventory.stock-report-table', [
            'stockReports' => $stockReports,
            'locations' => $locations,
        ]);
    }
}
