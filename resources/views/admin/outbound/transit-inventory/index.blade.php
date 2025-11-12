<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Modal Overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }

    /* Modal positioning */
    #detail-modal, #status-update-modal {
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

    #detail-modal.hidden, #status-update-modal.hidden {
        display: none !important;
    }

    /* Modal content */
    #modal-content-container, #status-modal-content {
        max-width: 56rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        z-index: 10000;
    }

    #status-modal-content {
        max-width: 40rem;
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

    #modal-content-container, #status-modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        Create Packing List
    </x-slot>
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Track Barang</h3>
            <div class="flex gap-3 items-start">
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
                        <th class="p-4 text-left font-semibold text-gray-700">No</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Sales Order</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Customer Id</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Total Items</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($deliveryOrders as $do)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors"
                            onclick="openDetailModal({{ $do->packingList->id }})">
                            <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4 text-gray-500">{{ $do->packingList->sales_order->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $do->packingList->sales_order->customer_id ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $do->packingList->items->count() }} Items</td>
                            <td class="p-4">
                                @php
                                    $color = match ($do->status) {
                                        'Shipped' => 'green',
                                        'Ready to Ship' => 'blue',
                                        default => 'yellow',
                                    };
                                @endphp
                                <span class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $do->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-12 text-gray-500">
                                Tidak ada data Packing List ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL STATUS UPDATE --}}
    <div id="status-update-modal" class="modal-overlay hidden">
        <div id="status-modal-content" class="bg-white rounded-xl shadow-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">
                        Informasi Transit Inventory
                    </h3>
                    <button onclick="closeStatusUpdateModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Sales Order</div>
                        <div class="text-lg font-bold text-blue-900" id="status-so-number">-</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="text-xs text-green-600 font-semibold uppercase mb-1">Customer</div>
                        <div class="text-lg font-bold text-green-900" id="status-customer-name">-</div>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="text-xs text-purple-600 font-semibold uppercase mb-1">Total Items</div>
                        <div class="text-lg font-bold text-purple-900" id="status-total-items">-</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <div class="text-xs text-orange-600 font-semibold uppercase mb-1">Packed Date</div>
                        <div class="text-lg font-bold text-orange-900" id="status-packed-date">-</div>
                    </div>
                </div>

                <!-- Current Status -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-600 font-semibold uppercase mb-1">Status Saat Ini</div>
                            <div class="text-lg font-bold text-gray-900" id="status-current">-</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-600 font-semibold uppercase mb-1">Barcode</div>
                            <div class="text-sm font-mono font-bold text-indigo-900" id="status-barcode">-</div>
                        </div>
                    </div>
                </div>

                <!-- Status Update Form -->
                <div class="border-t pt-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Update Status Pengiriman</h4>
                    <form id="statusUpdateForm" class="space-y-4">
                        <input type="hidden" id="update-barcode" name="barcode">
                        
                        <!-- Status Selection -->
                        <div>
                            <label for="new-status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Baru <span class="text-red-500">*</span>
                            </label>
                            <select id="new-status" name="status" required
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">-- Pilih Status --</option>
                                <option value="Delivered">Delivered (Terkirim)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Ubah status menjadi "Delivered" jika barang sudah sampai ke customer</p>
                        </div>

                        <!-- Delivery Date -->
                        <div>
                            <label for="delivery-date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" id="delivery-date" name="delivery_date" required
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Received By -->
                        <div>
                            <label for="received-by" class="block text-sm font-medium text-gray-700 mb-2">
                                Diterima Oleh <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="received-by" name="received_by" required
                                placeholder="Nama penerima barang"
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="delivery-notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Pengiriman
                            </label>
                            <textarea id="delivery-notes" name="notes" rows="3"
                                placeholder="Catatan tambahan (opsional)"
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button onclick="closeStatusUpdateModal()"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Batal
                </button>
                <button onclick="submitStatusUpdate()"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Update Status
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL (existing) --}}
    <div id="detail-modal" class="modal-overlay hidden">
        <div id="modal-content-container" class="bg-white rounded-xl shadow-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">
                        Detail Packing List: <span id="packing-list-number-title">Loading...</span>
                    </h3>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4">
                <!-- Loading State -->
                <div id="loading-modal" class="py-12 text-center">
                    <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-600 mt-4 font-medium">Memuat data item...</p>
                </div>

                <!-- Content State -->
                <div id="content-modal" class="hidden">
                    <!-- Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Sales Order</div>
                            <div class="text-lg font-bold text-blue-900" id="so-number-info">-</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-xs text-green-600 font-semibold uppercase mb-1">Customer</div>
                            <div class="text-lg font-bold text-green-900" id="customer-name-info">-</div>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="text-xs text-purple-600 font-semibold uppercase mb-1">Packed By</div>
                            <div class="text-lg font-bold text-purple-900" id="packed-by-info">-</div>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="text-xs text-orange-600 font-semibold uppercase mb-1">Packed Date</div>
                            <div class="text-lg font-bold text-orange-900" id="packed-at-info">-</div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-bold text-gray-800">Packing Items</h4>
                            <span class="text-sm text-gray-500" id="items-count">0 items</span>
                        </div>
                        <div class="border rounded-lg overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-indigo-50 to-blue-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Label/QR Code</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="items-table-body">
                                        <!-- Items will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button onclick="closeDetailModal()"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Tutup
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

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_VALIDATE_QR = '/admin/outbound/transit-inventory/validate-barcodes';
    const API_UPDATE_STATUS = '/admin/outbound/transit-inventory/update-status'; // Route untuk update status

    // Modal Status Update Functions
    function openStatusUpdateModal(data) {
        const modal = document.getElementById('status-update-modal');
        
        // Populate data
        document.getElementById('status-so-number').textContent = data.so_number || '-';
        document.getElementById('status-customer-name').textContent = data.customer_name || '-';
        document.getElementById('status-total-items').textContent = data.total_items || '-';
        document.getElementById('status-packed-date').textContent = data.packed_date || '-';
        document.getElementById('status-current').textContent = data.status || '-';
        document.getElementById('status-barcode').textContent = data.barcode || '-';
        document.getElementById('update-barcode').value = data.barcode || '';
        
        // Set default delivery date to now
        const now = new Date();
        const formatted = now.toISOString().slice(0, 16);
        document.getElementById('delivery-date').value = formatted;
        
        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Focus on status select
        setTimeout(() => {
            document.getElementById('new-status').focus();
        }, 100);
    }

    function closeStatusUpdateModal() {
        const modal = document.getElementById('status-update-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset form
        document.getElementById('statusUpdateForm').reset();
    }

    // Validate QR Code
    async function validateQrCode(qrCode) {
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
                    barcode: qrCode,
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
            
            // Open status update modal instead of just showing info
            openStatusUpdateModal(result.data);

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

    // Submit Status Update
    async function submitStatusUpdate() {
        const form = document.getElementById('statusUpdateForm');
        const barcode = document.getElementById('update-barcode').value;
        const newStatus = document.getElementById('new-status').value;
        const deliveryDate = document.getElementById('delivery-date').value;
        const receivedBy = document.getElementById('received-by').value.trim();
        const notes = document.getElementById('delivery-notes').value.trim();

        // Validation
        if (!newStatus) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap pilih status baru.'
            });
            document.getElementById('new-status').focus();
            return;
        }

        if (!deliveryDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap isi tanggal pengiriman.'
            });
            document.getElementById('delivery-date').focus();
            return;
        }

        if (!receivedBy) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap isi nama penerima barang.'
            });
            document.getElementById('received-by').focus();
            return;
        }

        // Confirmation
        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Update Status',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan mengupdate status pengiriman:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Barcode:</strong> ${barcode}</p>
                        <p><strong>Status Baru:</strong> <span class="text-green-600 font-bold">${newStatus}</span></p>
                        <p><strong>Diterima Oleh:</strong> ${receivedBy}</p>
                        <p><strong>Tanggal:</strong> ${new Date(deliveryDate).toLocaleString('id-ID')}</p>
                        ${notes ? `<p><strong>Catatan:</strong> ${notes}</p>` : ''}
                    </div>
                    <p class="text-gray-600 mt-3">Pastikan semua data sudah benar.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Periksa Kembali',
            width: '500px'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        // Close modal and show loading
        closeStatusUpdateModal();
        
        Swal.fire({
            title: 'Memproses...',
            html: 'Sedang mengupdate status pengiriman...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(API_UPDATE_STATUS, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    barcode: barcode,
                    status: newStatus,
                    delivery_date: deliveryDate,
                    received_by: receivedBy,
                    notes: notes
                })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mengupdate status');
            }

            // Success
            await Swal.fire({
                icon: 'success',
                title: 'Status Berhasil Diupdate!',
                html: `
                    <div class="text-sm space-y-2">
                        <p>Status pengiriman telah berhasil diupdate:</p>
                        <div class="bg-green-50 p-3 rounded-md mt-2 text-left">
                            <p><strong>Status:</strong> ${newStatus}</p>
                            <p><strong>Diterima Oleh:</strong> ${receivedBy}</p>
                            <p><strong>Tanggal:</strong> ${new Date(deliveryDate).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#16a34a'
            });

            // Reload page
            window.location.reload();

        } catch (error) {
            console.error('Error updating status:', error);
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal Update Status',
                text: error.message || 'Terjadi kesalahan saat mengupdate status. Silakan coba lagi.',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Detail Modal Functions (existing)
    function openDetailModal(packingListId) {
        console.log('Opening modal for ID:', packingListId);

        const modal = document.getElementById('detail-modal');
        const loadingState = document.getElementById('loading-modal');
        const contentState = document.getElementById('content-modal');

        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        contentState.classList.add('hidden');
        document.body.style.overflow = 'hidden';

        const url = `/admin/outbound/packing-lists/${packingListId}/items`;
        console.log('Fetching:', url);

        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);

                document.getElementById('packing-list-number-title').textContent = data.pl_number || 'N/A';
                document.getElementById('so-number-info').textContent = data.so_number || 'N/A';
                document.getElementById('customer-name-info').textContent = data.customer_name || 'N/A';
                document.getElementById('packed-by-info').textContent = data.packed_by || 'System';
                document.getElementById('packed-at-info').textContent = data.packed_at || 'N/A';

                const tableBody = document.getElementById('items-table-body');
                tableBody.innerHTML = '';

                if (data.items && data.items.length > 0) {
                    document.getElementById('items-count').textContent = `${data.items.length} items`;

                    data.items.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50 transition-colors';
                        row.innerHTML = `
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">${index + 1}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">${item.product_code || 'N/A'}</div>
                            <div class="text-sm text-gray-500">${item.product_name || '-'}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-medium bg-indigo-100 text-indigo-800">
                                ${item.label_code || item.qr_code || 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                ${item.quantity || 0}
                            </span>
                        </td>
                    `;
                        tableBody.appendChild(row);
                    });
                } else {
                    document.getElementById('items-count').textContent = '0 items';
                    tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center">
                            <div class="text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada item ditemukan</p>
                            </div>
                        </td>
                    </tr>
                `;
                }

                loadingState.classList.add('hidden');
                contentState.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);

                document.getElementById('packing-list-number-title').textContent = 'Error';
                const tableBody = document.getElementById('items-table-body');
                tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center">
                        <div class="text-red-500">
                            <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-bold">Gagal memuat data</p>
                            <p class="text-sm mt-1">${error.message}</p>
                        </div>
                    </td>
                </tr>
            `;

                loadingState.classList.add('hidden');
                contentState.classList.remove('hidden');
            });
    }

    function closeDetailModal() {
        const modal = document.getElementById('detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        const qrScanForm = document.getElementById('qrScanForm');
        const qrCodeInput = document.getElementById('qrCodeInput');

        qrScanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            validateQrCode(qrCodeInput.value);
            qrCodeInput.value = '';
        });

        // Close modals on overlay click
        document.getElementById('detail-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        document.getElementById('status-update-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusUpdateModal();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const detailModal = document.getElementById('detail-modal');
                const statusModal = document.getElementById('status-update-modal');
                
                if (!detailModal.classList.contains('hidden')) {
                    closeDetailModal();
                }
                if (!statusModal.classList.contains('hidden')) {
                    closeStatusUpdateModal();
                }
            }
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