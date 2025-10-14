<?php

namespace App\Livewire\SuperAdmin\MasterData;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Rack;
use App\Models\SuperAdmin\MasterData\Pallet;

class LocationForm extends Component
{
    public $showModal = false;

    // Properti untuk form
    public $locationName, $locationCode, $rackCode, $palletCode, $capacity;

    #[On('openLocationModal')]
    public function openModal()
    {
        $this->reset(); // Reset form setiap kali modal dibuka
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'locationName' => 'required|string',
            'locationCode' => 'required|string|unique:locations,code',
            'rackCode' => 'required|string|unique:racks,code',
            'palletCode' => 'required|string|unique:pallets,code',
            'capacity' => 'nullable|string',
        ]);

        // Buat Lokasi/Gudang
        $location = Location::create([
            'name' => $this->locationName,
            'code' => $this->locationCode,
        ]);

        // Buat Rak dan hubungkan ke Lokasi
        $rack = $location->racks()->create([
            'code' => $this->rackCode,
        ]);

        // Buat Pallet dan hubungkan ke Rak
        $rack->pallets()->create([
            'code' => $this->palletCode,
            'capacity' => $this->capacity,
        ]);

        $this->showModal = false;
        $this->dispatch('location-saved'); // Kirim event bahwa data berhasil disimpan
    }

    public function render()
    {
        return view('livewire.super-admin.master-data.location-form');
    }
}