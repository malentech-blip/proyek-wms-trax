<?php

namespace App\Livewire;

use Livewire\Component;

class LabelTemplateForm extends Component
{
    public $showModal = false;

#[On('open-label-modal')]
public function openModal()
{
    $this->showModal = true;
}

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        // Logika untuk menyimpan data akan ditambahkan di sini nanti
        
        $this->closeModal(); // Tutup modal setelah disimpan
    }

    public function render()
    {
        return view('livewire.label-template-form');
    }
}