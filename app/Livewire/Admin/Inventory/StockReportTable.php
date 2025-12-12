<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Inventory\Inventory;
use App\Models\SuperAdmin\MasterData\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class StockReportTable extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locationId = 'all';

    public string $dateRangePreset = 'all';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocationId(): void
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
        $query = Inventory::query()->with(['item', 'location'])->latest();

        $this->applyLocationFilter($query);
        $this->applySearchFilter($query);
        $this->applyDateFilter($query);

        $stockReports = $query->paginate(15);
        $locations = Location::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        return view('livewire.admin.inventory.stock-report-table', [
            'stockReports' => $stockReports,
            'locations' => $locations,
        ]);
    }

    protected function applyLocationFilter(Builder $query): void
    {
        if ($this->locationId !== 'all') {
            $query->where('location_id', $this->locationId);
        }
    }

    protected function applySearchFilter(Builder $query): void
    {
        if (empty($this->search)) {
            return;
        }

        $searchTerm = $this->search;

        $query->whereHas('item', function ($itemQuery) use ($searchTerm) {
            $itemQuery->where('item_name', 'like', "%{$searchTerm}%")
                ->orWhere('item_code', 'like', "%{$searchTerm}%");
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
