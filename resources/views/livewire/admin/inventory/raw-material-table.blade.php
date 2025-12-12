<div class="space-y-6" wire:key="raw-material-table">
    {{-- Search and Filter Bar --}}
<div class="flex flex-wrap justify-between items-center gap-4">
        {{-- Search Bar --}}
<div class="flex-1 min-w-[220px] relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari No / Supplier"
                class="w-full border-gray-300 rounded-[15px] shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 pl-10 pr-4 py-2">
            <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div wire:loading wire:target="search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>

<div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <select wire:model.live="locationId"
                    class="border-gray-300 rounded-[15px] shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 pr-8 pl-4 py-2 appearance-none bg-white">
                    <option value="all">Filter : Status</option>
                    @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
</div>
                </div>

                <select wire:model.live="dateRangePreset"
                    class="border-gray-300 rounded-[15px] shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-4 bg-white">
                    <option value="all">Semua Waktu</option>
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_30_days">30 Hari Terakhir</option>
                    <option value="custom">Rentang Tanggal</option>
                </select>

                <div class="flex items-center gap-2">
                    <input type="date" wire:model.live="dateFrom"
                        class="border-gray-300 rounded-[15px] shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
                    <span class="text-gray-400 text-sm">s/d</span>
                    <input type="date" wire:model.live="dateTo"
                        class="border-gray-300 rounded-[15px] shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 py-2 px-3">
            </div>
        </div>
    </div>

    {{-- Filter Panel (Collapsible) --}}
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-sm font-medium text-gray-700 mb-4">Filter Panel (Collapsible) :</h3>
<div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-4">
            {{-- Lokasi Filter --}}
            <div>
                <label for="filter-location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi :</label>
                <select id="filter-location" wire:model.live="locationId" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Rak Filter --}}
            <div>
                <label for="filter-rack" class="block text-sm font-medium text-gray-700 mb-2">Rak :</label>
                <select id="filter-rack" wire:model.live="rackId" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua Rak</option>
                    @foreach ($racks as $rack)
                        <option value="{{ $rack->id }}">{{ $rack->code }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Batch Filter --}}
            <div>
                <label for="filter-batch" class="block text-sm font-medium text-gray-700 mb-2">Batch :</label>
                <select id="filter-batch" wire:model.live="batchNo" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua Batch</option>
                    @foreach ($batchNumbers as $batch)
                        <option value="{{ $batch }}">{{ $batch }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        {{-- Table Header --}}
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-medium text-gray-800">Tabel Stok Bahan Baku</h3>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#624bff]">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Kode Item</th>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Nama Item</th>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Batch</th>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Lokasi</th>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Rak</th>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Qty</th>
                        <th class="px-6 py-3 text-left font-medium text-white capitalize">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($rawMaterials as $index => $material)
                        <tr wire:key="raw-material-{{ $material->id }}">
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $material->item->item_code ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $material->item->item_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $material->itemLabel->batch_no ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $material->location->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                @if($material->itemLabel && $material->itemLabel->rack)
                                    <span class="underline">{{ $material->itemLabel->rack->code ?? 'N/A' }}</span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $material->quantity ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <button wire:click="openModal({{ $material->id }})" class="text-gray-600 hover:text-blue-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <button wire:click="openModal({{ $material->id }})" class="ml-2 text-gray-600 hover:text-blue-600">
                                    <span class="text-sm">Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-500">
                                Tidak ada data raw material ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rawMaterials->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $rawMaterials->links() }}
            </div>
        @endif
    </div>

    {{-- Edit Stock Position Modal --}}
    @if($showModal)
        {{-- Modal Overlay --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
            wire:click="closeModal"
            wire:keydown.escape="closeModal"
            wire:loading.attr="disabled">
            {{-- Modal Content --}}
            <div class="bg-white rounded-lg shadow-lg w-full max-w-[583px] p-6"
                wire:click.stop
                wire:key="edit-stock-modal">
                {{-- Modal Header --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Edit Stok Position :</h3>
                </div>

                {{-- Current Location --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-700 mb-2">
                        Lokasi Sekarang :
                        <span class="font-medium">
                            @if($selectedInventory && $selectedInventory->location)
                                {{ $selectedInventory->location->name }}
                                @if($currentRack)
                                    - {{ $currentRack->code }}
                                @endif
                            @else
                                N/A
                            @endif
                        </span>
                    </p>
                </div>

                {{-- Move To Location --}}
                <div class="mb-4">
                    <label for="modal-new-location" class="block text-sm font-medium text-gray-700 mb-2">
                        Pindahkan ke :
                    </label>
                    <select id="modal-new-location"
                        wire:model.live="newLocationId"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('newLocationId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rack Selection (shown when location is selected) --}}
                @if($newLocationId)
                    <div class="mb-4">
                        <label for="modal-new-rack" class="block text-sm font-medium text-gray-700 mb-2">
                            Rak :
                        </label>
                        <select id="modal-new-rack"
                            wire:model="newRackId"
                            class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Rak</option>
                            @foreach ($availableRacks as $rack)
                                <option value="{{ $rack->id }}">{{ $rack->code }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Quantity to Move --}}
                <div class="mb-6">
                    <label for="modal-quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Qty Dipindahkan :
                    </label>
                    <input id="modal-quantity"
                        type="number"
                        wire:model="quantityToMove"
                        min="1"
                        max="{{ $selectedInventory ? $selectedInventory->quantity : 0 }}"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    @if($selectedInventory)
                        <p class="mt-1 text-xs text-gray-500">Maksimal: {{ $selectedInventory->quantity }}</p>
                    @endif
                    @error('quantityToMove')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Flash Messages --}}
                @if (session()->has('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Modal Footer --}}
                <div class="flex justify-end gap-3">
                    <button wire:click="closeModal"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Batal
                    </button>
                    <button wire:click="saveChanges"
                        wire:loading.attr="disabled"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-[#624bff] rounded-md hover:bg-[#5333ff] disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveChanges">Simpan Perubahan</span>
                        <span wire:loading wire:target="saveChanges">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
