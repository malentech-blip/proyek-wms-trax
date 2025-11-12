<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-app-layout>
    <x-slot name="header">
        Timeline Production (Picking List)
    </x-slot>
    @php
        $hasUnpickedItem = $pickingList->contains(function ($pl) {
            return is_null($pl->date_picked);
        });
        $confirmPickedDisabled = $hasUnpickedItem;
    @endphp

    <x-production.tabs-production :mrId="$mrId" :wip="$wip" />
    <div x-data="{
        open: false,
        qr_code: '',
        scanning: false,
        scanResult: null,
        submittingConfirm: false,
    
        openModal(id) {
            this.qr_code = '';
            this.scanResult = null;
            this.open = true;
            this.$nextTick(() => this.$refs.qrInput.focus());
        },
    
        async submitScan() {
            this.scanning = true;
            try {
                const res = await fetch('{{ route('admin.production.picking-list.scan-item') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        qr_code: this.qr_code,
                    }),
                });
                const data = await res.json();
                this.scanning = false;
    
                console.log(data)
    
                if (data.success) {
                    this.scanResult = data.data;
                } else {
                    alert('❌ ' + data.message);
                    this.scanResult = null;
                }
            } catch (err) {
                this.scanning = false;
                console.error(err);
                alert('Terjadi error: ' + err.message);
            }
        },
    
        async confirmPick() {
            if (!this.scanResult) return;
    
            this.submittingConfirm = true;
            try {
                const res = await fetch('{{ route('admin.production.picking-list.confirm-pick') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        picking_id: this.scanResult.id,
                    }),
                });
                const data = await res.json();
                this.submittingConfirm = false;
    
                if (data.success) {
                    alert('✅ ' + data.message);
                    this.open = false;
                    window.location.reload(); // Refresh table
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (err) {
                this.submittingConfirm = false;
                alert('Terjadi error: ' + err.message);
            }
        },
    }" class="bg-white rounded-xl shadow-sm mt-8">
        <div class="p-6 max-sm:overflow-x-auto max-sm:min-w-full border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Detail Picking List</h3>
            <div class="flex flex-col gap-3 mt-2">
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[70px] w-[200px] text-sm text-gray-600">MR. No:</p>
                    <p class="font-medium max-sm:min-w-[150px]">{{ $mr->mr_no }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[70px] w-[200px] text-sm text-gray-600">Status:</p>
                    <p class="font-medium max-sm:min-w-[150px]">{{ $mr->status }}</p>
                </div>
                <div class="flex items-center gap-3 mt-3">
                    @if ($confirmPickedDisabled)
                        <button type="button" id="openGlobalScanBtn"
                            class="w-max border-none rounded py-2 px-4 bg-green-500 text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400">
                            Scan Item
                        </button>
                    @endif
                    @if ($mr->status == 'Requested' && !$confirmPickedDisabled)
                        <button type="button" onclick="showConfirmPickedModal()" id="btnConfirmPicked"
                            {{ $confirmPickedDisabled ? 'disabled' : '' }}
                            class="w-max border-none rounded py-2 px-4 
                            {{ $confirmPickedDisabled
                                ? 'bg-gray-400 text-gray-100 cursor-not-allowed'
                                : 'bg-blue-500 text-white hover:bg-blue-600' }}">
                            Confirm Picked
                        </button>
                    @endif
                    @if ($mr->status == 'Picked')
                        <button type="button" {{ $mr->status == 'Requested' ? 'disabled' : '' }}
                            onclick="showConfirmDeliverWIPModal()" id="btnConfirmDeliverWIP"
                            class="w-max border-none rounded py-2 px-4 
                            {{ $mr->status == 'Requested'
                                ? 'bg-gray-400 text-gray-100 cursor-not-allowed'
                                : 'bg-orange-500 text-white hover:bg-orange-600' }}">
                            Deliver to WIP
                        </button>
                    @endif
                </div>
            </div>
        </div>
        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[60px]">No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[120px]">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[200px]">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[120px]">Rack</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Qty Request</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Qty Ready</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Picked By</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Date Picked</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($pickingList as $pl)
                        <tr>
                            <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $pl->item_code }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->item_name }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->location_name ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->rack_code ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->quantity ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->item_quantity ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->picked_by ?? '-' }}</td>
                            <td class="p-4 text-gray-500">
                                @if ($pl->date_picked)
                                    {{ \Carbon\Carbon::parse($pl->date_picked)->format('d/m/Y') }}
                                @else
                                    -Not yet picked-
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center p-12 text-gray-500">
                                Tidak ada data Picking List ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Scan -->
        <div x-show="open" x-cloak
            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50" x-transition>
            <div @click.away="open = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-lg font-semibold mb-4">Scan Item</h2>

                <!-- Input Scan -->
                <form @submit.prevent="submitScan" x-show="!scanResult">
                    <input type="text" x-model="qr_code" x-ref="qrInput" placeholder="Scan QR Code di sini..."
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md"
                            x-text="scanning ? 'Memproses...' : 'Verifikasi'"></button>
                    </div>
                </form>

                <!-- Hasil Scan -->
                <div x-show="scanResult" class="space-y-3">
                    <div class="border-t pt-4 mt-4">
                        <p><strong>Item:</strong> <span x-text="scanResult.item_name"></span></p>
                        <p><strong>QR Code:</strong> <span x-text="scanResult.qr_code"></span></p>
                        <p><strong>Qty Request:</strong> <span x-text="scanResult.quantity"></span></p>
                        <p><strong>Picked By:</strong> <span x-text="scanResult.picked_by"></span></p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button @click="open = false"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Tutup</button>
                        <button @click="confirmPick" class="px-4 py-2 bg-green-600 text-white rounded-md"
                            x-text="submittingConfirm ? 'Menyimpan...' : 'Confirm Pick'"></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL CONFIRMATION --}}
        <div id="confirmPickedModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div onclick="hideConfirmPickedModal()"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Konfirmasi Material Request Picked?
                                </h3>
                                <p class="text-sm text-gray-500 mt-3">
                                    Mengubah status Material Request No.**<span id="soNumberDisplay"
                                        class="font-semibold text-blue-600">{{ $mr->mr_no }}</span>**.
                                    menjadi <span class="text-black font-bold">Picked</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="confirmPicked()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ya, Confirm Picked
                        </button>
                        <button type="button" onclick="hideConfirmPickedModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL CONFIRMATION DELIVERED --}}
        <div id="confirmDeliverWIPModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto modal-container" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div onclick="hideConfirmDeliverWIPModal()"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Konfirmasi Deliver Picking List ke WIP?
                                </h3>
                                <p class="text-sm text-gray-500 mt-3">
                                    Semua Picking List items akan di deliver ke WIP.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="deliveredToWIP()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ya, Confirm Deliver WIP
                        </button>
                        <button type="button" onclick="hideConfirmDeliverWIPModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div id="scanModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div id="scanModalOverlay" class="absolute inset-0"></div>

            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <h2 class="text-lg font-semibold mb-4">Scan Item</h2>

                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 id="scanModeTitle" class="font-medium text-gray-700">Mode Input Manual</h3>
                    <button id="toggleCameraBtn" type="button"
                        class="px-3 py-1 text-sm text-white rounded-md transition duration-150 bg-blue-500 hover:bg-blue-600">
                        <span>Gunakan Kamera</span>
                    </button>
                </div>

                <form id="scanForm" class="">
                    <input type="text" id="qrInput" placeholder="Scan QR Code di sini..."
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" data-action="close-modal"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Batal</button>
                        <button type="submit" id="submitScanBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md">
                            Verifikasi
                        </button>
                    </div>
                </form>

                <div id="cameraContainer" class="hidden">
                    <div id="reader" class="w-full" style="min-height: 250px;"></div>
                    <p class="text-xs text-center text-gray-500 mt-2">Arahkan kamera ke QR/Barcode</p>
                    <div class="mt-4 flex justify-end">
                        <button type"button" id="cancelCameraBtn" class="px-4 py-2 bg-red-600 text-white rounded-md">
                            Batalkan Scan
                        </button>
                    </div>
                </div>

                <div id="scanResultContainer" class="hidden space-y-3">
                    <div class="border-t pt-4 mt-4">
                        <p><strong>Item:</strong> <span id="resultItemName">-</span></p>
                        <p><strong>QR Code:</strong> <span id="resultQrCode">-</span></p>
                        <p><strong>Qty Request:</strong> <span id="resultQty">-</span></p>
                        <p><strong>Picked By:</strong> <span id="resultPickedBy">-</span></p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" data-action="close-modal"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Tutup</button>
                        <button type="button" id="confirmPickBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-md">
                            Confirm Pick
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    window.showConfirmPickedModal = function() {
        // Dipanggil oleh Alpine (pastikan isFormValid = true)
        const modal = document.getElementById('confirmPickedModal')
        if (modal) {
            modal.style.display = 'block';
        }
    }
    window.hideConfirmPickedModal = function() {
        const modal = document.getElementById('confirmPickedModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    window.showConfirmDeliverWIPModal = function() {
        // Dipanggil oleh Alpine (pastikan isFormValid = true)
        const modal = document.getElementById('confirmDeliverWIPModal')
        if (modal) {
            modal.style.display = 'block';
        }
    }
    window.hideConfirmDeliverWIPModal = function() {
        const modal = document.getElementById('confirmDeliverWIPModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    window.confirmPicked = async function() {
        hideConfirmPickedModal(); // Sembunyikan modal konfirmasi

        const mrId = '{{ $mrId }}';
        // 1. Siapkan Payload JSON Lengkap
        const payload = {
            _token: '{{ csrf_token() }}',
            mr_id: mrId
        };

        Swal.fire({
            title: 'Update status material request Picked',
            text: 'Sedang memproses dokumen di server.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // 2. Kirim ke API Endpoint Baru
        try {
            const response = await fetch('/admin/production/material-request/change-status', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json', // Kirim sebagai JSON
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                const errorMessage = result.message || 'Gagal update status material request';
                Swal.fire('Gagal!', errorMessage, 'error');
                return;
            }

            Swal.fire('Berhasil!', result.message || 'Status material request Picked berhasil diubah.',
                    'success')
                .then(() => {
                    window.location.reload();
                });

        } catch (error) {
            console.error('Final Creation Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan jaringan atau sistem.', 'error');
        }
    }

    window.deliveredToWIP = async function() {
        hideConfirmDeliverWIPModal();
        const mrId = '{{ $mrId }}';

        const payload = {
            _token: '{{ csrf_token() }}',
            mr_id: mrId
        };

        Swal.fire({
            title: 'Deliver Picking List ke WIP',
            text: 'Sedang memproses dokumen di server.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // 2. Kirim ke API Endpoint Baru
        try {
            const response = await fetch('/admin/production/work-in-progress/create-wip', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Kirim sebagai JSON
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                const errorMessage = result.message || 'Gagal deliver picking list ke WIP';
                Swal.fire('Gagal!', errorMessage, 'error');
                return;
            }

            Swal.fire('Berhasil!', result.message || 'Berhasil deliver picking list ke WIP.',
                    'success')
                .then(() => {
                    let redirectUrl = `{{ route('admin.production.wip.detail', ['mr_id' => '__ID__']) }}`;
                    window.location = redirectUrl.replace('__ID__', mrId);
                });

        } catch (error) {
            console.error('Final Creation Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan jaringan atau sistem.', 'error');
        }
    }


    document.addEventListener('DOMContentLoaded', () => {

        // --- Variabel State ---
        let cameraMode = false;
        let scanResult = null;
        let html5QrCode = null;
        let isScanningAPI = false;
        let isConfirmingPick = false;

        // --- Seleksi Elemen DOM ---
        const scanModal = document.getElementById('scanModal');
        const scanModalOverlay = document.getElementById('scanModalOverlay');
        const qrInput = document.getElementById('qrInput');
        const scanForm = document.getElementById('scanForm');
        const cameraContainer = document.getElementById('cameraContainer');
        const scanResultContainer = document.getElementById('scanResultContainer');
        const toggleCameraBtn = document.getElementById('toggleCameraBtn');
        const cancelCameraBtn = document.getElementById('cancelCameraBtn');
        const submitScanBtn = document.getElementById('submitScanBtn');
        const confirmPickBtn = document.getElementById('confirmPickBtn');
        const scanModeTitle = document.getElementById('scanModeTitle');
        const resultItemName = document.getElementById('resultItemName');
        const resultQrCode = document.getElementById('resultQrCode');
        const resultQty = document.getElementById('resultQty');
        const resultPickedBy = document.getElementById('resultPickedBy');

        // [DIUBAH] Menyeleksi tombol scan global
        const globalScanBtn = document.getElementById('openGlobalScanBtn');

        // --- Helper Functions ---
        // ... (Fungsi updateModalView() tidak berubah)
        function updateModalView() {
            scanForm.classList.add('hidden');
            cameraContainer.classList.add('hidden');
            scanResultContainer.classList.add('hidden');

            if (scanResult) {
                scanResultContainer.classList.remove('hidden');
                resultItemName.textContent = scanResult.item_name || '-';
                resultQrCode.textContent = scanResult.qr_code || '-';
                resultQty.textContent = scanResult.quantity || '-';
                resultPickedBy.textContent = scanResult.picked_by || '-';
            } else if (cameraMode) {
                cameraContainer.classList.remove('hidden');
                scanModeTitle.textContent = 'Mode Kamera';
                toggleCameraBtn.textContent = 'Gunakan Input';
                toggleCameraBtn.classList.replace('bg-blue-500', 'bg-red-500');
                toggleCameraBtn.classList.replace('hover:bg-blue-600', 'hover:bg-red-600');
            } else {
                scanForm.classList.remove('hidden');
                scanModeTitle.textContent = 'Mode Input Manual';
                toggleCameraBtn.textContent = 'Gunakan Kamera';
                toggleCameraBtn.classList.replace('bg-red-500', 'bg-blue-500');
                toggleCameraBtn.classList.replace('hover:bg-red-600', 'hover:bg-blue-600');
            }
        }

        // ... (Fungsi openModal() tidak berubah)
        function openModal() {
            scanResult = null;
            cameraMode = false;
            qrInput.value = '';
            updateModalView();
            scanModal.classList.remove('hidden');
            qrInput.focus();
        }

        // ... (Fungsi closeModal() tidak berubah)
        function closeModal() {
            scanModal.classList.add('hidden');
            stopScan();
        }

        // ... (Fungsi startScan() tidak berubah)
        function startScan() {
            if (html5QrCode) return;
            html5QrCode = new Html5Qrcode('reader');
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };
            html5QrCode.start({
                    facingMode: 'environment'
                }, config,
                (decodedText, decodedResult) => {
                    qrInput.value = decodedText;
                    stopScan();
                    handleScanSubmit();
                },
                (errorMessage) => {}
            ).catch((err) => {
                console.error('Gagal memulai kamera:', err);
                alert('Gagal memulai kamera. Pastikan Anda memberi izin.');
                stopScan();
            });
        }

        // ... (Fungsi stopScan() tidak berubah)
        function stopScan() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode = null;
                }).catch(err => {
                    console.error('Gagal menghentikan scanner:', err);
                });
            }
            cameraMode = false;
            updateModalView();
        }

        // ... (Fungsi toggleCamera() tidak berubah)
        function toggleCamera() {
            cameraMode = !cameraMode;
            if (cameraMode) {
                startScan();
            } else {
                stopScan();
            }
            updateModalView();
        }

        // ... (Fungsi handleScanSubmit() tidak berubah)
        async function handleScanSubmit(event) {
            if (event) event.preventDefault();
            if (isScanningAPI) return;

            isScanningAPI = true;
            submitScanBtn.disabled = true;
            submitScanBtn.textContent = 'Memproses...';

            try {
                const res = await fetch('{{ route('admin.production.picking-list.scan-item') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        qr_code: qrInput.value,
                        mr_id: '{{ $mrId }}'
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    scanResult = data.data;
                    updateModalView();
                } else {
                    alert('❌ ' + data.message);
                    scanResult = null;
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi error: ' + err.message);
            } finally {
                isScanningAPI = false;
                submitScanBtn.disabled = false;
                submitScanBtn.textContent = 'Verifikasi';
            }
        }

        async function handleConfirmPick() {
            if (!scanResult || isConfirmingPick) return;

            isConfirmingPick = true;
            confirmPickBtn.disabled = true;
            confirmPickBtn.textContent = 'Menyimpan...';

            try {
                const res = await fetch('{{ route('admin.production.picking-list.confirm-pick') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        picking_id: scanResult.id
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    alert('✅ ' + data.message);
                    closeModal();
                    window.location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (err) {
                alert('Terjadi error: ' + err.message);
            } finally {
                isConfirmingPick = false;
                confirmPickBtn.disabled = false;
                confirmPickBtn.textContent = 'Confirm Pick';
            }
        }

        if (globalScanBtn) {
            globalScanBtn.addEventListener('click', openModal);
        }

        document.querySelectorAll('[data-action="close-modal"]').forEach(button => {
            button.addEventListener('click', closeModal);
        });
        scanModalOverlay.addEventListener('click', closeModal);
        toggleCameraBtn.addEventListener('click', toggleCamera);
        cancelCameraBtn.addEventListener('click', stopScan);
        scanForm.addEventListener('submit', handleScanSubmit);
        confirmPickBtn.addEventListener('click', handleConfirmPick);
    });
</script>
