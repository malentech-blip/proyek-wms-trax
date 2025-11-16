<style>
    /* Modal Overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }

    /* Modal positioning */
    #detail-modal,
    #status-update-modal,
    #delivery-order-modal {
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

    #detail-modal.hidden,
    #status-update-modal.hidden,
    #delivery-order-modal.hidden {
        display: none !important;
    }

    /* Modal content */
    #modal-content-container,
    #status-modal-content,
    #do-modal-content {
        max-width: 56rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        z-index: 10000;
    }

    #status-modal-content,
    #do-modal-content {
        max-width: 42rem;
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

    #modal-content-container,
    #status-modal-content,
    #do-modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        Transit Inventory
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
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Transit at</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Transit out</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[180px]">Status</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[180px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($transitInventories as $transit)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                            onclick="openDetailModal({{ $transit->packing_id }})">
                            <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4 text-gray-500 cursor-pointer">
                                <span
                                    class="hover:underline">{{ $transit->packingList->sales_order->so_number ?? 'N/A' }}</span>
                            </td>
                            <td class="p-4 text-gray-500">
                                {{ $transit->packingList->sales_order->customer_id ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $transit->packingList->items->count() }} Items</td>
                            <td class="p-4 text-gray-500">{{ $transit->transit_in_at }}</td>
                            <td class="p-4 text-gray-500">{{ $transit->transit_out_at ?? '--' }}</td>
                            <td class="p-4">
                                @php
                                    $color = match ($transit->packingList->status) {
                                        'Shipped' => 'green',
                                        'Ready to Ship' => 'blue',
                                        default => 'yellow',
                                    };
                                @endphp
                                <span
                                    class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $transit->packingList->status }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if ($transit->packingList->status === 'Ready to Ship')
                                    <button onclick="openDeliveryOrderModal(event, {{ $transit->packing_id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        Buat DO
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-12 text-gray-500">
                                Tidak ada data Transit Inventory ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL DELIVERY ORDER --}}
    <div id="delivery-order-modal" class="modal-overlay hidden">
        <div id="do-modal-content" class="bg-white rounded-xl shadow-2xl">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Buat Delivery Order
                    </h3>
                    <button onclick="closeDeliveryOrderModal()"
                        class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="loading-do-modal" class="px-6 py-12 text-center">
                <svg class="animate-spin h-12 w-12 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-600 mt-4 font-medium">Memuat data transit inventory...</p>
            </div>
            <div id="content-do-modal" class="hidden">
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Sales Order</div>
                            <div class="text-lg font-bold text-blue-900" id="do-so-number">-</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-xs text-green-600 font-semibold uppercase mb-1">Customer</div>
                            <div class="text-base font-bold text-green-900" id="do-customer-name">-</div>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="text-xs text-purple-600 font-semibold uppercase mb-1">Total Items</div>
                            <div class="text-lg font-bold text-purple-900" id="do-total-items">-</div>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="text-xs text-orange-600 font-semibold uppercase mb-1">Packed Date</div>
                            <div class="text-sm font-bold text-orange-900" id="do-packed-date">-</div>
                        </div>
                    </div>

                    <form id="deliveryOrderForm" class="space-y-4">
                        <input type="hidden" id="do-packing-list-id" name="packing_list_id">
                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pengiriman <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="delivery_date" name="delivery_date" required
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <p class="text-xs text-gray-500 mt-1">Tanggal pengiriman barang ke customer</p>
                        </div>
                        <div>
                            <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Driver <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="driver_name" name="driver_name" required
                                placeholder="Masukkan nama driver"
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                    <button onclick="closeDeliveryOrderModal()"
                        class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Batal
                    </button>
                    <button onclick="submitDeliveryOrder()"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Buat Delivery Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL STATUS UPDATE --}}
    <div id="status-update-modal" class="modal-overlay hidden">
        <div id="status-modal-content" class="bg-white rounded-xl shadow-2xl">
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
                    <button onclick="closeStatusUpdateModal()"
                        class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-6">
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
                <div class="border-t pt-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Update Status Transit</h4>
                    <form id="statusUpdateForm" class="space-y-4">
                        <input type="hidden" id="transit-id" name="transit-id">
                        <div>
                            <label for="transit-status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status Transit <span class="text-red-500">*</span>
                            </label>
                            <select id="transit-status" name="status" required
                                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Status</option>
                                <option value="In Transit">In Transit</option>
                                <option value="Ready to Ship">Ready to Ship</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih status sesuai kondisi barang saat ini</p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                <button onclick="closeStatusUpdateModal()"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Batal
                </button>
                <button onclick="submitStatusUpdate()"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Update Status
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detail-modal" class="modal-overlay hidden">
        <div id="modal-content-container" class="bg-white rounded-xl shadow-2xl">
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
            <div class="px-6 py-4">
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
                <div id="content-modal" class="hidden">
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
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                #</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Product</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Label/QR Code</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="items-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

