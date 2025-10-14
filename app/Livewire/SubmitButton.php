<?php

namespace App\Livewire;
use Livewire\Component;

class SubmitButton extends Component
{
    public $quotationId;

    public function render()
    {
        return view('livewire.submit-button');
    }
}