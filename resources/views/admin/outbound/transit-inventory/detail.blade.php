<x-app-layout>
    <x-slot name="header">
        Timeline Outbound (Transtit Inventory)
    </x-slot>

    <x-outbound.tabs-outbound :packingId="$packingList->id" :status="$packingList->status"/>
    
    <div class="mt-8 bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Track Packing List</h3>
            <div class="flex gap-3 items-start">
                <form id="qrScanForm" class="flex-1 w-full">
                    <input type="text" id="qrCodeInput" name="qr_code"
                        placeholder="Tulis QR Code/Scan QR code di sini"
                        class="border rounded-lg px-3 py-2 w-full flex-1" required />
                </form>
                <button type="button" id="openCameraScanBtn"
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
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-700">Sales Order</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Customer Id</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Total Items</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($packingList->status != 'Packed' && $packingList)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors">
                            <td class="p-4 text-gray-500">{{ $packingList->sales_order->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $packingList->sales_order->customer_id ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $packingList->items->count() }} Items</td>
                            <td class="p-4">
                                @php
                                    $color = match ($packingList->status) {
                                        'Shipped' => 'green',
                                        'Ready to Ship' => 'blue',
                                        default => 'yellow',
                                    };
                                @endphp
                                <span
                                    class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $packingList->status }}
                                </span>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center p-12 text-gray-500">
                                Tidak ada data Packing List ditemukan.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL SCAN --}}
    <div id="scanModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div id="scanModalBg" class="absolute inset-0"></div>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-lg font-semibold mb-4">Scan Item</h2>
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 id="scanModeTitle" class="font-medium text-gray-700">Mode Input Manual</h3>
            </div>
            <form id="scanForm" class="">
                <input type="text" id="qrInput" placeholder="Scan QR Code di sini..."
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" data-action="close-scan-modal"
                        class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Batal</button>
                    <button type="submit" id="submitScanBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Verifikasi
                    </button>
                </div>
            </form>

            <div id="cameraContainer" class="hidden">
                <div id="reader" class="w-full" style="min-height: 250px;"></div>
                <p class="text-xs text-center text-gray-500 mt-2">Arahkan kamera ke QR/Barcode</p>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="cancelCameraBtn" class="px-4 py-2 bg-red-600 text-white rounded-md">
                        Batalkan Scan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Include Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_VALIDATE_QR = '/admin/outbound/timeline/validate-qr'; // Sesuaikan dengan route Anda

    // Fungsi untuk memvalidasi QR Code
    async function validateQrCode(qrCode) {
        const packingId = '{{ $packingList->id }}';
        
        if (!qrCode) return;
        
        Swal.fire({
            title: 'Memvalidasi...',
            text: 'Mencari item dengan QR Code: ' + qrCode,
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        
        try {
            const response = await fetch(API_VALIDATE_QR, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    packing_id: packingId
                })
            });

            const result = await response.json();
            Swal.close();
            
            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Validasi!',
                    text: result.message || 'QR Code tidak valid atau item tidak ditemukan.'
                });
                return;
            }

            // Sukses - tampilkan informasi item
            Swal.fire({
                icon: 'success',
                title: 'Item Ditemukan!',
                html: `
                    <div class="text-left space-y-2">
                        <p><strong>QR Code:</strong> ${result.data.qr_code}</p>
                        <p><strong>Item:</strong> ${result.data.item_name}</p>
                        <p><strong>Quantity:</strong> ${result.data.quantity}</p>
                        <p><strong>Status:</strong> ${result.data.status}</p>
                    </div>
                `,
                confirmButtonText: 'OK'
            });

        } catch (error) {
            console.error('Error saat validasi QR:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Gagal terhubung ke server validasi.'
            });
        }
    }

    // Inisialisasi form scan manual
    document.addEventListener('DOMContentLoaded', function() {
        const qrScanForm = document.getElementById('qrScanForm');
        const qrCodeInput = document.getElementById('qrCodeInput');

        qrScanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            validateQrCode(qrCodeInput.value);
            qrCodeInput.value = '';
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let cameraMode = false;
        let html5QrCode = null;

        const scanModal = document.getElementById('scanModal');
        const scanModalBg = document.getElementById('scanModalBg');
        const qrInput = document.getElementById('qrInput');
        const scanForm = document.getElementById('scanForm');
        const cameraContainer = document.getElementById('cameraContainer');
        const cancelCameraBtn = document.getElementById('cancelCameraBtn');
        const submitScanBtn = document.getElementById('submitScanBtn');
        const scanModeTitle = document.getElementById('scanModeTitle');
        const openCameraBtn = document.getElementById('openCameraScanBtn');

        function updateModalView() {
            scanForm.classList.add('hidden');
            cameraContainer.classList.add('hidden');

            if (cameraMode) {
                cameraContainer.classList.remove('hidden');
                scanModeTitle.textContent = 'Mode Kamera';
            } else {
                scanForm.classList.remove('hidden');
                scanModeTitle.textContent = 'Mode Input Manual';
            }
        }

        function openInputModal() {
            cameraMode = false;
            qrInput.value = '';
            updateModalView();
            scanModal.classList.remove('hidden');
            qrInput.focus();
        }

        function openCameraModal() {
            cameraMode = true;
            qrInput.value = '';
            updateModalView();
            scanModal.classList.remove('hidden');
            startScan();
        }

        function closeModal() {
            scanModal.classList.add('hidden');
            stopScan();
        }

        function startScan() {
            if (html5QrCode) {
                stopScan();
            }

            html5QrCode = new Html5Qrcode('reader');
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            html5QrCode.start(
                { facingMode: 'environment' }, 
                config,
                (decodedText, decodedResult) => {
                    stopScan();
                    closeModal();

                    if (window.validateQrCode) {
                        window.validateQrCode(decodedText);
                        document.querySelector('#qrCodeInput').value = decodedText;
                    } else {
                        alert('Error: Fungsi validasi tidak siap.');
                    }
                },
                (errorMessage) => {}
            ).catch((err) => {
                alert('Gagal memulai kamera. Pastikan Anda memberi izin akses.');
                stopScan();
            });
        }

        function stopScan() {
            if (html5QrCode) {
                try {
                    html5QrCode.stop().then(() => {
                        html5QrCode = null;
                    }).catch(err => {
                        html5QrCode = null;
                    });
                } catch (e) {
                    html5QrCode = null;
                }
            }
            cameraMode = false;
        }

        function handleScanSubmit(event) {
            if (event) event.preventDefault();
            const qrCode = qrInput.value;
            if (!qrCode) return;
            closeModal();
            if (window.validateQrCode) {
                window.validateQrCode(qrCode);
            } else {
                alert('Error: Fungsi validasi tidak siap.');
            }
        }

        if (openCameraBtn) {
            openCameraBtn.addEventListener('click', openCameraModal);
        }

        document.querySelectorAll('[data-action="close-scan-modal"]').forEach(button => {
            button.addEventListener('click', closeModal);
        });

        if (scanModalBg) {
            scanModalBg.addEventListener('click', closeModal);
        }

        cancelCameraBtn.addEventListener('click', () => {
            scanModal.classList.add('hidden');
            stopScan();
        });
        
        scanForm.addEventListener('submit', handleScanSubmit);
    });
</script>