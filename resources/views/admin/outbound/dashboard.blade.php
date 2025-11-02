<x-app-layout>
    <x-slot name="header">
        Dashboard Outbound
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">Ringkasan Tugas Outbound</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Pending Sales Orders</h3>
                <p class="text-3xl font-bold mt-2">{{ $pendingSalesOrders }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Packing in Progress</h3>
                <p class="text-3xl font-bold mt-2">{{ $packingInProgress }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Today's Shipments</h3>
                <p class="text-3xl font-bold mt-2">{{ $todayShipments }}</p>
            </div>
        </div>
    </div>
</x-app-layout>