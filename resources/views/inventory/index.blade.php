<x-app-layout>
    <x-slot name="header">
Dashboard Inventory
</x-slot>
<div class="space-y-6">
    <h2 class="text-2xl font-semibold">Ringkasan Inventory</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="text-gray-500">Total Raw Materials</h3>
            <p class="text-3xl font-bold mt-2">0</p> {{-- Akan kita buat dinamis --}}
            </div>
<div class="bg-white p-6 rounded-xl shadow-sm">
    <h3 class="text-gray-500">Total Reject Products</h3>
    <p class="text-3xl font-bold mt-2">0</p> {{-- Akan kita buat dinamis --}}
</div>
<div class="bg-white p-6 rounded-xl shadow-sm">
    <h3 class="text-gray-500">Total Finished Products</h3>
    <p class="text-3xl font-bold mt-2">0</p> {{-- Akan kita buat dinamis --}}
</div>
</div>


</x-app-layout>
