<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Production\RejectProduction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class RejectWarehouseTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public string $dateRangePreset = 'all';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDateRangePreset(): void
    {
        $this->applyDatePreset();
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        if ($this->dateRangePreset !== 'custom') {
            $this->dateRangePreset = 'custom';
        }

        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        if ($this->dateRangePreset !== 'custom') {
            $this->dateRangePreset = 'custom';
        }

        $this->resetPage();
    }

    public function render()
    {
        $query = RejectProduction::query()
            ->with([
                'wip_record.material_request',
                'wip_record.finishedGoods.item.inventories.location',
            ])
            ->latest();

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);
        $this->applyDateFilter($query);

        $rejectProductions = $query->paginate(15);

        return view('livewire.admin.inventory.reject-warehouse-table', [
            'rejectProductions' => $rejectProductions,
        ]);
    }

    protected function applyStatusFilter(Builder $query): void
    {
        if ($this->status === 'all') {
            return;
        }

        $query->where('status', $this->status);
    }

    protected function applySearchFilter(Builder $query): void
    {
        if (empty($this->search)) {
            return;
        }

        $searchTerm = $this->search;

        $query->where(function ($q) use ($searchTerm) {
            $q->where('reason', 'like', "%{$searchTerm}%")
                ->orWhereHas('wip_record.finishedGoods.item', function ($itemQuery) use ($searchTerm) {
                    $itemQuery->where('item_name', 'like', "%{$searchTerm}%")
                        ->orWhere('item_code', 'like', "%{$searchTerm}%");
                });
        });
    }

    protected function applyDateFilter(Builder $query): void
    {
        if (empty($this->dateFrom) && empty($this->dateTo)) {
            return;
        }

        $from = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : null;
        $to = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : null;

        if ($from && $to) {
            if ($from->greaterThan($to)) {
                [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            }

            $query->whereBetween('created_at', [$from, $to]);

            return;
        }

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }
    }

    protected function applyDatePreset(): void
    {
        $today = Carbon::today();

        switch ($this->dateRangePreset) {
            case 'today':
                $this->dateFrom = $today->toDateString();
                $this->dateTo = $today->toDateString();

                return;
            case 'this_week':
                $this->dateFrom = $today->copy()->startOfWeek()->toDateString();
                $this->dateTo = $today->copy()->endOfWeek()->toDateString();

                return;
            case 'this_month':
                $this->dateFrom = $today->copy()->startOfMonth()->toDateString();
                $this->dateTo = $today->copy()->endOfMonth()->toDateString();

                return;
            case 'last_30_days':
                $this->dateFrom = $today->copy()->subDays(29)->toDateString();
                $this->dateTo = $today->toDateString();

                return;
            case 'custom':
                return;
            default:
                $this->dateFrom = null;
                $this->dateTo = null;
        }
    }
}
