<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Inventory\StockMovement;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MoveStockForm extends Component
{
    use LogsActivity;

    public string $itemId = '';

    public string $fromLocationId = '';

    public string $toLocationId = '';

    public int $availableQuantity = 0;

    public ?int $quantity = null;

    public string $notes = '';

    public function updatedItemId(): void
    {
        $this->fromLocationId = '';
        $this->toLocationId = '';
        $this->availableQuantity = 0;
        $this->quantity = null;
    }

    public function updatedFromLocationId(): void
    {
        $this->loadAvailableQuantity();
        $this->quantity = null;
    }

    public function updatedQuantity(): void
    {
        if ($this->quantity !== null && $this->quantity > $this->availableQuantity) {
            $this->quantity = $this->availableQuantity;
        }
    }

    public function getAvailableFromLocationsProperty()
    {
        if (! $this->itemId) {
            return collect([]);
        }

        $inventoryLocations = Inventory::where('item_id', $this->itemId)
            ->where('quantity', '>', 0)
            ->with('location')
            ->get()
            ->pluck('location')
            ->filter()
            ->unique('id');

        return $inventoryLocations->sortBy('name')->values();
    }

    public function loadAvailableQuantity(): void
    {
        if ($this->itemId && $this->fromLocationId) {
            $inventory = Inventory::where('item_id', $this->itemId)
                ->where('location_id', $this->fromLocationId)
                ->first();

            $this->availableQuantity = $inventory ? $inventory->quantity : 0;
        } else {
            $this->availableQuantity = 0;
        }
    }

    public function moveStock(): void
    {
        $this->validate([
            'itemId' => 'required|exists:items,id',
            'fromLocationId' => 'required|exists:locations,id',
            'toLocationId' => 'required|exists:locations,id|different:fromLocationId',
            'quantity' => 'required|integer|min:1' . ($this->availableQuantity > 0 ? '|max:' . $this->availableQuantity : ''),
            'notes' => 'nullable|string|max:500',
        ], [
            'itemId.required' => 'Item harus dipilih.',
            'itemId.exists' => 'Item yang dipilih tidak valid.',
            'fromLocationId.required' => 'Lokasi asal harus dipilih.',
            'fromLocationId.exists' => 'Lokasi asal yang dipilih tidak valid.',
            'toLocationId.required' => 'Lokasi tujuan harus dipilih.',
            'toLocationId.exists' => 'Lokasi tujuan yang dipilih tidak valid.',
            'toLocationId.different' => 'Lokasi tujuan harus berbeda dengan lokasi asal.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah minimal adalah 1.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ]);

        // Validate available quantity
        $fromInventory = Inventory::where('item_id', $this->itemId)
            ->where('location_id', $this->fromLocationId)
            ->first();

        if (! $fromInventory) {
            $this->addError('fromLocationId', 'Stok tidak ditemukan di lokasi asal.');

            return;
        }

        if ($fromInventory->quantity < $this->quantity) {
            $this->addError('quantity', 'Jumlah yang dipindahkan melebihi stok yang tersedia.');

            return;
        }

        DB::beginTransaction();

        try {
            // Get location names for movement record
            $fromLocation = Location::find($this->fromLocationId);
            $toLocation = Location::find($this->toLocationId);

            // Decrease quantity from source location
            $fromInventory->decrement('quantity', $this->quantity);

            // If quantity becomes 0, we can optionally delete the record or keep it
            // For now, we'll keep it with 0 quantity

            // Increase quantity in destination location (or create if doesn't exist)
            $toInventory = Inventory::firstOrCreate(
                [
                    'item_id' => $this->itemId,
                    'location_id' => $this->toLocationId,
                ],
                [
                    'quantity' => 0,
                ]
            );

            $toInventory->increment('quantity', $this->quantity);

            // Create stock movement record
            StockMovement::create([
                'item_id' => $this->itemId,
                'quantity' => $this->quantity,
                'from_location' => $fromLocation->name,
                'to_location' => $toLocation->name,
                'moved_by' => auth()->user()->name ?? 'System',
                'movement_type' => 'transfer',
                'date' => now(),
            ]);

            DB::commit();

            // Log activity
            $this->logActivity('Move Stock', 'Inventory');

            session()->flash('success', 'Stok berhasil dipindahkan dari ' . $fromLocation->name . ' ke ' . $toLocation->name . '.');

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memindahkan stok: ' . $e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'itemId',
            'fromLocationId',
            'toLocationId',
            'availableQuantity',
            'quantity',
            'notes',
        ]);

        $this->quantity = null;

        $this->dispatch('form-reset');
    }

    public function render()
    {
        $items = Item::where('is_active', true)
            ->orderBy('item_name')
            ->get();
        $allLocations = Location::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();

        return view('livewire.admin.inventory.move-stock-form', [
            'items' => $items,
            'allLocations' => $allLocations,
        ]);
    }
}
