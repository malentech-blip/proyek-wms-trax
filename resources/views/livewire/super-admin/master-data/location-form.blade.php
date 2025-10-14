<div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.set('showModal', false)">
            <form wire:submit.prevent="save">
                <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tambah Lokasi / Rak / Pallet</h3></div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Nama Lokasi (Gudang)</label>
                        <input type="text" wire:model="locationName" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('locationName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Kode Lokasi</label>
                        <input type="text" wire:model="locationCode" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('locationCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <hr>
                    <div>
                        <label class="block text-sm font-medium">Kode Rak</label>
                        <input type="text" wire:model="rackCode" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('rackCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <hr>
                    <div>
                        <label class="block text-sm font-medium">Kode Pallet</label>
                        <input type="text" wire:model="palletCode" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('palletCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Kapasitas</label>
                        <input type="text" wire:model="capacity" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('capacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end space-x-2">
                    <button type="button" @click="$wire.set('showModal', false)" class="bg-white border rounded-lg px-4 py-2">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>