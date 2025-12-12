<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800">Detail Purchase Order</h3>
            <dl class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="font-medium text-gray-500">No. PO</dt>
                    <dd class="mt-1 font-semibold text-gray-800">{{ $purchaseOrder['number'] }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Pemasok</dt>
                    <dd class="mt-1 font-semibold text-gray-800">{{ $purchaseOrder['vendor']['name'] }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500">Tanggal Transaksi</dt>
                    <dd class="mt-1 font-semibold text-gray-800">{{ \Carbon\Carbon::parse($purchaseOrder['transDate'])->format('d F Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800">Scan QR/Barcode Item</h3>
            <div id="qr-reader" class="mt-4 border rounded-lg overflow-hidden max-w-sm mx-auto"></div>
            @if (session()->has('scan-success'))
                <div class="mt-4 text-center text-sm text-green-600 font-semibold">{{ session('scan-success') }}</div>
            @endif
            @if (session()->has('scan-error'))
                <div class="mt-4 text-center text-sm text-red-600 font-semibold">{{ session('scan-error') }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Item yang Diterima</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Nama Item</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Diharapkan (PO)</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Diterima (Scan)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($items as $itemId => $item)
                        <tr wire:key="received-item-{{ $itemId }}">
                            <td class="p-4 font-medium">{{ $item['item_name'] }}</td>
                            <td class="p-4 text-gray-500">{{ $item['expected_qty'] }}</td>
                            <td class="p-4 font-bold text-lg {{ $item['received_qty'] > 0 ? 'text-blue-600' : 'text-gray-500' }}">
                                <input type="number" wire:model="items.{{ $itemId }}.received_qty" class="w-24 border-gray-300 rounded-md text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg text-sm hover:bg-blue-700">
                Selesai & Lanjutkan ke Quality Check
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const html5QrCode = new Html5Qrcode("qr-reader");
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                // Kirim data hasil scan ke komponen Livewire
                @this.dispatch('itemScanned', { scannedCode: decodedText });
            };
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            // Mulai pemindaian
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

            // Event listener untuk audio feedback (opsional)
            @this.on('play-scan-success-sound', (event) => {
                // Logika untuk memainkan suara 'beep' sukses
            });
            @this.on('play-scan-error-sound', (event) => {
                // Logika untuk memainkan suara 'error'
            });
        });
    </script>
    @endpush
</div>