<div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h2 class="text-lg font-semibold mb-4">Sales Order</h2>
        @if ($salesOrder)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div><span class="text-gray-500">SO Number:</span> <span
                        class="font-medium">{{ $salesOrder['number'] ?? '-' }}</span></div>
                <div><span class="text-gray-500">Date:</span> <span
                        class="font-medium">{{ $salesOrder['transDate'] }}</span>
                </div>
                <div><span class="text-gray-500">Customer:</span> <span
                        class="font-medium">{{ $salesOrder['customer']['name'] ?? '-' }}</span></div>
            </div>
            @php
                $items = $salesOrder['detailItem'] ?? [];
            @endphp
            @foreach ($items as $item)
                <div class="grid grid-cols-4 gap-5 mt-10">
                    <div class="flex flex-col gap-2">
                        <p class="text-gray-500 text-sm">Item Name</p>
                        <p class="font-medium">{{ $item['detailName'] }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-gray-500 text-sm">Item Quantity</p>
                        <p class="font-medium">{{ $item['quantity'] }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-gray-500 text-sm">Item Price</p>
                        <p class="font-medium">{{ formatRupiah($item['unitPrice']) }}</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <p class="text-gray-500 text-sm">Total Price</p>
                        <p class="font-medium">{{ formatRupiah($item['totalPrice']) }}</p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-sm text-gray-600">Detail SO tidak tersedia. Anda tetap dapat melakukan scan.</div>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm space-y-4">
        <h3 class="font-semibold">Scan Finished Goods</h3>
        <div class="flex gap-3 items-start">
            <input type="text" wire:model.defer="qrCode" wire:keydown.enter.prevent="scanFinishedGood"
                placeholder="Scan QR code di sini" class="border rounded-lg px-3 py-2 w-full flex-1" />
            <button type="button" wire:click="openQrScanner"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1">
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Camera
            </button>
            <button type="button" wire:click="openManualItemModal"
                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 flex items-center gap-1">
                Tambah Item Manual
            </button>
        </div>
        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white w-full">Cari Item</button>
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
                                <button class="px-2 py-1 text-sm rounded border"
                                    wire:click="removeScanned({{ $idx }})">Hapus</button>
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
        <button wire:click="savePackingList" class="px-4 py-2 rounded-lg bg-green-600 text-white"
            wire:loading.attr="disabled">Simpan & Buat DO</button>
    </div>


    <div id="camera-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 items-center justify-center"
        style="display: none;">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Scan QR Code</h3>
                <button type="button" onclick="closeCamera()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            {{-- Video element untuk kamera --}}
            <video id="camera-video" class="w-full rounded-lg" autoplay playsinline></video>
            <p class="text-sm text-gray-500 mt-4 text-center">Arahkan kamera ke QR code</p>
        </div>
    </div>

    {{-- MODAL TAMBAH ITEM MANUAL --}}
    <div x-data="{ show: @entangle('showManualItemModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
        style="display: none;"> {{-- Livewire/Alpine akan mengontrol display: none --}}

        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" **wire:click="closeManualItemModal"**>
        </div>

        {{-- Modal Panel --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Tambah Item Manual
                            </h3>
                            <div class="mt-4 space-y-4">

                                {{-- Input Kode Item --}}
                                <div>
                                    <label for="manualItemCode" class="block text-sm font-medium text-gray-700">Kode
                                        Item</label>
                                    <input type="text" id="manualItemCode" **wire:model.defer="manualItemCode"**
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                    @error('manualItemCode')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Input Quantity --}}
                                <div>
                                    <label for="manualQuantity"
                                        class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <input type="number" id="manualQuantity" **wire:model.defer="manualQuantity"**
                                        min="1"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                                    @error('manualQuantity')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" **wire:click="addManualItem"**
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tambahkan
                    </button>
                    <button type="button" **wire:click="closeManualItemModal"**
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>
    <script>
        let qrScanner = null;
        let stream = null;
        const video = document.getElementById('camera-video');
        const modal = document.getElementById('camera-modal');

        // Fungsi untuk memulai QR Scanner
        function startCamera() {
            modal.style.display = 'flex'; // Tampilkan modal
            modal.classList.remove('hidden');

            // Meminta akses kamera
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    }
                })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    video.srcObject = mediaStream;

                    // Setelah video dimuat, mulai QR Scanner
                    video.onloadedmetadata = () => {
                        if (qrScanner) { // Hentikan scanner lama jika ada
                            qrScanner.stop();
                            qrScanner.destroy();
                        }

                        qrScanner = new QrScanner(
                            video,
                            result => {
                                // Handler ketika QR code terdeteksi
                                const qrCodeValue = result.data;
                                closeCamera(); // Tutup modal/scanner

                                // Mengirim hasil scan ke Livewire Component
                                @this.set('qrCode', qrCodeValue);
                                @this.call('scanFinishedGood'); // Panggil fungsi Livewire untuk proses data
                            }, {
                                returnDetailedScanResult: true,
                                highlightScanRegion: true,
                                // Anda bisa mengatur preferredCamera: 'environment' jika tidak otomatis memilih kamera belakang
                            }
                        );
                        qrScanner.start();
                    };
                })
                .catch(function(err) {
                    console.error('Error accessing camera:', err);
                    alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
                    closeCamera(); // Tutup modal jika gagal
                });
        }

        // Fungsi untuk menutup QR Scanner dan mematikan kamera
        function closeCamera() {
            if (qrScanner) {
                qrScanner.stop();
                qrScanner.destroy();
                qrScanner = null;
            }

            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            modal.style.display = 'none';
            modal.classList.add('hidden');
        }

        // Livewire listener untuk memanggil startCamera dari komponen
        window.addEventListener('open-qr-scanner', () => {
            startCamera();
        });
    </script>
@endpush
