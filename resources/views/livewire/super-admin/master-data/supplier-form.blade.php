<div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="$wire.set('showModal', false)">
            <form wire:submit.prevent="save" class="w-full">
                <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tambah Supplier Baru</h3></div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Nama Supplier</label>
                        <input type="text" wire:model="name" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Alamat</label>
                        <input type="text" wire:model="address" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Telepon</label>
                        <input type="text" wire:model="phone" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">PIC</label>
                        <input type="text" wire:model="pic" class="mt-1 w-full border-gray-300 rounded-md">
                        @error('pic') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end space-x-2">
                    <button type="button" @click="$wire.set('showModal', false)" class="bg-white border rounded-lg px-4 py-2">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>