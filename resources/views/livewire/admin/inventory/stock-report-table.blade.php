<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form wire:submit.prevent class="flex items-center space-x-4">
            <select wire:model.live="locationId" class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Locations</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau Item..."
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-blue-600">
                <tr>
                    <th class="p-4 text-left font-semibold text-white">Kode Produk</th>
                    <th class="p-4 text-left font-semibold text-white">Nama Produk</th>
                    <th class="p-4 text-left font-semibold text-white">Batch</th>
                    <th class="p-4 text-left font-semibold text-white">Qty</th>
                    <th class="p-4 text-left font-semibold text-white">Lokasi</th>
                    <th class="p-4 text-left font-semibold text-white">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($stockReports as $stockReport)
                    <tr wire:key="stock-report-{{ $stockReport->id }}">
                        <td class="p-4 text-gray-700 font-medium">{{ $stockReport->item->item_code }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->item->item_name }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->item->batch_no ?? '-' }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->quantity }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->location->name }}</td>
                        <td class="p-4">
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full">Aktif</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-12 text-gray-500">
                            Tidak ada data stock report ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stockReports->hasPages())
        <div class="p-4 border-t">
            {{ $stockReports->links() }}
        </div>
    @endif
</div>
