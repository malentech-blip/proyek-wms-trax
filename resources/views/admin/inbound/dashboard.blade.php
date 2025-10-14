<x-app-layout>
    <x-slot name="header">
        Dashboard Inbound
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">Ringkasan Tugas Inbound</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">PO Pending</h3>
                <p class="text-3xl font-bold mt-2">0</p> {{-- Akan kita buat dinamis --}}
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">QC Pending</h3>
                <p class="text-3xl font-bold mt-2">0</p> {{-- Akan kita buat dinamis --}}
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Rejected Items</h3>
                <p class="text-3xl font-bold mt-2">0</p> {{-- Akan kita buat dinamis --}}
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-semibold">Chart: Inbound per Supplier</h3>
            <div class="mt-4 h-64 bg-gray-100 flex items-center justify-center">
                <p class="text-gray-400">[Placeholder untuk Chart]</p>
            </div>
        </div>
    </div>
</x-app-layout>