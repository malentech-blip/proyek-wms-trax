<div>
    @if ($showModal)
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="show = false">
            
            <form wire:submit.prevent="save">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Template Label</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Nama Template</label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="cth: Template Inbound">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Jenis Label</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option>Item (Bahan/Produk)</option>
                            <option>Pallet</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Ukuran Label</label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm text-sm" value="55x45/45x30/dll">
                    </div>

                    <hr class="pt-2">
                    <p class="font-semibold text-gray-800">Elemen Label</p>
                    
                    <div class="space-y-2 text-sm text-gray-500">
                        <p>Nama Item: <span class="font-mono text-gray-800">[item_name]</span></p>
                        <p>Kode Item: <span class="font-mono text-gray-800">[item_code]</span></p>
                        <p>Batch No: <span class="font-mono text-gray-800">[batch_no]</span></p>
                        <p>Lokasi: <span class="font-mono text-gray-800">[location_code]</span></p>
                        <p>QR Code: <span class="font-mono text-gray-800">[qr_code]</span></p>
                    </div>

                    <div class="pt-4 flex justify-center">
                        <button type="button" class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700">
                            Preview Label
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 flex justify-end space-x-2">
                    <button type="button" wire:click="closeModal" class="bg-white hover:bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-lg border">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>