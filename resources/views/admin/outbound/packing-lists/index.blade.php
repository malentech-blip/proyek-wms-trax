<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Modal Overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }

    /* SweetAlert Above Modal */
    .swal-above-modal {
        z-index: 20000 !important;
    }

    /* Modal positioning */
    #detail-modal,
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
    #delivery-order-modal.hidden {
        display: none !important;
    }

    /* Modal content */
    #modal-content-container,
    #do-modal-content {
        max-width: 56rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        z-index: 10000;
    }

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
    #do-modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        List Packing List (Outbound)
    </x-slot>
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Filter Packing List</h3>

            <form method="GET" action="{{ route('admin.outbound.packing-lists.index') }}">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="packed_date" class="text-sm font-medium text-gray-700">Tanggal Packing</label>
                        <input type="date" name="packed_date" id="packed_date" value="{{ request('packed_date') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="search" class="text-sm font-medium text-gray-700">Cari SO Number</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Cari nomor PL atau SO...">
                    </div>
                    <div>
                        <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="Packed" {{ request('status') === 'Packed' ? 'selected' : '' }}>Packed
                            </option>
                            <option value="In Transit" {{ request('status') === 'In Transit' ? 'selected' : '' }}>In
                                Transit</option>
                            <option value="Ready to Ship" {{ request('status') === 'Ready to Ship' ? 'selected' : '' }}>
                                Ready to Ship</option>
                            <option value="Shipped" {{ request('status') === 'Shipped' ? 'selected' : '' }}>Shipped
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        @if (request()->hasAny(['search', 'status', 'packed_date']))
                            <a href="{{ route('admin.outbound.packing-lists.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 shadow-sm">
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
                <thead class="bg-gradient-to-r from-indigo-50 to-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-700">No</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Sales Order</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Packed By</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Packed Date</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[150px]">Status</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[250px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($packingLists as $pl)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                            onclick="openDetailModal({{ $pl->id }})">
                            <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4">
                                <span class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                                    {{ $pl->sales_order->so_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-600">{{ $pl->packed_by ?? 'System' }}</td>
                            <td class="p-4 text-gray-600">
                                {{ $pl->packed_at ? \Carbon\Carbon::parse($pl->packed_at)->format('d M Y, H:i') : 'N/A' }}
                            </td>
                            <td class="p-4">
                                @php
                                    $statusConfig = match ($pl->status) {
                                        'Shipped' => ['color' => 'green', 'icon' => 'M5 13l4 4L19 7'],
                                        'Ready to Ship' => [
                                            'color' => 'blue',
                                            'icon' =>
                                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                        ],
                                        'In Transit' => [
                                            'color' => 'orange',
                                            'icon' =>
                                                'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
                                        ],
                                        default => [
                                            'color' => 'yellow',
                                            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                        ],
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center bg-{{ $statusConfig['color'] }}-100 text-{{ $statusConfig['color'] }}-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $statusConfig['icon'] }}"></path>
                                    </svg>
                                    {{ $pl->status }}
                                </span>
                            </td>
                            <td class="p-4" onclick="event.stopPropagation()">
                                @if ($pl->status === 'Packed')
                                    <div class="flex items-center gap-2">
                                        <button onclick="openDeliveryOrderModal(event, {{ $pl->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            Buat DO
                                        </button>
                                        <button
                                            onclick="confirmTransit(event, {{ $pl->id }}, '{{ $pl->sales_order->so_number ?? 'N/A' }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-xs font-semibold rounded-lg hover:bg-orange-700 transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                                </path>
                                            </svg>
                                            Transit
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
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
                                    <p class="text-lg font-medium">Tidak ada data Packing List ditemukan</p>
                                    <p class="text-sm mt-1">Packing List akan muncul setelah proses packing selesai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($packingLists->hasPages())
            <div class="p-4 border-t">
                {{ $packingLists->links() }}
            </div>
        @endif
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

            <!-- Loading State -->
            <div id="loading-do-modal" class="px-6 py-12 text-center">
                <svg class="animate-spin h-12 w-12 text-green-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-600 mt-4 font-medium">Memuat data packing list...</p>
            </div>

            <!-- Content State -->
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
                        Buat Delivery Order
                    </button>
                </div>
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
                                    <tbody class="bg-white divide-y divide-gray-200" id="items-table-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-gray-50 px-6 py-4 rounded-b-xl justify-end">
                <button onclick="timelineOutbound()"
                    class="hidden px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Lihat Timeline
                </button>
                <button onclick="closeDetailModal()"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL TRANSIT CONFIRMATION --}}
    <div id="transit-modal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
                <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4 rounded-t-xl">
                    <h3 class="text-xl font-bold text-white">Konfirmasi Transit</h3>
                </div>

                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-700 mb-2">
                                Apakah Anda yakin ingin melanjutkan packing list ini ke tahap <strong>Transit</strong>?
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                                <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Sales Order</div>
                                <div class="text-lg font-bold text-blue-900" id="transit-so-number">-</div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                                <p class="text-xs text-yellow-800">
                                    <strong>Note:</strong> Status akan berubah menjadi "In Transit"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                    <button onclick="closeTransitModal()"
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button onclick="processTransit()"
                        class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Ya, Lanjutkan Transit
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- SCRIPT DELIVERY MODAL --}}
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const API_CREATE_DO = '/admin/outbound/delivery-orders/store';

    async function openDeliveryOrderModal(event, packingListId) {
        event.stopPropagation();

        const modal = document.getElementById('delivery-order-modal');
        const loadingState = document.getElementById('loading-do-modal');
        const contentState = document.getElementById('content-do-modal');

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
            console.error('Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: 'Tidak dapat memuat detail packing list.',
                confirmButtonColor: '#dc2626',
                customClass: {
                    container: 'swal-above-modal'
                }
            });

            closeDeliveryOrderModal();
        }
    }

    function closeDeliveryOrderModal() {
        const modal = document.getElementById('delivery-order-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';

        const form = document.getElementById('deliveryOrderForm');
        if (form) form.reset();
    }

    async function submitDeliveryOrder() {
        const form = document.getElementById('deliveryOrderForm');
        const formData = new FormData(form);

        const data = {
            packing_id: formData.get('packing_list_id'),
            delivery_date: formData.get('delivery_date'),
            driver_name: formData.get('driver_name').trim(),
        };

        if (!data.delivery_date) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Tanggal pengiriman harus diisi.',
                customClass: {
                    container: 'swal-above-modal'
                }
            });
            return;
        }

        if (!data.driver_name) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Nama driver harus diisi.',
                customClass: {
                    container: 'swal-above-modal'
                }
            });
            document.getElementById('driver_name').focus();
            return;
        }

        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Pembuatan DO',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan membuat Delivery Order dengan detail:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Tanggal Kirim:</strong> ${data.delivery_date}</p>
                        <p><strong>Driver:</strong> ${data.driver_name}</p>
                    </div>
                    <p class="text-red-600 font-semibold mt-3">⚠️ Status packing list akan berubah menjadi "Shipped"</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Buat DO',
            cancelButtonText: 'Batal',
            width: '500px',
            customClass: {
                container: 'swal-above-modal'
            }
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
            },
            customClass: {
                container: 'swal-above-modal'
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

            await Swal.fire({
                icon: 'success',
                title: 'Delivery Order Berhasil Dibuat!',
                html: `
                    <div class="text-sm space-y-2">
                        <p>DO Number: <strong>${result.do_number || 'N/A'}</strong></p>
                        <p class="text-green-600">Status packing list telah diubah menjadi "Shipped"</p>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#16a34a',
                customClass: {
                    container: 'swal-above-modal'
                }
            });

            window.location.reload();

        } catch (error) {
            console.error('Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat DO',
                text: error.message || 'Terjadi kesalahan saat membuat delivery order.',
                confirmButtonColor: '#dc2626',
                customClass: {
                    container: 'swal-above-modal'
                }
            });
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryOrderModal = document.getElementById('delivery-order-modal');
        if (deliveryOrderModal) {
            deliveryOrderModal.addEventListener('click', function(e) {
                // Hanya close jika klik di overlay, bukan di modal content
                if (e.target === deliveryOrderModal) {
                    closeDeliveryOrderModal();
                }
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const doModal = document.getElementById('delivery-order-modal');
                if (!doModal.classList.contains('hidden')) closeDeliveryOrderModal();
            }
        })
    });
</script>

{{-- SCRIPT DETAIL MODAL --}}
<script>
    let currentPackingListId = null;

    function openDetailModal(packingListId) {
        const modal = document.getElementById('detail-modal');
        const loadingState = document.getElementById('loading-modal');
        const contentState = document.getElementById('content-modal');
        currentPackingListId = packingListId;

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
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat detail packing list.',
                    confirmButtonColor: '#dc2626'
                });
                closeDetailModal();
            });
    }

    function closeDetailModal() {
        const modal = document.getElementById('detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function timelineOutbound() {
        window.location.href = `/admin/outbound/packing-lists/detail/${currentPackingListId}`;
    }
    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = document.getElementById("detail-modal");
        if (detailModal) {
            detailModal.addEventListener('click', function(e) {
                // Hanya close jika klik di overlay, bukan di modal content
                if (e.target === detailModal) {
                    closeDetailModal();
                }
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const detailModal = document.getElementById('detail-modal');
                if (!detailModal.classList.contains('hidden')) closeDetailModal();
            }
        })
    });
</script>

{{-- SCRIPT TRANSIT MODAL --}}
<script>
    let currentTransitId = null;
    const API_TRANSIT = '/admin/outbound/packing-lists';

    function confirmTransit(e, packingListId, soNumber) {
        e.stopPropagation();
        currentTransitId = packingListId;
        document.getElementById('transit-so-number').textContent = soNumber;
        document.getElementById('transit-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeTransitModal() {
        document.getElementById('transit-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentTransitId = null;
    }

    async function processTransit() {
        Swal.fire({
            title: 'Memproses...',
            html: 'Sedang memproses packing list ke tahap transit',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch(`${API_TRANSIT}/${currentTransitId}/in-transit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal memproses transit');
            }

            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: `
                    <div class="text-sm space-y-2">
                        <p>${result.message || 'Packing list berhasil dilanjutkan ke tahap transit'}</p>
                        <p class="text-orange-600">Status telah diubah menjadi "In Transit"</p>
                    </div>
                `,
                confirmButtonColor: '#ea580c',
                confirmButtonText: 'OK'
            });

            window.location.reload();

        } catch (error) {
            console.error('Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses Transit',
                text: error.message || 'Terjadi kesalahan saat memproses transit.',
                confirmButtonColor: '#dc2626'
            });
        } finally {
            closeTransitModal();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('transit-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTransitModal()
            }
        })
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const transitModal = document.getElementById('transit-modal');
                if (!transitModal.classList.contains('hidden')) closeTransitModal();
            }
        })
    });
</script>
