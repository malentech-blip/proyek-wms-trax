<form wire:submit.prevent="save" class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800">Detail Penerimaan</h3>
        <dl class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="font-medium text-gray-500">No. Penerimaan</dt>
                <dd class="mt-1 font-semibold text-gray-800">{{ $goodsReceipt->receipt_number }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">No. PO</dt>
                <dd class="mt-1 font-semibold text-gray-800">{{ $goodsReceipt->po_number }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Tanggal Terima</dt>
                <dd class="mt-1 font-semibold text-gray-800">{{ \Carbon\Carbon::parse($goodsReceipt->receipt_date)->format('d F Y') }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Formulir Quality Check</h3>
        </div>
        <div class="divide-y">
            @foreach($items as $itemId => $item)
            <div class="p-6 grid grid-cols-1 md:grid-cols-5 gap-6 items-start" wire:key="item-{{ $itemId }}">
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-gray-800">{{ $item['item_name'] }}</h4>
                    <p class="text-xs text-gray-500">Total Diterima: {{ $item['received_qty'] }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:col-span-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah Lolos (Passed)</label>
                        <input type="number" wire:model.live="items.{{ $itemId }}.passed_qty" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah Ditolak (Rejected)</label>
                        <input type="number" wire:model.live="items.{{ $itemId }}.rejected_qty" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Catatan QC</label>
                        <input type="text" wire:model.defer="items.{{ $itemId }}.notes" placeholder="cth: Kemasan rusak" class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                     <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Foto Bukti (Opsional)</label>
                        <input type="file" wire:model="items.{{ $itemId }}.photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <div wire:loading wire:target="items.{{ $itemId }}.photo" class="text-xs text-gray-500 mt-1">Uploading...</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg text-sm hover:bg-blue-700">
            Submit Hasil QC & Lanjut ke Putaway
        </button>
    </div>
</form>