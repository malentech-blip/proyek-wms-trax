<x-app-layout>
    <x-slot name="header">
        Move Stock
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">Move Stock</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<div class="md:col-span-3">
    @livewire('admin.inventory.move-stock-form')
</div>
    </div>
</x-app-layout>
