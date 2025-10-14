<?php

// PERBAIKAN UTAMA DI SINI: Hapus 'Http'
namespace App\Livewire; 

use Livewire\Component;
use App\Services\AccurateService;
use Exception;

class CreateCustomerForm extends Component
{
    public $showModal = false;

    public $name;
    public $email;
    public $phone;

    protected $listeners = ['openCreateCustomerModal' => 'openModal'];

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'phone']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

   public function saveCustomer(AccurateService $accurate)
{
    $this->validate([
        'name' => 'required|string|min:3',
        'email' => 'nullable|email',
        'phone' => 'nullable|string',
    ]);

    try {
        $newCustomerResponse = $accurate->createCustomer([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        // --- PENYESUAIAN FINAL ADA DI SINI ---
        // Ganti 'd' menjadi 'r' sesuai dengan respons dari log
        if (isset($newCustomerResponse['r'])) {
            $customerData = $newCustomerResponse['r'];

            if (isset($customerData['id'], $customerData['name'])) {
                $this->dispatch('customer-created', [
                    'id' => $customerData['id'],
                    'name' => $customerData['name'],
                ]);
                
                $this->closeModal();
            } else {
                throw new Exception('Respons dari Accurate tidak berisi ID atau nama customer di dalam objek \'r\'.');
            }
        } else {
            $errorMessage = $newCustomerResponse['d'][0] ?? 'Respons dari Accurate tidak memiliki format yang diharapkan.';
            throw new Exception($errorMessage);
        }

    } catch (Exception $e) {
        $this->addError('general', 'Gagal membuat customer: ' . $e->getMessage());
    }
}

    public function render()
    {
        return view('livewire.create-customer-form');
    }
}