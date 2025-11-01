<x-app-layout>
    <x-slot name="header">
        Raw Material Storage
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Raw Material Storage</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel Stok Bahan Baku</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div>
            </div>
            
        </div>
        {{-- Using livewire for handling datatable and filtering --}}
        @livewire('admin.inventory.raw-material-table')
    </div>
</x-app-layout>
