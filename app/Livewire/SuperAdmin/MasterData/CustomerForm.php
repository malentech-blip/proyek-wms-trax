<?php

namespace App\Livewire\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Customer;
use Livewire\Component;
use Livewire\Attributes\On;

class CustomerForm extends Component
{
    public $showModal = false;

    // Properti untuk form
    public $name, $address, $phone, $pic;

    #[On('openCustomerModal')]
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

        Customer::create($validated);

        $this->showModal = false;
        $this->dispatch('customer-saved');
    }

    public function render()
    {
        return view('livewire.super-admin.master-data.customer-form');
    }
}