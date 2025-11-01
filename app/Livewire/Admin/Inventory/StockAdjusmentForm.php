<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Admin\Inventory\Inventory;
use App\Models\Admin\Inventory\StockAdjustment;
use App\Models\SuperAdmin\MasterData\Item;
use App\Models\SuperAdmin\MasterData\Location;
use App\Notifications\StockAdjustmentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class StockAdjusmentForm extends Component
{
    public string $itemId = '';

    public string $locationId = '';

    public int $systemQuantity = 0;

    public int $physicalQuantity = 0;

    public int $difference = 0;

    public string $reason = '';

    public bool $requiresApproval = true;

    public function updatedItemId(): void
    {
        // Reset location when item changes
        $this->locationId = '';
        $this->systemQuantity = 0;
        $this->physicalQuantity = 0;
        $this->difference = 0;
    }

    public function updatedLocationId(): void
    {
        $this->loadSystemQuantity();
    }

    public function updatedPhysicalQuantity(): void
    {
        $this->calculateDifference();
    }

    public function calculateDifference(): void
    {
        $this->difference = $this->physicalQuantity - $this->systemQuantity;
    }

    public function getAvailableLocationsProperty()
    {
        if (!$this->itemId) {
            return collect([]);
        }

        $inventoryLocations = Inventory::where('item_id', $this->itemId)
            ->with('location')
            ->get()
            ->pluck('location')
            ->filter()
            ->unique('id');

        return $inventoryLocations->sortBy('name')->values();
    }

    public function loadSystemQuantity(): void
    {
        if ($this->itemId && $this->locationId) {
            $inventory = Inventory::where('item_id', $this->itemId)
                ->where('location_id', $this->locationId)
                ->first();

            $this->systemQuantity = $inventory ? $inventory->quantity : 0;
            $this->physicalQuantity = $this->systemQuantity;
            $this->calculateDifference();
        } else {
            $this->systemQuantity = 0;
            $this->physicalQuantity = 0;
            $this->difference = 0;
        }
    }

    public function saveAdjustment(): void
    {
        $this->validate([
            'itemId' => 'required|exists:items,id',
            'locationId' => 'required|exists:locations,id',
            'physicalQuantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:500',
            'requiresApproval' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Get current inventory
            $inventory = Inventory::where('item_id', $this->itemId)
                ->where('location_id', $this->locationId)
                ->first();

            if (!$inventory) {
                session()->flash('error', 'Stok tidak ditemukan untuk item dan lokasi yang dipilih.');
                DB::rollBack();
                return;
            }

            // Calculate difference
            $this->difference = $this->physicalQuantity - $inventory->quantity;

            // Create stock adjustment record
            $stockAdjustment = StockAdjustment::create([
                'item_id' => $this->itemId,
                'location_id' => $this->locationId,
                'system_quantity' => $inventory->quantity,
                'physical_quantity' => $this->physicalQuantity,
                'difference' => $this->difference,
                'reason' => $this->reason,
                'adjusted_by' => auth()->id(),
                'requires_approval' => $this->requiresApproval,
                'status' => $this->requiresApproval ? 'pending' : 'approved',
            ]);

            // If approval is not required, update inventory immediately
            if (!$this->requiresApproval) {
                $inventory->update(['quantity' => $this->physicalQuantity]);
                $stockAdjustment->update([
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'status' => 'approved',
                ]);

                // Create stock movement record
                \App\Models\Admin\Inventory\StockMovement::create([
                    'item_id' => $this->itemId,
                    'quantity' => abs($this->difference),
                    'from_location' => $this->difference > 0 ? 'Adjustment' : $inventory->location->name,
                    'to_location' => $this->difference > 0 ? $inventory->location->name : 'Adjustment',
                    'moved_by' => auth()->user()->name ?? 'System',
                    'movement_type' => 'adjustment',
                    'date' => now(),
                ]);
            } else {
                // Send notification to Super Admin users
                $superAdmins = \App\Models\User::role('Super Admin')->get();

                if ($superAdmins->isNotEmpty()) {
                    Notification::send($superAdmins, new StockAdjustmentNotification($stockAdjustment));
                }
            }

            DB::commit();

            session()->flash('success', $this->requiresApproval
                ? 'Penyesuaian stok berhasil diajukan. Menunggu persetujuan Super Admin.'
                : 'Penyesuaian stok berhasil diselesaikan.');

            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan penyesuaian stok: ' . $e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'itemId',
            'locationId',
            'systemQuantity',
            'physicalQuantity',
            'difference',
            'reason',
        ]);

        // Emit event to reset Select2
        $this->dispatch('form-reset');
    }

    public function render()
    {
        $items = Item::where('is_active', true)->orderBy('item_name')->get();
        $locations = Location::orderBy('name')->get();

        return view('livewire.admin.inventory.stock-adjusment-form', [
            'items' => $items,
            'locations' => $locations,
        ]);
    }
}
