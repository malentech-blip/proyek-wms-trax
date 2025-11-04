<x-app-layout>
    <x-slot name="header">
        Dashboard Production
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">Ringkasan Tugas Production</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Material Request (Requested)</h3>
                <p class="text-3xl font-bold mt-2">{{ $materialRequestsRequested }}</p> {{-- Akan kita buat dinamis --}}
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Work In Progress (Active)</h3>
                <p class="text-3xl font-bold mt-2">{{ $wipActive }}</p> {{-- Akan kita buat dinamis --}}
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Finished Goods (Today)</h3>
                <p class="text-3xl font-bold mt-2">{{ $finishedGoodsToday }}</p> {{-- Akan kita buat dinamis --}}
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-semibold">Chart: Production per Supplier</h3>
            <div class="mt-4 h-64 bg-gray-100 flex items-center justify-center">
                <p class="text-gray-400">[Placeholder untuk Chart]</p>
            </div>
        </div>
    </div>
</x-app-layout>