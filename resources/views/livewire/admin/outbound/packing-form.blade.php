<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Sales Order</h2>
        @if ($salesOrder)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div><span class="text-gray-500">SO Number:</span> <span class="font-medium">{{ $salesOrder['number'] ?? '-' }}</span></div>
                <div><span class="text-gray-500">Date:</span> <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($salesOrder['transDate'] ?? null)->format('Y-m-d') }}</span></div>
                <div><span class="text-gray-500">Customer:</span> <span class="font-medium">{{ $salesOrder['customer']['name'] ?? '-' }}</span></div>
            </div>
        @else
            <div class="text-sm text-gray-600">Detail SO tidak tersedia. Anda tetap dapat melakukan scan.</div>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm space-y-4">
        <h3 class="font-semibold">Scan Finished Goods</h3>
        <div class="flex gap-2 items-start">
            <input type="text" wire:model.defer="qrCode" wire:keydown.enter.prevent="scanFinishedGood" placeholder="Scan QR code di sini" class="border rounded-lg px-3 py-2 w-full" />
            <button wire:click="scanFinishedGood" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Tambah</button>
        </div>
        @error('qrCode')
            <div class="text-sm text-red-600">{{ $message }}</div>
        @enderror

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-2">QR</th>
                        <th class="text-left px-4 py-2">Item</th>
                        <th class="text-left px-4 py-2">Qty</th>
                        <th class="text-left px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($scanned as $idx => $row)
                    <tr class="border-t">
                        <td class="px-4 py-2 text-xs text-gray-600">{{ $row['qr_code'] }}</td>
                        <td class="px-4 py-2">{{ $row['item_name'] }}</td>
                        <td class="px-4 py-2">{{ $row['quantity'] }}</td>
                        <td class="px-4 py-2">
                            <button class="px-2 py-1 text-sm rounded border" wire:click="removeScanned({{ $idx }})">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada item.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('admin.outbound.sales-orders.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
        <button wire:click="savePackingList" class="px-4 py-2 rounded-lg bg-green-600 text-white" wire:loading.attr="disabled">Simpan & Buat DO</button>
    </div>
</div>


