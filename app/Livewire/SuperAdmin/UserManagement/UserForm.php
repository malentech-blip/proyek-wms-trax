<?php

namespace App\Livewire\SuperAdmin\UserManagement;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;

class UserForm extends Component
{
    public $showModal = false;
    
    // Properti untuk form
    public $name, $email, $password, $role;
    public $roles;

    #[On('openUserModal')]
    public function openModal()
    {
        $this->reset(); // Reset form setiap kali modal dibuka
        $this->roles = Role::pluck('name', 'name')->all(); // Ambil semua nama role
        $this->showModal = true;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role, // Kolom 'role' di tabel users
        ]);

        $user->assignRole($this->role); // Tetapkan role menggunakan Spatie

        $this->showModal = false;
        $this->dispatch('user-saved'); // Kirim event untuk refresh halaman utama
    }

    public function render()
    {
        return view('livewire.super-admin.user-management.user-form');
    }
}