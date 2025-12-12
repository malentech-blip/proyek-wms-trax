<div class="space-y-6">
<form wire:submit.prevent class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center space-x-4">
            <select wire:model.live="status" class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="all">Filter: Status</option>
                <option value="pending">Pending</option>
                <option value="rework">Rework</option>
                <option value="scrap">Scrap</option>
            </select>
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Item atau Alasan"
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
<div class="flex flex-wrap items-center gap-3">
    <select wire:model.live="dateRangePreset"
        class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-4 bg-white">
        <option value="all">Semua Waktu</option>
        <option value="today">Hari Ini</option>
        <option value="this_week">Minggu Ini</option>
        <option value="this_month">Bulan Ini</option>
        <option value="last_30_days">30 Hari Terakhir</option>
        <option value="custom">Rentang Tanggal</option>
    </select>

    <div class="flex items-center gap-2">
        <input type="date" wire:model.live="dateFrom"
            class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
        <span class="text-gray-400 text-sm">s/d</span>
        <input type="date" wire:model.live="dateTo"
            class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
    </div>
</div>
    </form>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-600">
                    <tr>
                        <th class="p-4 text-left font-semibold text-white">No</th>
                        <th class="p-4 text-left font-semibold text-white">Kode Item</th>
                        <th class="p-4 text-left font-semibold text-white">Nama Item</th>
                        <th class="p-4 text-left font-semibold text-white">Qty</th>
                        <th class="p-4 text-left font-semibold text-white">Alasan</th>
                        <th class="p-4 text-left font-semibold text-white">Asal</th>
                        <th class="p-4 text-left font-semibold text-white">Status</th>
                        <th class="p-4 text-left font-semibold text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($rejectProductions as $index => $reject)
                        @php
                            $finishedGood = $reject->wip_record->finishedGoods->first() ?? null;
                            $item = $finishedGood->item ?? null;
                            $wipRecord = $reject->wip_record ?? null;
                            // Get the first inventory location for this item (already eager loaded)
                            $inventory = $item && $item->inventories ? $item->inventories->first() : null;
                            $location = $inventory ? $inventory->location : null;
                        @endphp
                        <tr wire:key="reject-{{ $reject->id }}">
                            <td class="p-4 text-gray-700 font-medium">{{ $rejectProductions->firstItem() + $index }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $item->item_code ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $item->item_name ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $wipRecord->rejected_qty ?? ($finishedGood->quantity ?? 'N/A') }}</td>
                            <td class="p-4 text-gray-500">{{ $reject->reason ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">
                                @if($location)
                                    <span class="text-sm">{{ $location->name ?? 'N/A' }}</span>
                                @else
                                    <span class="text-gray-400 text-sm">N/A</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($reject->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                                @elseif($reject->status === 'rework')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Rework</span>
                                @elseif($reject->status === 'scrap')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Scrap</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($reject->status === 'pending')
                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('admin.inventory.reject-warehouses.rework', $reject) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:underline text-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin mengubah status menjadi rework?')">
                                                Rework
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.inventory.reject-warehouses.scrap', $reject) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:underline text-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin mengubah status menjadi scrap?')">
                                                Scrap
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-12 text-gray-500">
                                Tidak ada data reject production ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rejectProductions->hasPages())
            <div class="p-4 border-t">
                {{ $rejectProductions->links() }}
            </div>
        @endif
    </div>
</div>
