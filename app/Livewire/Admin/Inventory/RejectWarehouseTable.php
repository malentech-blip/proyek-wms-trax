<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Production\RejectProduction;
use Livewire\Component;
use Livewire\WithPagination;

class RejectWarehouseTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = RejectProduction::query()
            ->with([
                'wip_record.material_request',
                'wip_record.finishedGoods.item.inventories.location',
            ]);

        // Filter by status
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Search by reason or item name
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('reason', 'like', "%{$this->search}%")
                    ->orWhereHas('wip_record.finishedGoods.item', function ($itemQuery) {
                        $itemQuery->where('item_name', 'like', "%{$this->search}%")
                            ->orWhere('item_code', 'like', "%{$this->search}%");
                    });
            });
        }

        $rejectProductions = $query->latest()->paginate(15);

        return view('livewire.admin.inventory.reject-warehouse-table', [
            'rejectProductions' => $rejectProductions,
        ]);
    }
}
