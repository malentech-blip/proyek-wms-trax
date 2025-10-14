<x-app-layout>
    <x-slot name="header">Lokasi Rak atau Pallet</x-slot>

    @livewire('super-admin.master-data.location-form')

    <div class="space-y-6" @location-saved.window="location.reload()">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Struktur Gudang</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua data lokasi, rak, dan pallet.</p>
            </div>
            <button @click="$dispatch('openLocationModal')" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                + Tambah Lokasi Rak / Pallet
            </button>
        </div>
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tabel Hirarki Lokasi</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Lokasi</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Rak</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Pallet</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Kapasitas</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($pallets as $pallet)
                        <tr>
                            <td class="p-4 font-medium">{{ $pallet->rack->location->name ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $pallet->rack->code ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $pallet->code }}</td>
                            <td class="p-4 text-gray-500">{{ $pallet->capacity ?? '-' }}</td>
                            <td class="p-4"><span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full">Aktif</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-12 text-gray-500">Belum ada data lokasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pallets->hasPages())
            <div class="p-4 border-t">{{ $pallets->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>