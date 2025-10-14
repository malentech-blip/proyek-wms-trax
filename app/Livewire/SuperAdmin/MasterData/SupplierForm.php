<?php

namespace App\Livewire\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Supplier;
use Livewire\Component;
use Livewire\Attributes\On;

class SupplierForm extends Component
{
    public $showModal = false;

    // Properti untuk form
    public $name, $address, $phone, $pic;

    #[On('openSupplierModal')]
    public function openModal()
    {
        $this->reset();
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'pic' => 'nullable|string',
        ]);

        Supplier::create($validated);

        $this->showModal = false;
        $this->dispatch('supplier-saved');
    }

    public function render()
    {
        return view('livewire.super-admin.master-data.supplier-form');
    }
}