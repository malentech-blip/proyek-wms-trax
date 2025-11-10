<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
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
                            <label for="search" class="text-sm font-medium text-gray-700">Cari DO Number / Driver</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500" 
                                placeholder="Cari nomor DO atau driver...">
                        </div>
                        <div>
                            <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="In Delivery" {{ request('status') === 'In Delivery' ? 'selected' : '' }}>In Delivery</option>
                                <option value="Delivered" {{ request('status') === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                                Cari
                            </button>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('admin.outbound.delivery-orders.index') }}" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">DO Number</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Sales Order</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Driver Name</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Delivery Date</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($deliveryOrders as $do)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-gray-700 font-medium cursor-pointer" onclick="openDetailModal({{ $do->id }})">
                                <span class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $do->delivered_no }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-500">{{ $do->packingList?->salesOrder?->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $do->driver_name }}</td>
                            <td class="p-4 text-gray-500">
                                {{ $do->delivery_date ? \Carbon\Carbon::parse($do->delivery_date)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="p-4">
                                @if($do->status === 'Delivered')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $do->status }}
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $do->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <button onclick="openDetailModal({{ $do->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detail
                                    </button>
                                    @if($do->status !== 'Delivered')
                                        <form method="POST" action="{{ route('admin.outbound.delivery-orders.mark-delivered', $do->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                onclick="return confirm('Are you sure you want to mark this delivery order as Delivered?')"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                                Mark Delivered
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.outbound.delivery-orders.print-pdf', $do->id) }}" 
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-12 text-gray-500">
                                Tidak ada data Delivery Order ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deliveryOrders->hasPages())
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Detail Delivery Order: <span id="do-number-title" class="ml-2">Loading...</span>
                    </h3>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
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
                            <div class="text-lg font-bold text-blue-900 mb-1" id="do-number">-</div>
                            <div class="text-xs text-blue-700" id="do-status-badge">-</div>
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
                    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg p-5 mb-6">
                        <h4 class="text-sm font-bold text-orange-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                            Informasi Pengiriman
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Driver</div>
                                <div class="text-sm font-semibold text-gray-900" id="driver-name">-</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 mb-1">No. Kendaraan</div>
                                <div class="text-sm font-semibold text-gray-900" id="vehicle-number">-</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 mb-1">No. Telepon</div>
                                <div class="text-sm font-semibold text-gray-900" id="phone-number">-</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Tanggal Pengiriman</div>
                                <div class="text-sm font-semibold text-gray-900" id="delivery-date">-</div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-orange-200" id="delivery-notes-container" style="display:none;">
                            <div class="text-xs text-gray-600 mb-1">Catatan</div>
                            <div class="text-sm text-gray-700 italic" id="delivery-notes">-</div>
                        </div>
                    </div>

                    <!-- Delivered Information (if delivered) -->
                    <div id="delivered-info" class="bg-green-50 border border-green-200 rounded-lg p-5 mb-6 hidden">
                        <h4 class="text-sm font-bold text-green-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Penerimaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Diterima Oleh</div>
                                <div class="text-sm font-semibold text-gray-900" id="received-by">-</div>
                            </div>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Daftar Item
                            </h4>
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full" id="items-count">0 items</span>
                        </div>
                        <div class="border rounded-lg overflow-hidden shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Item Code</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Item Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Unit</th>
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
                    <button onclick="printDO()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <button onclick="closeDetailModal()"
                        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_GET_DO_DETAIL = '/admin/outbound/delivery-orders'; // Base URL

    // Open Detail Modal
    function openDetailModal(doId) {
        console.log('Opening DO detail modal for ID:', doId);

        const modal = document.getElementById('do-detail-modal');
        const loadingState = document.getElementById('do-loading');
        const contentState = document.getElementById('do-content');

        // Show modal with loading state
        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        contentState.classList.add('hidden');
        document.body.style.overflow = 'hidden';

        const url = `${API_GET_DO_DETAIL}/${doId}/getDetails`;
        console.log('Fetching:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);

            // Populate header
            document.getElementById('do-number-title').textContent = data.do_number || 'N/A';
            document.getElementById('do-number').textContent = data.do_number || 'N/A';

            // Status badge
            const statusBadge = document.getElementById('do-status-badge');
            if (data.status === 'Delivered') {
                statusBadge.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">✓ Delivered</span>';
            } else {
                statusBadge.innerHTML = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">⏱ In Transit</span>';
            }

            // SO and Customer Info
            document.getElementById('so-number').textContent = data.so_number || 'N/A';
            document.getElementById('customer-name').textContent = data.customer_name || 'N/A';
            document.getElementById('customer-id').textContent = `ID: ${data.customer_id || '-'}`;

            // Delivery Information
            document.getElementById('driver-name').textContent = data.driver_name || '-';
            document.getElementById('vehicle-number').textContent = data.vehicle_number || '-';
            document.getElementById('phone-number').textContent = data.phone_number || '-';
            document.getElementById('delivery-date').textContent = data.delivery_date || '-';

            // Notes
            if (data.notes && data.notes.trim() !== '') {
                document.getElementById('delivery-notes-container').style.display = 'block';
                document.getElementById('delivery-notes').textContent = data.notes;
            } else {
                document.getElementById('delivery-notes-container').style.display = 'none';
            }

            // Delivered Information
            const deliveredInfo = document.getElementById('delivered-info');
            if (data.status === 'Delivered' && data.received_by) {
                deliveredInfo.classList.remove('hidden');
                document.getElementById('received-by').textContent = data.received_by || '-';
                document.getElementById('delivered-at').textContent = data.delivered_at || '-';
            } else {
                deliveredInfo.classList.add('hidden');
            }

            // Created at info
            document.getElementById('created-at-info').textContent = data.created_at ? `Dibuat: ${data.created_at}` : '';

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
                            <span class="font-mono text-sm font-semibold text-gray-900">${item.item_code || 'N/A'}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">${item.item_name || '-'}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                                ${item.quantity || 0}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">${item.unit || 'pcs'}</td>
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
        })
        .catch(error => {
            console.error('Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: 'Terjadi kesalahan saat memuat detail delivery order.',
                confirmButtonColor: '#dc2626'
            });

            closeDetailModal();
        });
    }

    // Close Detail Modal
    function closeDetailModal() {
        const modal = document.getElementById('do-detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Print DO
    function printDO() {
        window.print();
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal on overlay click
        document.getElementById('do-detail-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('do-detail-modal');
                if (!modal.classList.contains('hidden')) {
                    closeDetailModal();
                }
            }
        });
    });
</script>