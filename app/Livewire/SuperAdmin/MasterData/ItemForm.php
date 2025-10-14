<?php

namespace App\Livewire\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Item;
use Livewire\Component;
use Livewire\Attributes\On;

class ItemForm extends Component
{
    public $showModal = false;

    // Properti untuk form fields
    public $item_code, $item_name, $item_type, $uom;

    #[On('openItemModal')]
    public function openModal()
    {
        $this->reset();
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'item_code' => 'required|string|unique:items,item_code',
            'item_name' => 'required|string',
            'item_type' => 'required|string', // cth: 'Raw Material'
            'uom' => 'required|string',       // cth: 'KG'
        ]);

        Item::create($validated);

        $this->showModal = false;
        $this->dispatch('item-saved'); // Kirim event untuk refresh halaman utama
    }

    public function render()
    {
        return view('livewire.super-admin.master-data.item-form');
    }
}