<style>
    /* Modal Overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }

    /* Modal positioning */
    #do-detail-modal {
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

    #do-detail-modal.hidden {
        display: none !important;
    }

    /* Modal content */
    #do-modal-content {
        max-width: 60rem;
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

    #do-modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }

    /* Clickable row */
    .clickable-row {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .clickable-row:hover {
        background-color: #f9fafb !important;
        transform: scale(1.001);
    }

    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>

<x-app-layout>
    <x-slot name="header">
        Delivery Orders
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Filter Delivery Order</h3>

                <form method="GET" action="{{ route('admin.outbound.delivery-orders.index') }}">
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="search" class="text-sm font-medium text-gray-700">Cari DO Number /
                                Driver</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari nomor DO atau driver...">
                        </div>
                        <div>
                            <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua Status</option>
                                <option value="In Delivery" {{ request('status') === 'In Delivery' ? 'selected' : '' }}>
                                    In Delivery</option>
                                <option value="Delivered" {{ request('status') === 'Delivered' ? 'selected' : '' }}>
                                    Delivered</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.outbound.delivery-orders.index') }}"
                                    class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-700">No</th>
                            <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">DO Number</th>
                            <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Sales Order</th>
                            <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Driver</th>
                            <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[150px]">Delivery Date
                            </th>
                            <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Status</th>
                            <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($deliveryOrders as $do)
                            <tr class="clickable-row" onclick="openDetailModal({{ $do->id }})">
                                <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                                <td class="p-4">
                                    <span class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                                        {{ $do->delivered_no }}
                                    </span>
                                </td>
                                <td class="p-4 text-gray-600">
                                    {{ $do->packingList?->sales_order?->so_number ?? 'N/A' }}
                                </td>
                                <td class="p-4 text-gray-600">{{ $do->driver_name }}</td>
                                <td class="p-4 text-gray-600">
                                    {{ $do->delivery_date ? \Carbon\Carbon::parse($do->delivery_date)->format('d M Y, H:i') : 'N/A' }}
                                </td>
                                <td class="p-4">
                                    @if ($do->status === 'Delivered')
                                        <span
                                            class="inline-flex items-center bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Delivered
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                            <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            In Delivery
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4" onclick="event.stopPropagation()">
                                    <div class="flex items-center gap-2">
                                        @if ($do->status !== 'Delivered')
                                            <button
                                                onclick="markAsDelivered({{ $do->id }}, '{{ $do->delivered_no }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Mark Delivered
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.outbound.delivery-orders.print-pdf', $do->id) }}"
                                            target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-12">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium">Tidak ada data Delivery Order ditemukan</p>
                                        <p class="text-sm mt-1">Delivery Order akan muncul setelah dibuat dari Transit
                                            Inventory</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliveryOrders->hasPages())
                <div class="p-4 border-t">
                    {{ $deliveryOrders->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL DETAIL DELIVERY ORDER --}}
    <div id="do-detail-modal" class="modal-overlay hidden">
        <div id="do-modal-content" class="bg-white rounded-xl shadow-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Detail Delivery Order: <span id="do-number-title" class="ml-2">Loading...</span>
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
            <div class="px-6 py-6">
                <!-- Loading State -->
                <div id="do-loading" class="py-12 text-center">
                    <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-600 mt-4 font-medium">Memuat detail delivery order...</p>
                </div>

                <!-- Content State -->
                <div id="do-content" class="hidden">
                    <!-- Info Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- DO Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-xs text-blue-600 font-semibold uppercase mb-2">Delivery Order</div>
                            <div class="text-lg font-bold text-blue-900 mb-2" id="do-number">-</div>
                            <div id="do-status-badge"></div>
                        </div>

                        <!-- Sales Order Info -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-xs text-green-600 font-semibold uppercase mb-2">Sales Order</div>
                            <div class="text-lg font-bold text-green-900" id="so-number">-</div>
                        </div>

                        <!-- Customer Info -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="text-xs text-purple-600 font-semibold uppercase mb-2">Customer</div>
                            <div class="text-base font-bold text-purple-900" id="customer-name">-</div>
                            <div class="text-xs text-purple-700 mt-1" id="customer-id">-</div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div
                        class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg p-5 mb-6">
                        <h4 class="text-sm font-bold text-orange-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                </path>
                            </svg>
                            Informasi Pengiriman
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Driver</div>
                                <div class="text-sm font-semibold text-gray-900" id="driver-name">-</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Tanggal Pengiriman</div>
                                <div class="text-sm font-semibold text-gray-900" id="delivery-date">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivered Information (if delivered) -->
                    <div id="delivered-info" class="bg-green-50 border border-green-200 rounded-lg p-5 mb-6 hidden">
                        <h4 class="text-sm font-bold text-green-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Penerimaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Tanggal Diterima</div>
                                <div class="text-sm font-semibold text-gray-900" id="delivered-at">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-bold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Daftar Item
                            </h4>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium"
                                id="items-count">0 items</span>
                        </div>
                        <div class="border rounded-lg overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                #</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Product Code</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Product Name</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Label/QR Code</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Quantity</th>
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
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    <span id="created-at-info"></span>
                </div>
                <div class="flex gap-3">
                    <button onclick="closeDetailModal()"
                        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_GET_DO_DETAIL = '/admin/outbound/delivery-orders';

    // Open Detail Modal - Fetch from API
    async function openDetailModal(doId) {
        const modal = document.getElementById('do-detail-modal');
        const loadingState = document.getElementById('do-loading');
        const contentState = document.getElementById('do-content');

        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        contentState.classList.add('hidden');
        document.body.style.overflow = 'hidden';

        const url = `${API_GET_DO_DETAIL}/${doId}/get-details`;

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            // Populate header
            document.getElementById('do-number-title').textContent = data.delivered_no || 'N/A';
            document.getElementById('do-number').textContent = data.delivered_no || 'N/A';

            // Status badge
            const statusBadge = document.getElementById('do-status-badge');
            if (data.status === 'Delivered') {
                statusBadge.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Delivered
                    </span>
                `;
            } else {
                statusBadge.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        In Delivery
                    </span>
                `;
            }

            // SO and Customer Info
            document.getElementById('so-number').textContent = data.so_number || 'N/A';
            document.getElementById('customer-name').textContent = data.customer_name || 'N/A';
            document.getElementById('customer-id').textContent = data.customer_id ? `ID: ${data.customer_id}` : '-';

            // Delivery Information
            document.getElementById('driver-name').textContent = data.driver_name || '-';
            document.getElementById('delivery-date').textContent = data.delivery_date || '-';

            // Delivered Information
            const deliveredInfo = document.getElementById('delivered-info');
            if (data.status === 'Delivered') {
                deliveredInfo.classList.remove('hidden');
                document.getElementById('delivered-at').textContent = data.delivered_at || '-';
            } else {
                deliveredInfo.classList.add('hidden');
            }

            // Created at info
            document.getElementById('created-at-info').textContent = data.created_at ?
                `Dibuat: ${data.created_at}` : '';

            // Populate items table
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
                            <span class="font-mono text-sm font-semibold text-gray-900">${item.product_code || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">${item.product_name || '-'}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-medium bg-indigo-100 text-indigo-800">
                                ${item.label_code || item.qr_code || 'N/A'}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
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
                        <td colspan="5" class="px-4 py-8 text-center">
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

            // Show content, hide loading
            loadingState.classList.add('hidden');
            contentState.classList.remove('hidden');

        } catch (error) {
            console.error('Error fetching DO details:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: 'Terjadi kesalahan saat memuat detail delivery order.',
                confirmButtonColor: '#dc2626'
            });

            closeDetailModal();
        }
    }

    // Close Detail Modal
    function closeDetailModal() {
        const modal = document.getElementById('do-detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal on overlay click
        const modal = document.getElementById('do-detail-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDetailModal();
                }
            });
        }

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('do-detail-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeDetailModal();
                }
            }
        });
    });
