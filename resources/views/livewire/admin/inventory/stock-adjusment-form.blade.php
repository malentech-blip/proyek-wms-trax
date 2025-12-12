<div class="bg-white rounded-lg shadow-sm p-6" x-data="{ locationEnabled: @entangle('itemId').live }">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Form Penyesuaian Stok</h3>

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

    <form wire:submit.prevent="saveAdjustment" class="space-y-6">
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
                <p class="mt-1 text-xs text-green-600">✓ Item dipilih. Sekarang pilih lokasi.</p>
            @endif
        </div>

        {{-- Location Selection --}}
        <div>
            <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">
                Lokasi <span class="text-red-500">*</span>
            </label>
            <select id="location_id"
                wire:model.live="locationId"
                x-bind:disabled="!locationEnabled"
                x-bind:class="locationEnabled ? 'w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500' : 'w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed'"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Lokasi</option>
                @if($itemId)
                    @foreach ($this->availableLocations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                @endif
            </select>
            @error('locationId')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(!$itemId)
                <p class="mt-1 text-xs text-gray-500">⚠ Silahkan pilih item terlebih dahulu sebelum memilih lokasi</p>
            @elseif($itemId && !$locationId)
                <p class="mt-1 text-xs text-blue-600">Pilih lokasi untuk melihat stok sistem</p>
            @endif
        </div>

        {{-- System Quantity (Read-only) --}}
        <div>
            <label for="system_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                Stok Sistem
            </label>
            <input type="number" id="system_quantity" wire:model="systemQuantity" readonly
                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed">
            <p class="mt-1 text-xs text-gray-500">Jumlah stok saat ini dalam sistem</p>
        </div>

        {{-- Physical Quantity --}}
        <div>
            <label for="physical_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                Stok Fisik <span class="text-red-500">*</span>
            </label>
<input type="number" id="physical_quantity" wire:model.debounce="physicalQuantity" min="0"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500">Jumlah aktual dari inventaris fisik</p>
            @error('physicalQuantity')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Difference (Read-only) --}}
        <div>
            <label for="difference" class="block text-sm font-medium text-gray-700 mb-2">
                Selisih
            </label>
            <input type="number" id="difference" wire:model="difference" readonly
                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed {{ $difference > 0 ? 'text-green-600 font-semibold' : ($difference < 0 ? 'text-red-600 font-semibold' : '') }}">
            <p class="mt-1 text-xs text-gray-500">
                @if($difference > 0)
                    <span class="text-green-600">Bertambah: +{{ $difference }} unit</span>
                @elseif($difference < 0)
                    <span class="text-red-600">Berkurang: {{ $difference }} unit</span>
                @else
                    Tidak ada selisih
                @endif
            </p>
        </div>

        {{-- Reason --}}
        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                Alasan Penyesuaian
            </label>
            <textarea id="reason" wire:model="reason" rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Jelaskan alasan penyesuaian stok ini..."></textarea>
            @error('reason')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Requires Approval --}}
        <div class="flex items-center">
            <input type="checkbox" id="requires_approval" wire:model="requiresApproval"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="requires_approval" class="ml-2 block text-sm text-gray-700">
                Memerlukan Persetujuan Super Admin
            </label>
        </div>
        <p class="text-xs text-gray-500">
            @if($requiresApproval)
                Penyesuaian ini akan diajukan untuk persetujuan Super Admin sebelum diterapkan.
            @else
                Penyesuaian ini akan diterapkan langsung tanpa persetujuan.
            @endif
        </p>

        {{-- Submit Button --}}
        <div class="flex justify-end">
            <button type="button" wire:click="resetForm"
                class="px-4 py-2 mr-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Reset
            </button>
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 text-sm font-medium text-white bg-[#624bff] rounded-md hover:bg-[#5333ff] disabled:opacity-50">
                <span wire:loading.remove wire:target="saveAdjustment">Simpan Penyesuaian</span>
                <span wire:loading wire:target="saveAdjustment">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
