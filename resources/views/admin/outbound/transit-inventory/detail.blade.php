<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
    /* Modal Overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }

    /* Modal positioning */
    #transit-status-modal, #delivery-order-modal {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1rem !important;
    }

    #transit-status-modal.hidden, #delivery-order-modal.hidden {
        display: none !important;
    }

    /* Modal content */
    #transit-modal-content, #do-modal-content {
        max-width: 42rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        z-index: 10000;
    }

    /* Smooth animations */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    #transit-modal-content, #do-modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        Timeline Outbound (Transtit Inventory)
    </x-slot>

    <x-outbound.tabs-outbound :packingId="$transitInventory->packingList->id" :status="$transitInventory->packingList->status" />

    <div class="mt-8 bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Track Packing List</h3>
                
                {{-- Tombol Buat Delivery Order --}}
                @if($transitInventory->packingList->status === 'Ready to Ship')
                    <button onclick="openDeliveryOrderModal()" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Buat Delivery Order
                    </button>
                @endif
            </div>
            
            <div class="flex gap-3 items-start mt-3">
                <form id="qrScanForm" class="flex-1 w-full">
                    <input type="text" id="qrCodeInput" name="qr_code" placeholder="Tulis QR Code/Scan QR code di sini"
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
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Customer Name</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Total Items</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Transit At</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Transit Out</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($transitInventory->packingList->status != 'Packed' && $transitInventory)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors">
                            <td class="p-4 text-gray-500">
                                {{ $transitInventory->packingList->sales_order->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $customer['name'] ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $transitInventory->packingList->items->count() }} Items
                            </td>
                            <td class="p-4 text-gray-500">{{ $transitInventory->transit_in_at }}</td>
                            <td class="p-4 text-gray-500">{{ $transitInventory->transit_out_at ?? '--' }}</td>
                            <td class="p-4">
                                @php
                                    $color = match ($transitInventory->packingList->status) {
                                        'Shipped' => 'green',
                                        'Ready to Ship' => 'blue',
                                        default => 'yellow',
                                    };
                                @endphp
                                <span
                                    class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $transitInventory->packingList->status }}
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

    {{-- MODAL DELIVERY ORDER --}}
    <div id="delivery-order-modal" class="modal-overlay hidden">
        <div id="do-modal-content" class="bg-white rounded-xl shadow-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Buat Delivery Order
                    </h3>
                    <button onclick="closeDeliveryOrderModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <!-- Summary Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Sales Order</div>
                        <div class="text-lg font-bold text-blue-900">{{ $transitInventory->packingList->sales_order->so_number ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-xs text-green-600 font-semibold uppercase mb-1">Customer</div>
                        <div class="text-base font-bold text-green-900">{{ $customer['name'] ?? 'N/A' }}</div>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="text-xs text-purple-600 font-semibold uppercase mb-1">Total Items</div>
                        <div class="text-lg font-bold text-purple-900">{{ $transitInventory->packingList->items->count() }} items</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="text-xs text-orange-600 font-semibold uppercase mb-1">Packed Date</div>
                        <div class="text-sm font-bold text-orange-900">{{ $transitInventory->packingList->packed_at ?? 'N/A' }}</div>
                    </div>
                </div>

                <!-- Form -->
                <form id="deliveryOrderForm" class="space-y-4">
                    <input type="hidden" name="packing_list_id" value="{{ $transitInventory->packingList->id }}">
                    
                    <!-- Delivery Date -->
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="delivery_date" name="delivery_date" required
                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <p class="text-xs text-gray-500 mt-1">Tanggal pengiriman barang ke customer</p>
                    </div>

                    <!-- Driver Name -->
                    <div>
                        <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Driver <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="driver_name" name="driver_name" required
                            placeholder="Masukkan nama driver"
                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>

                    <!-- Vehicle Number -->
                    <div>
                        <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="vehicle_number" name="vehicle_number" required
                            placeholder="Contoh: B 1234 XYZ"
                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm uppercase">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                            No. Telepon Driver <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone_number" name="phone_number" required
                            placeholder="Contoh: 081234567890"
                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Catatan
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                            placeholder="Catatan tambahan (opsional)"
                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button onclick="closeDeliveryOrderModal()"
                    class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Batal
                </button>
                <button onclick="submitDeliveryOrder()"
                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Buat Delivery Order
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL UPDATE STATUS TRANSIT --}}
    <div id="transit-status-modal" class="modal-overlay hidden">
        <div id="transit-modal-content" class="bg-white rounded-xl shadow-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        Validasi Transit Inventory
                    </h3>
                    <button onclick="closeTransitModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <!-- Update Status Form -->
                <div>
                    <form id="transitStatusUpdateForm" class="space-y-4">
                        <input type="hidden" id="transit-barcode" name="barcode">
                        <input type="hidden" id="transit-packing-id" name="packing_id">

                        <!-- Transit Status Selection -->
                        <div>
                            <label for="transit-status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Transit <span class="text-red-500">*</span>
                            </label>
                            <select id="transit-status" name="status" required
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Status</option>
                                <option value="In Transit">In Transit</option>
                                <option value="Ready to Ship">Ready to Ship</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih status sesuai kondisi barang saat ini</p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button onclick="closeTransitModal()"
                    class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Batal
                </button>
                <button onclick="submitTransitStatusUpdate()"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm flex items-center">
                    Update Status
                </button>
            </div>
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

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_VALIDATE_QR = '/admin/outbound/transit-inventory/validate-barcode';
    const API_UPDATE_TRANSIT = '/admin/outbound/transit-inventory/update-status';
    const API_CREATE_DO = '/admin/outbound/delivery-orders/store';

    // Delivery Order Modal Functions
    function openDeliveryOrderModal() {
        const modal = document.getElementById('delivery-order-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Set default date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('delivery_date').value = today;
        
        // Focus on first input
        setTimeout(() => {
            document.getElementById('delivery_date').focus();
        }, 100);
    }

    function closeDeliveryOrderModal() {
        const modal = document.getElementById('delivery-order-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset form
        document.getElementById('deliveryOrderForm').reset();
    }

    // Submit Delivery Order
    async function submitDeliveryOrder() {
        const form = document.getElementById('deliveryOrderForm');
        const formData = new FormData(form);
        
        const data = {
            packing_list_id: formData.get('packing_list_id'),
            delivery_date: formData.get('delivery_date'),
            driver_name: formData.get('driver_name').trim(),
            vehicle_number: formData.get('vehicle_number').trim().toUpperCase(),
            phone_number: formData.get('phone_number').trim(),
            notes: formData.get('notes').trim()
        };

        // Validation
        if (!data.delivery_date) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Tanggal pengiriman harus diisi.'
            });
            return;
        }

        if (!data.driver_name) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Nama driver harus diisi.'
            });
            document.getElementById('driver_name').focus();
            return;
        }

        if (!data.vehicle_number) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Nomor kendaraan harus diisi.'
            });
            document.getElementById('vehicle_number').focus();
            return;
        }

        if (!data.phone_number) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'No. telepon driver harus diisi.'
            });
            document.getElementById('phone_number').focus();
            return;
        }

        // Phone validation
        const phoneRegex = /^[0-9]{10,15}$/;
        if (!phoneRegex.test(data.phone_number)) {
            Swal.fire({
                icon: 'warning',
                title: 'Format Tidak Valid',
                text: 'No. telepon harus berisi 10-15 digit angka.'
            });
            document.getElementById('phone_number').focus();
            return;
        }

        // Confirmation
        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Pembuatan DO',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan membuat Delivery Order dengan detail:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Tanggal Kirim:</strong> ${data.delivery_date}</p>
                        <p><strong>Driver:</strong> ${data.driver_name}</p>
                        <p><strong>Kendaraan:</strong> ${data.vehicle_number}</p>
                        <p><strong>No. Telepon:</strong> ${data.phone_number}</p>
                        ${data.notes ? `<p><strong>Catatan:</strong> ${data.notes}</p>` : ''}
                    </div>
                    <p class="text-gray-600 mt-3">Pastikan semua data sudah benar.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Buat DO',
            cancelButtonText: 'Periksa Kembali',
            width: '500px'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        // Close modal and show loading
        closeDeliveryOrderModal();
        
        Swal.fire({
            title: 'Memproses...',
            html: 'Sedang membuat Delivery Order...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(API_CREATE_DO, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal membuat Delivery Order');
            }

            // Success
            await Swal.fire({
                icon: 'success',
                title: 'Delivery Order Berhasil Dibuat!',
                html: `
                    <div class="text-sm space-y-2">
                        <p>Delivery Order telah berhasil dibuat:</p>
                        <div class="bg-green-50 p-3 rounded-md mt-2 text-left">
                            <p><strong>DO Number:</strong> ${result.do_number || 'Generating...'}</p>
                            <p><strong>Driver:</strong> ${data.driver_name}</p>
                            <p><strong>Kendaraan:</strong> ${data.vehicle_number}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#16a34a'
            });

            // Redirect or reload
            window.location.reload();

        } catch (error) {
            console.error('Error creating delivery order:', error);
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat Delivery Order',
                text: error.message || 'Terjadi kesalahan saat membuat delivery order.',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Transit Modal Functions
    function openTransitModal(data) {
        const modal = document.getElementById('transit-status-modal');
        document.getElementById('transit-barcode').value = data.barcode || '';
        document.getElementById('transit-packing-id').value = data.packing_id || '';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById("transit-status").value = data?.status;
        setTimeout(() => {
            document.getElementById('transit-status').focus();
        }, 100);
    }

    function closeTransitModal() {
        const modal = document.getElementById('transit-status-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('transitStatusUpdateForm').reset();
    }

    // Validate QR Code
    async function validateQrCode(qrCode) {
        const packingId = '{{ $transitInventory->packingList->id }}';

        if (!qrCode) return;

        Swal.fire({
            title: 'Memvalidasi...',
            text: 'Mencari item dengan QR Code: ' + qrCode,
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        try {
            const response = await fetch(`${API_VALIDATE_QR}/${packingId}`, {
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

            openTransitModal(result.data);

        } catch (error) {
            console.error('Error saat validasi QR:', error);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Gagal terhubung ke server validasi.'
            });
        }
    }

    // Submit Transit Status Update
    async function submitTransitStatusUpdate() {
        const form = document.getElementById('transitStatusUpdateForm');
        const barcode = document.getElementById('transit-barcode').value;
        const packingId = document.getElementById('transit-packing-id').value;
        const status = document.getElementById('transit-status').value;

        if (!status) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap pilih status transit.'
            });
            document.getElementById('transit-status').focus();
            return;
        }

        closeTransitModal();

        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Update Status',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan mengupdate status transit:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Status Baru:</strong> <span class="text-indigo-600 font-bold">${status}</span></p>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Periksa Kembali',
            width: '500px'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        Swal.fire({
            title: 'Memproses...',
            html: 'Sedang mengupdate status transit...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const transitId = '{{ $transitInventory->id }}';
            const response = await fetch(`${API_UPDATE_TRANSIT}/${transitId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengupdate status');
            }

            await Swal.fire({
                icon: 'success',
                title: 'Status Berhasil Diupdate!',
                html: `
                    <div class="text-sm space-y-2">
                        <p>Status transit telah berhasil diupdate:</p>
                        <div class="bg-green-50 p-3 rounded-md mt-2 text-left">
                            <p><strong>Status:</strong> ${status}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4f46e5'
            });

            window.location.reload();

        } catch (error) {
            console.error('Error updating transit status:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Update Status',
                text: error.message || 'Terjadi kesalahan saat mengupdate status.',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        const qrScanForm = document.getElementById('qrScanForm');
        const qrCodeInput = document.getElementById('qrCodeInput');

        qrScanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            validateQrCode(qrCodeInput.value);
            qrCodeInput.value = '';
        });

        // Close modals on overlay click
        document.getElementById('transit-status-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTransitModal();
            }
        });

        document.getElementById('delivery-order-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeliveryOrderModal();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const transitModal = document.getElementById('transit-status-modal');
                const doModal = document.getElementById('delivery-order-modal');
                
                if (!transitModal.classList.contains('hidden')) {
                    closeTransitModal();
                }
                if (!doModal.classList.contains('hidden')) {
                    closeDeliveryOrderModal();
                }
            }
        });

        // Auto format vehicle number
        document.getElementById('vehicle_number').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

        // Phone number validation
        document.getElementById('phone_number').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    });
</script>

<script>
    // Camera Scanner Implementation
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