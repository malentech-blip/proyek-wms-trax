<x-app-layout>
    <x-slot name="header">
        Stock Adjustment
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">Stock Adjustment</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @livewire('admin.inventory.stock-adjusment-form')
    </div>
</x-app-layout>
