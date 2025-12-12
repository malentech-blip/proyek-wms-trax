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
    
    <div class="bg-white rounded-xl shadow-sm mt-8">
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
                        <button type="button" id="btnConfirmPicked"
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
                            id="btnConfirmDeliverWIP"
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

        {{-- MODAL SCAN --}}
        <div id="scanModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
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

                <form id="scanForm">
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
                        <button type="button" id="cancelCameraBtn" class="px-4 py-2 bg-red-600 text-white rounded-md">
                            Batalkan Scan
                        </button>
                    </div>
                </div>

                <div id="scanResultContainer" class="hidden">
                    <!-- Success Icon -->
                    <div class="flex justify-center mb-4">
                        <div class="rounded-full bg-green-100 p-3">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-center text-lg font-semibold text-gray-900 mb-4">QR Code Valid!</h3>

                    <!-- Item Details Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 mb-4 border border-blue-100">
                        <div class="space-y-3">
                            <!-- Item Name -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-600 mb-1">Item Name</p>
                                    <p class="text-sm font-semibold text-gray-900" id="resultItemName">-</p>
                                </div>
                            </div>

                            <!-- QR Code -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-600 mb-1">QR Code</p>
                                    <p class="text-sm font-mono bg-white px-2 py-1 rounded border border-gray-200" id="resultQrCode">-</p>
                                </div>
                            </div>

                            <!-- Quantity and Picked By Grid -->
                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-blue-200">
                                <!-- Quantity -->
                                <div class="flex items-start gap-2">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Qty Request</p>
                                        <p class="text-lg font-bold text-blue-700" id="resultQty">-</p>
                                    </div>
                                </div>

                                <!-- Picked By -->
                                <div class="flex items-start gap-2">
                                    <div class="flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Picked By</p>
                                        <p class="text-sm font-semibold text-gray-900" id="resultPickedBy">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button type="button" data-action="close-modal"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium transition-colors duration-200">
                            Tutup
                        </button>
                        <button type="button" id="confirmPickBtn"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 rounded-lg text-white font-medium shadow-lg shadow-green-500/30 transition-all duration-200 transform hover:scale-105">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Confirm Pick
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL CONFIRMATION PICKED --}}
        <div id="confirmPickedModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Konfirmasi Material Request Picked?
                                </h3>
                                <p class="text-sm text-gray-500 mt-3">
                                    Mengubah status Material Request No.**<span class="font-semibold text-blue-600">{{ $mr->mr_no }}</span>**.
                                    menjadi <span class="text-black font-bold">Picked</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="confirmPickedBtn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ya, Confirm Picked
                        </button>
                        <button type="button" data-dismiss="confirmPickedModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL CONFIRMATION DELIVERED --}}
        <div id="confirmDeliverWIPModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Konfirmasi Deliver Picking List ke WIP?
                                </h3>
                                <p class="text-sm text-gray-500 mt-3">
                                    Semua Picking List items akan di deliver ke WIP.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" id="confirmDeliverWIPBtn"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ya, Confirm Deliver WIP
                        </button>
                        <button type="button" data-dismiss="confirmDeliverWIPModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // ============================================
    // MODAL MANAGEMENT
    // ============================================
    const ModalManager = {
        show(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        },
        hide(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    };

    // ============================================
    // CONFIRM PICKED FUNCTIONALITY
    // ============================================
    async function confirmPicked() {
        ModalManager.hide('confirmPickedModal');

        const mrId = '{{ $mrId }}';
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

        try {
            const response = await fetch('/admin/production/material-request/change-status', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
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

            Swal.fire('Berhasil!', result.message || 'Status material request Picked berhasil diubah.', 'success')
                .then(() => {
                    window.location.reload();
                });

        } catch (error) {
            console.error('Final Creation Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan jaringan atau sistem.', 'error');
        }
    }

    // ============================================
    // DELIVER TO WIP FUNCTIONALITY
    // ============================================
    async function deliveredToWIP() {
        ModalManager.hide('confirmDeliverWIPModal');
        
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

        try {
            const response = await fetch('/admin/production/work-in-progress/create-wip', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
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

            Swal.fire('Berhasil!', result.message || 'Berhasil deliver picking list ke WIP.', 'success')
                .then(() => {
                    let redirectUrl = `{{ route('admin.production.wip.detail', ['mr_id' => '__ID__']) }}`;
                    window.location = redirectUrl.replace('__ID__', mrId);
                });

        } catch (error) {
            console.error('Final Creation Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan jaringan atau sistem.', 'error');
        }
    }

    // ============================================
    // SCAN MODAL FUNCTIONALITY
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        // State Management
        const state = {
            cameraMode: false,
            scanResult: null,
            html5QrCode: null,
            isScanningAPI: false,
            isConfirmingPick: false
        };

        // DOM Elements
        const elements = {
            scanModal: document.getElementById('scanModal'),
            scanModalOverlay: document.getElementById('scanModalOverlay'),
            qrInput: document.getElementById('qrInput'),
            scanForm: document.getElementById('scanForm'),
            cameraContainer: document.getElementById('cameraContainer'),
            scanResultContainer: document.getElementById('scanResultContainer'),
            toggleCameraBtn: document.getElementById('toggleCameraBtn'),
            cancelCameraBtn: document.getElementById('cancelCameraBtn'),
            submitScanBtn: document.getElementById('submitScanBtn'),
            confirmPickBtn: document.getElementById('confirmPickBtn'),
            scanModeTitle: document.getElementById('scanModeTitle'),
            resultItemName: document.getElementById('resultItemName'),
            resultQrCode: document.getElementById('resultQrCode'),
            resultQty: document.getElementById('resultQty'),
            resultPickedBy: document.getElementById('resultPickedBy'),
            globalScanBtn: document.getElementById('openGlobalScanBtn'),
            btnConfirmPicked: document.getElementById('btnConfirmPicked'),
            btnConfirmDeliverWIP: document.getElementById('btnConfirmDeliverWIP'),
            confirmPickedBtn: document.getElementById('confirmPickedBtn'),
            confirmDeliverWIPBtn: document.getElementById('confirmDeliverWIPBtn')
        };

        // Update Modal View
        function updateModalView() {
            elements.scanForm.classList.add('hidden');
            elements.cameraContainer.classList.add('hidden');
            elements.scanResultContainer.classList.add('hidden');

            if (state.scanResult) {
                elements.scanResultContainer.classList.remove('hidden');
                elements.resultItemName.textContent = state.scanResult.item_name || '-';
                elements.resultQrCode.textContent = state.scanResult.qr_code || '-';
                elements.resultQty.textContent = state.scanResult.quantity || '-';
                elements.resultPickedBy.textContent = state.scanResult.picked_by || '-';
            } else if (state.cameraMode) {
                elements.cameraContainer.classList.remove('hidden');
                elements.scanModeTitle.textContent = 'Mode Kamera';
                elements.toggleCameraBtn.textContent = 'Gunakan Input';
                elements.toggleCameraBtn.classList.replace('bg-blue-500', 'bg-red-500');
                elements.toggleCameraBtn.classList.replace('hover:bg-blue-600', 'hover:bg-red-600');
            } else {
                elements.scanForm.classList.remove('hidden');
                elements.scanModeTitle.textContent = 'Mode Input Manual';
                elements.toggleCameraBtn.textContent = 'Gunakan Kamera';
                elements.toggleCameraBtn.classList.replace('bg-red-500', 'bg-blue-500');
                elements.toggleCameraBtn.classList.replace('hover:bg-red-600', 'hover:bg-blue-600');
            }
        }

        // Open Modal
        function openModal() {
            state.scanResult = null;
            state.cameraMode = false;
            elements.qrInput.value = '';
            updateModalView();
            elements.scanModal.classList.remove('hidden');
            elements.qrInput.focus();
        }

        // Close Modal
        function closeModal() {
            elements.scanModal.classList.add('hidden');
            stopScan();
        }

        // Start Camera Scan
        function startScan() {
            if (state.html5QrCode) return;
            
            state.html5QrCode = new Html5Qrcode('reader');
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            };

            state.html5QrCode.start(
                { facingMode: 'environment' },
                config,
                (decodedText) => {
                    elements.qrInput.value = decodedText;
                    stopScan();
                    handleScanSubmit();
                },
                (errorMessage) => {
                    // Ignore scan errors
                }
            ).catch((err) => {
                console.error('Gagal memulai kamera:', err);
                Swal.fire({
                    icon: 'warning',
                    title: 'Kamera Tidak Tersedia',
                    text: 'Gagal memulai kamera. Pastikan Anda memberi izin akses kamera.',
                    confirmButtonColor: '#3B82F6'
                });
                stopScan();
            });
        }

        // Stop Camera Scan
        function stopScan() {
            if (state.html5QrCode) {
                state.html5QrCode.stop().then(() => {
                    state.html5QrCode = null;
                }).catch(err => {
                    console.error('Gagal menghentikan scanner:', err);
                });
            }
            state.cameraMode = false;
            updateModalView();
        }

        // Toggle Camera Mode
        function toggleCamera() {
            state.cameraMode = !state.cameraMode;
            if (state.cameraMode) {
                startScan();
            } else {
                stopScan();
            }
            updateModalView();
        }

        // Handle Scan Submit
        async function handleScanSubmit(event) {
            if (event) event.preventDefault();
            if (state.isScanningAPI) return;

            state.isScanningAPI = true;
            elements.submitScanBtn.disabled = true;
            elements.submitScanBtn.textContent = 'Memproses...';

            try {
                const res = await fetch('{{ route('admin.production.picking-list.scan-item') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        qr_code: elements.qrInput.value,
                        mr_id: '{{ $mrId }}'
                    }),
                });
                
                const data = await res.json();
                
                if (data.success) {
                    state.scanResult = data.data;
                    updateModalView();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Scan Gagal',
                        text: data.message,
                        confirmButtonColor: '#3B82F6'
                    });
                    state.scanResult = null;
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: err.message || 'Gagal memproses scan QR Code',
                    confirmButtonColor: '#3B82F6'
                });
            } finally {
                state.isScanningAPI = false;
                elements.submitScanBtn.disabled = false;
                elements.submitScanBtn.textContent = 'Verifikasi';
            }
        }

        // Handle Confirm Pick
        async function handleConfirmPick() {
            if (!state.scanResult || state.isConfirmingPick) return;

            state.isConfirmingPick = true;
            elements.confirmPickBtn.disabled = true;
            elements.confirmPickBtn.textContent = 'Menyimpan...';

            try {
                const res = await fetch('{{ route('admin.production.picking-list.confirm-pick') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        picking_id: state.scanResult.id
                    }),
                });
                
                const data = await res.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        closeModal();
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                        confirmButtonColor: '#3B82F6'
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: err.message || 'Gagal melakukan confirm pick',
                    confirmButtonColor: '#3B82F6'
                });
            } finally {
                state.isConfirmingPick = false;
                elements.confirmPickBtn.disabled = false;
                elements.confirmPickBtn.textContent = 'Confirm Pick';
            }
        }

        // Event Listeners - Scan Modal
        if (elements.globalScanBtn) {
            elements.globalScanBtn.addEventListener('click', openModal);
        }

        document.querySelectorAll('[data-action="close-modal"]').forEach(button => {
            button.addEventListener('click', closeModal);
        });

        if (elements.scanModalOverlay) {
            elements.scanModalOverlay.addEventListener('click', closeModal);
        }

        if (elements.toggleCameraBtn) {
            elements.toggleCameraBtn.addEventListener('click', toggleCamera);
        }

        if (elements.cancelCameraBtn) {
            elements.cancelCameraBtn.addEventListener('click', stopScan);
        }

        if (elements.scanForm) {
            elements.scanForm.addEventListener('submit', handleScanSubmit);
        }

        if (elements.confirmPickBtn) {
            elements.confirmPickBtn.addEventListener('click', handleConfirmPick);
        }

        // Event Listeners - Confirmation Modals
        if (elements.btnConfirmPicked) {
            elements.btnConfirmPicked.addEventListener('click', () => {
                ModalManager.show('confirmPickedModal');
            });
        }

        if (elements.btnConfirmDeliverWIP) {
            elements.btnConfirmDeliverWIP.addEventListener('click', () => {
                ModalManager.show('confirmDeliverWIPModal');
            });
        }

        if (elements.confirmPickedBtn) {
            elements.confirmPickedBtn.addEventListener('click', confirmPicked);
        }

        if (elements.confirmDeliverWIPBtn) {
            elements.confirmDeliverWIPBtn.addEventListener('click', deliveredToWIP);
        }

        // Close modal buttons
        document.querySelectorAll('[data-dismiss]').forEach(button => {
            button.addEventListener('click', (e) => {
                const modalId = e.target.getAttribute('data-dismiss');
                ModalManager.hide(modalId);
            });
        });

        // Close modal when clicking overlay
        document.querySelectorAll('.modal-container .fixed.inset-0.bg-gray-500').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal-container');
                if (modal) {
                    ModalManager.hide(modal.id);
                }
            });
        });
    });
</script>