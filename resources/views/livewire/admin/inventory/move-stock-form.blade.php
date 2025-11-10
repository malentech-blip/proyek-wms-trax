<div class="bg-white rounded-lg shadow-sm p-6"
    x-data="{ fromLocationEnabled: @entangle('itemId').live, toLocationEnabled: @entangle('itemId').live }">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Form Pindah Stok</h3>

    @if (session()->has('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <form wire:submit.prevent="moveStock" class="space-y-6">
        {{-- Item Selection --}}
        <div>
            <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">
                Item <span class="text-red-500">*</span>
            </label>
            <select id="item_id" wire:model.live="itemId"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Item</option>
                @foreach ($items as $item)
                <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->item_name }}</option>
                @endforeach
            </select>
            @error('itemId')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if($itemId)
            <p class="mt-1 text-xs text-green-600">✓ Item dipilih. Sekarang pilih lokasi asal dan tujuan.</p>
            @endif
        </div>

        {{-- From Location Selection --}}
        <div>
            <label for="from_location_id" class="block text-sm font-medium text-gray-700 mb-2">
                Lokasi Asal <span class="text-red-500">*</span>
            </label>
            <select id="from_location_id" wire:model.live="fromLocationId" x-bind:disabled="!fromLocationEnabled"
                x-bind:class="fromLocationEnabled ? 'w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500' : 'w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed'"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Lokasi Asal</option>
                @if($itemId)
                @foreach ($this->availableFromLocations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
                @endif
            </select>
            @error('fromLocationId')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(!$itemId)
            <p class="mt-1 text-xs text-gray-500">⚠ Silahkan pilih item terlebih dahulu sebelum memilih lokasi asal</p>
            @elseif($itemId && !$fromLocationId)
            <p class="mt-1 text-xs text-blue-600">Pilih lokasi asal untuk melihat stok yang tersedia</p>
            @endif
        </div>

        {{-- Available Quantity (Read-only) --}}
        @if($fromLocationId)
        <div>
            <label for="available_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                Stok Tersedia
            </label>
            <input type="number" id="available_quantity" wire:model="availableQuantity" readonly
                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed">
            <p class="mt-1 text-xs text-gray-500">Jumlah stok yang tersedia di lokasi asal</p>
        </div>
        @endif

        {{-- To Location Selection --}}
        <div>
            <label for="to_location_id" class="block text-sm font-medium text-gray-700 mb-2">
                Lokasi Tujuan <span class="text-red-500">*</span>
            </label>
            <select id="to_location_id" wire:model.live="toLocationId" x-bind:disabled="!toLocationEnabled"
                x-bind:class="toLocationEnabled ? 'w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500' : 'w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed'"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Lokasi Tujuan</option>
                @if($itemId)
                @foreach ($allLocations as $location)
                <option value="{{ $location->id }}" @if($location->id == $fromLocationId) disabled @endif>
                    {{ $location->name }}
                </option>
                @endforeach
                @endif
            </select>
            @error('toLocationId')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(!$itemId)
            <p class="mt-1 text-xs text-gray-500">⚠ Silahkan pilih item terlebih dahulu sebelum memilih lokasi tujuan
            </p>
            @elseif($itemId && !$toLocationId)
            <p class="mt-1 text-xs text-blue-600">Pilih lokasi tujuan untuk memindahkan stok</p>
            @endif
        </div>

        {{-- Quantity --}}
        <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                Jumlah <span class="text-red-500">*</span>
            </label>
            <input type="number" id="quantity" wire:model.debounce.500ms="quantity" min="1" @if($fromLocationId &&
                $availableQuantity> 0)
            max="{{ $availableQuantity }}" @endif
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500">
                @if($fromLocationId)
                Jumlah yang akan dipindahkan (maksimal: {{ $availableQuantity }})
                @else
                Jumlah yang akan dipindahkan
                @endif
            </p>
            @error('quantity')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Notes --}}
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                Catatan
            </label>
            <textarea id="notes" wire:model="notes" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Tambahkan catatan untuk perpindahan stok ini (opsional)..."></textarea>
            @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end">
            <button type="button" wire:click="resetForm"
                class="px-4 py-2 mr-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Reset
            </button>
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium text-white bg-[#624bff] rounded-md hover:bg-[#5333ff] disabled:opacity-50">
                <span wire:loading.remove wire:target="moveStock">Pindahkan Stok</span>
                <span wire:loading wire:target="moveStock">Memindahkan...</span>
            </button>
        </div>
    </form>
</div>
