<x-app-layout>
    <x-slot name="header">
        Stock Reports
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Stock Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel stock barang jadi.</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div>
                <!-- Livewire component will handle search and location filter -->
            </div>
            <a href="{{ route('admin.inventory.stock-reports.export') }}"
                class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center">
                Print Stock Report (Excel)
            </a>
        </div>
        @livewire('admin.inventory.stock-report-table')
    </div>
</x-app-layout>