{{-- SCRIPT VALIDATE BARCODE/QR --}}
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_VALIDATE_QR = '/admin/outbound/transit-inventory/validate-barcodes';


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
        ['detail-modal', 'status-update-modal', 'delivery-order-modal'].forEach(modalId => {
            document.getElementById(modalId).addEventListener('click', function(e) {
                if (e.target === this) {
                    if (modalId === 'detail-modal') closeDetailModal();
                    if (modalId === 'status-update-modal') closeStatusUpdateModal();
                    if (modalId === 'delivery-order-modal') closeDeliveryOrderModal();
                }
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['detail-modal', 'status-update-modal', 'delivery-order-modal'].forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (!modal.classList.contains('hidden')) {
                        if (modalId === 'detail-modal') closeDetailModal();
                        if (modalId === 'status-update-modal') closeStatusUpdateModal();
                        if (modalId === 'delivery-order-modal') closeDeliveryOrderModal();
                    }
                });
            }
        });

        // Auto format vehicle number
        const vehicleInput = document.getElementById('vehicle_number');
        if (vehicleInput) {
            vehicleInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });
        }

        // Phone number validation
        const phoneInput = document.getElementById('phone_number');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }
    });
</script>

{{-- SCRIPT OPEN DETAIL MODAL --}}
<script>
    // Detail Modal Functions
    function openDetailModal(packingListId) {
        const modal = document.getElementById('detail-modal');
        const loadingState = document.getElementById('loading-modal');
        const contentState = document.getElementById('content-modal');

        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        contentState.classList.add('hidden');
        document.body.style.overflow = 'hidden';

        const url = `/admin/outbound/packing-lists/${packingListId}/items`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
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
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                            <p class="text-lg font-medium">Tidak ada item ditemukan</p>
                        </td>
                    </tr>
                `;
                }

                loadingState.classList.add('hidden');
                contentState.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                loadingState.classList.add('hidden');
                contentState.classList.remove('hidden');
            });
    }

    function closeDetailModal() {
        const modal = document.getElementById('detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

{{-- SCRIPT OPEN MODAL UPDATE STATUS --}}
<script>
    const API_UPDATE_STATUS = '/admin/outbound/transit-inventory/update-status';

    function openStatusUpdateModal(data) {
        const modal = document.getElementById('status-update-modal');
        document.getElementById('status-so-number').textContent = data.so_number || '-';
        document.getElementById('status-customer-name').textContent = data.customer_name || '-';
        document.getElementById('status-total-items').textContent = data.total_items || '-';
        document.getElementById('status-packed-date').textContent = data.packed_date || '-';
        document.getElementById('transit-status').value = data.status || '';
        document.getElementById('transit-id').value = data.transit_id || '';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            document.getElementById('transit-status').focus();
        }, 100);
    }

    function closeStatusUpdateModal() {
        const modal = document.getElementById('status-update-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('statusUpdateForm').reset();
    }

    async function submitStatusUpdate() {
        const form = document.getElementById('statusUpdateForm');
        const status = document.getElementById('transit-status').value;
        const transitId = document.getElementById('transit-id').value;

        if (!status) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap pilih status baru.'
            });
            document.getElementById('transit-status').focus();
            return;
        }

        closeStatusUpdateModal();

        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Update Status',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan mengupdate status transit:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Status Transit:</strong> <span class="text-indigo-600 font-bold">${status}</span></p>
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
            const response = await fetch(`${API_UPDATE_STATUS}/${transitId}`, {
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
                            <p><strong>Status Transit:</strong> ${status}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4f46e5'
            });

            window.location.reload();

        } catch (error) {
            console.error('Error updating status:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Update Status',
                text: error.message || 'Terjadi kesalahan saat mengupdate status.',
                confirmButtonColor: '#dc2626'
            });
        }
    }
</script>

{{-- SCRIPT OPEN DELIVERY MODAL --}}
<script>
    const API_CREATE_DO = '/admin/outbound/transit-inventory/create-do';

    async function openDeliveryOrderModal(event, packingListId) {
        event.stopPropagation();
        
        const modal = document.getElementById('delivery-order-modal');
        const loadingState = document.getElementById('loading-do-modal');
        const contentState = document.getElementById('content-do-modal');
        const packingIdDo = document.getElementById('packing-id-do');
        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        contentState.classList.add('hidden');
        document.body.style.overflow = 'hidden';

        const url = `/admin/outbound/packing-lists/${packingListId}/items`;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            const data = await response.json();

            document.getElementById('do-so-number').textContent = data.so_number || 'N/A';
            document.getElementById('do-customer-name').textContent = data.customer_name || 'N/A';
            document.getElementById('do-total-items').textContent = (data.items?.length || 0) + ' items';
            document.getElementById('do-packed-date').textContent = data.packed_at || 'N/A';
            document.getElementById('do-packing-list-id').value = packingListId;

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('delivery_date').value = today;

            loadingState.classList.add('hidden');
            contentState.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('delivery_date').focus();
            }, 100);
        } catch (error) {
            console.error('Error fetching packing list details:', error);  
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: 'Tidak dapat memuat detail packing list. Silakan coba lagi.',
                confirmButtonColor: '#dc2626'
            });
            closeDeliveryOrderModal();
        }
    }

    function closeDeliveryOrderModal() {
        const modal = document.getElementById('delivery-order-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        const form = document.getElementById('deliveryOrderForm');
        if (form) {
            form.reset();
        }
    }

    async function submitDeliveryOrder() {
        const form = document.getElementById('deliveryOrderForm');
        const formData = new FormData(form);
        const packingListId = formData.get("packing_list_id");

        const data = {
            delivery_date: formData.get('delivery_date'),
            driver_name: formData.get('driver_name').trim(),
        };

        closeDeliveryOrderModal()

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

        // Confirmation
        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Pembuatan DO',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan membuat Delivery Order dengan detail:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Tanggal Kirim:</strong> ${data.delivery_date}</p>
                        <p><strong>Driver:</strong> ${data.driver_name}</p>
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
            const response = await fetch(`${API_CREATE_DO}/${packingListId}`, {
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
            })
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
</script>

{{-- CAMERA SCANNER SCRIPT --}}
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
        const openCameraBtn = document.getElementById('openCameraScanBtn');

        function updateModalView() {
            scanForm.classList.add('hidden');
            cameraContainer.classList.add('hidden');
            if (cameraMode) {
                cameraContainer.classList.remove('hidden');
                document.getElementById('scanModeTitle').textContent = 'Mode Kamera';
            } else {
                scanForm.classList.remove('hidden');
                document.getElementById('scanModeTitle').textContent = 'Mode Input Manual';
            }
        }

        function closeModal() {
            scanModal.classList.add('hidden');
            stopScan();
        }

        function startScan() {
            if (html5QrCode) stopScan();
            html5QrCode = new Html5Qrcode('reader');
            html5QrCode.start({
                    facingMode: 'environment'
                }, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                (decodedText) => {
                    stopScan();
                    closeModal();
                    if (window.validateQrCode) {
                        window.validateQrCode(decodedText);
                        document.querySelector('#qrCodeInput').value = decodedText;
                    }
                },
                () => {}
            ).catch(() => {
                alert('Gagal memulai kamera.');
                stopScan();
            });
        }

        function stopScan() {
            if (html5QrCode) {
                try {
                    html5QrCode.stop().then(() => html5QrCode = null).catch(() => html5QrCode = null);
                } catch (e) {
                    html5QrCode = null;
                }
            }
            cameraMode = false;
        }

        if (openCameraBtn) {
            openCameraBtn.addEventListener('click', () => {
                cameraMode = true;
                updateModalView();
                scanModal.classList.remove('hidden');
                startScan();
            });
        }

        document.querySelectorAll('[data-action="close-scan-modal"]').forEach(button => {
            button.addEventListener('click', closeModal);
        });

        if (scanModalBg) scanModalBg.addEventListener('click', closeModal);
        if (cancelCameraBtn) cancelCameraBtn.addEventListener('click', closeModal);

        scanForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const qrCode = qrInput.value;
            if (!qrCode) return;
            closeModal();
            if (window.validateQrCode) window.validateQrCode(qrCode);
        });
    });
</script>