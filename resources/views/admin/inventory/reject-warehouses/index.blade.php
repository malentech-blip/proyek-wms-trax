<x-app-layout>
    <x-slot name="header">
        Reject Warehouses
    </x-slot>

    <div class="space-y-6" @user-saved.window="location.reload()">
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Reject Warehouses</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel Produk Yang Ditolak.</p>
            </div>
        </div>

        {{-- Using livewire for handling datatable and filtering --}}
        @livewire('admin.inventory.reject-warehouse-table')
    </div>
</x-app-layout>
