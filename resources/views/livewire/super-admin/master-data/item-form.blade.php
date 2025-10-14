<div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.set('showModal', false)">
            <form wire:submit.prevent="save" class="w-full">
                <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tambah Item Baru</h3></div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Kode Item</label>
                        <input type="text" wire:model="item_code" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('item_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Nama Item</label>
                        <input type="text" wire:model="item_name" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('item_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Kategori</label>
                        <input type="text" wire:model="item_type" placeholder="cth: Raw Material" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('item_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Satuan</label>
                        <input type="text" wire:model="uom" placeholder="cth: KG, PCS, Liter" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('uom') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end space-x-2">
                    <button type="button" @click="$wire.set('showModal', false)" class="bg-white border rounded-lg px-4 py-2">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2">Simpan Item</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>