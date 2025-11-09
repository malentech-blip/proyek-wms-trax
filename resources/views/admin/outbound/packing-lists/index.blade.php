<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Modal Overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }

    /* Modal positioning fix */
    #detail-modal {
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

    #detail-modal.hidden {
        display: none !important;
    }

    /* Modal content */
    #modal-content-container {
        max-width: 56rem;
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

    #modal-content-container {
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
                        <label for="search" class="text-sm font-medium text-gray-700">Tanggal Packing</label>
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
                        <select name="status" id="status" onchange="this.form.submit()"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Status</option>
                            <option value="Packed" {{ request('status') === 'Packed' ? 'selected' : '' }}>Packed
                            </option>
                            <option value="In Transit" {{ request('status') === 'In Transit' ? 'selected' : '' }}>In Transit
                            </option>
                            <option value="Ready to Ship" {{ request('status') === 'Ready to Ship' ? 'selected' : '' }}>
                                Ready to Ship</option>
                            <option value="Shipped" {{ request('status') === 'Shipped' ? 'selected' : '' }}>Shipped
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            Cari
                        </button>
                        @if (request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.outbound.packing-lists.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 shadow-sm">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-700">No</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Sales Order</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Customer Id</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Packed By</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Packed Date</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($packingLists as $pl)
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors"
                            onclick="openDetailModal({{ $pl->id }})">
                            <td class="p-4 text-gray-700 font-medium">
                                {{ $loop->iteration }}
                            </td>
                            <td class="p-4 text-gray-500">{{ $pl->sales_order->so_number ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->sales_order->customer_id ?? 'N/A' }}</td>
                            <td class="p-4 text-gray-500">{{ $pl->packed_by ?? 'System' }}</td>
                            <td class="p-4 text-gray-500">
                                {{ $pl->packed_at ? \Carbon\Carbon::parse($pl->packed_at)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="p-4">
                                @php
                                    $color = match ($pl->status) {
                                        'Shipped' => 'green',
                                        'Ready to Ship' => 'blue',
                                        default => 'yellow',
                                    };
                                @endphp
                                <span
                                    class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $pl->status }}
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
                                        <!-- Items will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center gap-3 bg-gray-50 px-6 py-4 rounded-b-xl justify-end">
                <button onclick="timelineOutbound()"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Lihat Timeline
                </button>
                <button onclick="closeDetailModal()"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div id="transit-modal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
                <!-- Header -->
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 rounded-t-xl">
                    <h3 class="text-xl font-bold text-white">Konfirmasi Transit</h3>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-700 mb-2">
                                Apakah Anda yakin ingin melanjutkan packing list ini ke tahap transit?
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                                <div class="text-xs text-blue-600 font-semibold uppercase mb-1">Sales Order</div>
                                <div class="text-lg font-bold text-blue-900" id="transit-so-number">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                    <button onclick="closeTransitModal()"
                        class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button onclick="processTransit()"
                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
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
                console.log('Response status:', response.status);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                document.getElementById('packing-list-number-title').textContent = data.pl_number || 'N/A';
                document.getElementById('so-number-info').textContent = data.so_number || 'N/A';
                document.getElementById('customer-name-info').textContent = data.customer_name || 'N/A';
                document.getElementById('packed-by-info').textContent = data.packed_by || 'System';
                document.getElementById('packed-at-info').textContent = data.packed_at || 'N/A';

                // Populate items
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
                            <svg id="barcode-${index}" class="h-10"></svg>
                          </td>
                          <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                              ${item.quantity || 0}
                            </span>
                          </td>
                        `;
                        tableBody.appendChild(row);

                        // 🔥 Generate barcode dynamically
                        JsBarcode(`#barcode-${index}`, item.label_code || item.qr_code || "N/A", {
                            format: "CODE128",
                            lineColor: "#000",
                            width: 2,
                            height: 40,
                            displayValue: false
                        });
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
            `
                loadingState.classList.add('hidden');
                contentState.classList.remove('hidden');
            });
    }

    function timelineOutbound() {
        window.location.href = `/admin/outbound/packing-lists/detail/${currentPackingListId}`;
    }

    function closeDetailModal() {
        const modal = document.getElementById('detail-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    // Close on overlay click
    document.getElementById('detail-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailModal();
        }
    });
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('detail-modal');
            if (!modal.classList.contains('hidden')) {
                closeDetailModal();
            }
        }
    });
</script>

<script>
    let currentTransitId = null;

    function confirmTransit(e, packingListId, soNumber) {
        e.stopPropagation()
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

    function processTransit() {
        Swal.fire({
            title: 'Memproses...',
            html: 'Sedang memproses packing list ke tahap transit',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim request ke backend
        fetch(`/admin/outbound/packing-lists/${currentTransitId}/transit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Network response was not ok');
                    });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Packing list berhasil dilanjutkan ke tahap transit',
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-5 py-2.5 rounded-lg font-medium shadow-sm'
                    }
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Gagal memproses transit. Silakan coba lagi.',
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-5 py-2.5 rounded-lg font-medium shadow-sm'
                    }
                });
            });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const transitModal = document.getElementById('transit-modal');
            if (!transitModal.classList.contains('hidden')) {
                closeTransitModal();
            }
        }
    });

    document.getElementById('transit-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTransitModal();
        }
    });
</script>
