<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Inbound\ItemLabel;
use App\Models\Admin\Inventory\Inventory;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Rack;
use App\Traits\LogsActivity;
use Livewire\Component;
use Livewire\WithPagination;

class RawMaterialTable extends Component
{
    use LogsActivity;
    use WithPagination;

    public string $search = '';

    public string $locationId = 'all';

    public string $rackId = 'all';

    public string $batchNo = 'all';

    // Modal properties
    public bool $showModal = false;

    public ?int $selectedInventoryId = null;

    public string $newLocationId = '';

    public string $newRackId = '';

    public int $quantityToMove = 0;

    // Computed properties for modal
    public ?object $selectedInventory = null;

    public ?object $currentItemLabel = null;

    public ?object $currentRack = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocationId(): void
    {
        $this->resetPage();
    }

    public function updatedRackId(): void
    {
        $this->resetPage();
    }

    public function updatedBatchNo(): void
    {
        $this->resetPage();
    }

    public function openModal(int $inventoryId): void
    {
        $this->selectedInventoryId = $inventoryId;
        $this->selectedInventory = Inventory::with(['item', 'location'])->find($inventoryId);

        if ($this->selectedInventory) {
            // Get current item label for rack info
            $this->currentItemLabel = ItemLabel::where('item_code', $this->selectedInventory->item->item_code)
                ->where('location_id', $this->selectedInventory->location_id)
                ->with('rack')
                ->first();

            $this->currentRack = $this->currentItemLabel && $this->currentItemLabel->rack
                ? $this->currentItemLabel->rack
                : null;

            $this->newLocationId = (string) $this->selectedInventory->location_id;
            $this->newRackId = $this->currentItemLabel && $this->currentItemLabel->rack_id
                ? (string) $this->currentItemLabel->rack_id
                : '';
            $this->quantityToMove = $this->selectedInventory->quantity;
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset([
            'selectedInventoryId',
            'newLocationId',
            'newRackId',
            'quantityToMove',
            'selectedInventory',
            'currentItemLabel',
            'currentRack',
        ]);
    }

    public function saveChanges(): void
    {
        if (! $this->selectedInventoryId) {
            return;
        }

        $inventory = Inventory::with(['item', 'location'])->find($this->selectedInventoryId);

        if (! $inventory) {
            session()->flash('error', 'Inventory tidak ditemukan.');

            return;
        }

        // Validate quantity
        if ($this->quantityToMove <= 0 || $this->quantityToMove > $inventory->quantity) {
            session()->flash('error', 'Quantity tidak valid.');

            return;
        }

        // Validate new location
        if (empty($this->newLocationId)) {
            session()->flash('error', 'Pilih lokasi tujuan.');

            return;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $oldLocationId = $inventory->location_id;
            $oldLocation = $inventory->location;

            // Get new location
            $newLocation = Location::find($this->newLocationId);
            if (! $newLocation) {
                throw new \Exception('Lokasi tujuan tidak ditemukan.');
            }

            // Get item label for current rack info
            $itemLabel = ItemLabel::where('item_code', $inventory->item->item_code)
                ->where('location_id', $oldLocationId)
                ->first();

            // Update inventory - reduce from old location
            if ($this->quantityToMove == $inventory->quantity) {
                // Move all quantity - update location
                $inventory->update([
                    'location_id' => $this->newLocationId,
                ]);
            } else {
                // Move partial quantity - reduce from old, add to new
                $inventory->decrement('quantity', $this->quantityToMove);

                // Find or create inventory at new location
                $newInventory = Inventory::firstOrCreate(
                    [
                        'item_id' => $inventory->item_id,
                        'location_id' => $this->newLocationId,
                    ],
                    ['quantity' => 0]
                );

                $newInventory->increment('quantity', $this->quantityToMove);
            }

            // Update item label if exists
            if ($itemLabel && $this->newRackId) {
                $newRack = Rack::find($this->newRackId);
                if ($newRack && $newRack->location_id == $this->newLocationId) {
                    // Update item label to new location and rack
                    $itemLabel->update([
                        'location_id' => $this->newLocationId,
                        'rack_id' => $this->newRackId,
                    ]);
                }
            }

            // Create stock movement record
            \App\Models\Admin\Inventory\StockMovement::create([
                'item_id' => $inventory->item_id,
                'quantity' => $this->quantityToMove,
                'from_location' => $oldLocation->code ?? $oldLocation->name,
                'to_location' => $newLocation->code ?? $newLocation->name,
                'moved_by' => auth()->user()->name ?? 'System',
                'movement_type' => 'transfer',
                'date' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            // Log activity
            $this->logActivity('Move Stock', 'Inventory');

            session()->flash('success', 'Stok berhasil dipindahkan.');
            $this->closeModal();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            session()->flash('error', 'Gagal memindahkan stok: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = Inventory::query()
            ->with(['item', 'location'])
            ->whereHas('item', function ($q) {
                $q->where('item_type', 'Raw Material');
            })
            ->latest();

        // Filter by location
        if ($this->locationId !== 'all') {
            $query->where('location_id', $this->locationId);
        }

        // Filter by rack
        if ($this->rackId !== 'all') {
            $itemCodesWithRack = ItemLabel::where('rack_id', $this->rackId)
                ->pluck('item_code')
                ->unique();

            if ($itemCodesWithRack->isNotEmpty()) {
                $itemIds = \App\Models\SuperAdmin\MasterData\Item::whereIn('item_code', $itemCodesWithRack)
                    ->where('item_type', 'Raw Material')
                    ->pluck('id');

                $query->whereIn('item_id', $itemIds);
            } else {
                // If no items found with this rack, return empty results
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by batch
        if ($this->batchNo !== 'all') {
            $itemCodesWithBatch = ItemLabel::where('batch_no', $this->batchNo)
                ->pluck('item_code')
                ->unique();

            if ($itemCodesWithBatch->isNotEmpty()) {
                $itemIds = \App\Models\SuperAdmin\MasterData\Item::whereIn('item_code', $itemCodesWithBatch)
                    ->where('item_type', 'Raw Material')
                    ->pluck('id');

                $query->whereIn('item_id', $itemIds);
            } else {
                // If no items found with this batch, return empty results
                $query->whereRaw('1 = 0');
            }
        }

        // Search functionality
        if (! empty($this->search)) {
            $query->whereHas('item', function ($q) {
                $q->where('item_name', 'like', "%{$this->search}%")
                    ->orWhere('item_code', 'like', "%{$this->search}%");
            });
        }

        $rawMaterials = $query->paginate(15);

        // Eager load item labels for batch and rack display
        $itemCodes = [];
        $locationIds = [];
        foreach ($rawMaterials->items() as $material) {
            if ($material->item && $material->item->item_code) {
                $itemCodes[] = $material->item->item_code;
            }
            if ($material->location_id) {
                $locationIds[] = $material->location_id;
            }
        }

        $itemLabels = ItemLabel::whereIn('item_code', array_unique($itemCodes))
            ->whereIn('location_id', array_unique($locationIds))
            ->with('rack')
            ->get()
            ->keyBy(function ($label) {
                return $label->item_code.'_'.$label->location_id;
            });

        // Attach item labels to materials
        foreach ($rawMaterials->items() as $material) {
            if ($material->item && $material->item->item_code) {
                $key = $material->item->item_code.'_'.$material->location_id;
                $material->itemLabel = $itemLabels->get($key);
            }
        }

        $locations = Location::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        $racks = Rack::query()
            ->select(['id', 'code', 'location_id'])
            ->orderBy('code')
            ->get()
            ->unique('code')
            ->values();

        // Get unique batch numbers for filter (from item_labels where item_type is Raw Material)
        $itemModel = \App\Models\SuperAdmin\MasterData\Item::class;
        $rawMaterialItemCodes = $itemModel::where('item_type', 'Raw Material')
            ->pluck('item_code');

        $batchNumbers = ItemLabel::whereNotNull('batch_no')
            ->whereIn('item_code', $rawMaterialItemCodes)
            ->distinct()
            ->pluck('batch_no')
            ->filter()
            ->values();

        // Get racks for selected location in modal
        if ($this->newLocationId && $this->newLocationId !== 'all' && $this->newLocationId !== '') {
            $availableRacks = Rack::query()
                ->where('location_id', $this->newLocationId)
                ->orderBy('code')
                ->get()
                ->unique('code')
                ->values();
        } else {
            $availableRacks = $racks;
        }

        return view('livewire.admin.inventory.raw-material-table', [
            'rawMaterials' => $rawMaterials,
            'locations' => $locations,
            'racks' => $racks,
            'availableRacks' => $availableRacks,
            'batchNumbers' => $batchNumbers,
        ]);
    }
}