</script>

{{-- SCRIPT MARK DELIVERED --}}
<script>
    const API_MARK_DELIVERED = '/admin/outbound/delivery-orders';

    async function markAsDelivered(doId, doNumber) {
        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Pengiriman',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan menandai delivery order ini sebagai <strong class="text-green-600">Delivered</strong>:</p>
                    <div class="bg-blue-50 p-3 rounded-md mt-3">
                        <p><strong>DO Number:</strong> ${doNumber}</p>
                    </div>
                    <p class="text-gray-600 mt-3">Pastikan barang telah diterima oleh customer.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Sudah Diterima',
            cancelButtonText: 'Batal',
            width: '500px'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        Swal.fire({
            title: 'Memproses...',
            html: 'Sedang memperbarui status delivery order...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(`${API_MARK_DELIVERED}/${doId}/mark-delivered`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal memperbarui status');
            }

            await Swal.fire({
                icon: 'success',
                title: 'Status Berhasil Diperbarui!',
                html: `
                    <div class="text-sm space-y-2">
                        <p>Delivery order telah berhasil ditandai sebagai <strong class="text-green-600">Delivered</strong></p>
                        <div class="bg-green-50 p-3 rounded-md mt-2 text-left">
                            <p><strong>DO Number:</strong> ${doNumber}</p>
                            <p><strong>Status:</strong> Delivered</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#16a34a'
            });

            // Reload page
            window.location.reload();

        } catch (error) {
            console.error('Error marking as delivered:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Memperbarui Status',
                text: error.message || 'Terjadi kesalahan saat memperbarui status delivery order.',
                confirmButtonColor: '#dc2626'
            });
        }
    }
</script>
