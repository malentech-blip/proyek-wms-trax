<x-app-layout>
    <x-slot name="header">
        Timeline Outbound (Packing List)
    </x-slot>

    <x-outbound.tabs-outbound :packingId="$packingList->id" :status="$packingList->status" />
    <div class="mt-8 bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Detail Packing Lists</h3>
            <div class="flex flex-col gap-3 mt-2">
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Sales Order:</p>
                    <p class="font-medium">{{ $so['number'] }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Customer:</p>
                    <p class="font-medium">{{ $so['customer']['name'] }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Packed Date:</p>
                    <p class="font-medium">
                        {{ $packingList->packed_at }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Status:</p>
                    <p class="font-medium">{{ $packingList->status }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Barcode:</p>
                    <p class="font-medium">
                        {!! DNS1D::getBarcodeHTML($packingList->barcode, 'C128', 1.5, 40) !!}
                    </p>
                </div>
            </div>
            @if ($packingList->status == 'Packed')
                <div class="flex items-center gap-3 !mt-5">
                    <button id="openDeliveryOrderModalBtn"
                        class="flex w-max items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Buat Delivery Order
                    </button>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[100px]">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[200px]">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Barcode</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($items as $item)
                        <tr>
                            <td class="p-4 text-gray-700 font-medium">{{ $item->finished_good->item->item_code }}</td>
                            <td class="p-4 text-gray-500">
                                {{ $item->finished_good->item->item_name }}
                            </td>
                            <td class="p-4 text-gray-500">{{ $item->quantity }}</td>
                            <td class="p-4 text-gray-500">
                                {!! DNS1D::getBarcodeHTML($item->finished_good->production_item_label->barcode, 'C128', 1.5, 40) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL DELIVERY ORDER --}}
    <div id="deliveryOrderModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div id="deliveryOrderModalOverlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block max-sm:w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="mt-3 sm:mt-0 sm:ml-4 text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Buat Delivery Order
                            </h3>
                            <div class="mt-4">
                                <form id="deliveryOrderForm" class="space-y-4">

                                    <div>
                                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tanggal Pengiriman <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" id="delivery_date" name="delivery_date" required
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            min="{{ date('Y-m-d') }}">
                                        <p class="text-xs text-gray-500 mt-1">Tanggal pengiriman barang ke customer</p>
                                    </div>

                                    <div>
                                        <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Driver <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="driver_name" name="driver_name" required
                                            placeholder="Masukkan nama driver"
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </form>

                                {{-- Summary Info --}}
                                <div class="mt-4 p-3 bg-gray-50 rounded-md border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Informasi Pengiriman:</h4>
                                    <div class="space-y-1 text-xs text-gray-600">
                                        <div class="flex justify-between">
                                            <span>SO Number:</span>
                                            <span class="font-medium">{{ $so['number'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Customer:</span>
                                            <span class="font-medium">{{ $so['customer']['name'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Total Items:</span>
                                            <span class="font-medium">{{ $items->count() }} items</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" id="submitDeliveryOrderBtn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Buat Delivery Order
                    </button>
                    <button type="button" id="cancelDeliveryOrderBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const PACKING_ID = '{{ $packingList->id }}';
    const API_CREATE_DELIVERY_ORDER = '/admin/outbound/delivery-orders/store';

    // Modal Functions
    function openDeliveryOrderModal() {
        const modal = document.getElementById('deliveryOrderModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('delivery_date').value = today;

        setTimeout(() => {
            document.getElementById('delivery_date').focus();
        }, 100);
    }

    function closeDeliveryOrderModal() {
        const modal = document.getElementById('deliveryOrderModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('deliveryOrderForm').reset();
    }

    // Validate Form
    function validateDeliveryOrderForm() {
        const form = document.getElementById('deliveryOrderForm');
        const deliveryDate = document.getElementById('delivery_date').value;
        const driverName = document.getElementById('driver_name').value.trim();

        if (!deliveryDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Tanggal pengiriman harus diisi.'
            });
            return false;
        }

        if (!driverName) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Nama driver harus diisi.'
            });
            document.getElementById('driver_name').focus();
            return false;
        }
        return true;
    }

    async function submitDeliveryOrder() {
        if (!validateDeliveryOrderForm()) {
            return;
        }
        const packingId = '{{ $packingList->id }}';
        const formData = {
            packing_id: PACKING_ID,
            delivery_date: document.getElementById('delivery_date').value,
            driver_name: document.getElementById('driver_name').value.trim(),
            packing_id: packingId
        };

        // Konfirmasi sebelum submit
        const confirmResult = await Swal.fire({
            title: 'Konfirmasi Pembuatan DO',
            html: `
                <div class="text-left space-y-2 text-sm">
                    <p>Anda akan membuat Delivery Order dengan detail:</p>
                    <div class="bg-gray-50 p-3 rounded-md mt-3">
                        <p><strong>Tanggal Kirim:</strong> ${formData.delivery_date}</p>
                        <p><strong>Driver:</strong> ${formData.driver_name}</p>
                    </div>
                    <p class="text-gray-600 mt-3">Pastikan semua data sudah benar sebelum melanjutkan.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
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
            html: 'Sedang membuat Delivery Order, mohon tunggu...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        try {
            const response = await fetch(API_CREATE_DELIVERY_ORDER, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
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
                        <p>Delivery Order telah berhasil dibuat dengan detail:</p>
                        <div class="bg-green-50 p-3 rounded-md mt-2 text-left">
                            <p><strong>DO Number:</strong> ${result.do_number || 'Generating...'}</p>
                            <p><strong>Driver:</strong> ${formData.driver_name}</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563eb'
            });

            window.location.reload();

        } catch (error) {
            console.error('Error creating delivery order:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat Delivery Order',
                text: error.message || 'Terjadi kesalahan saat membuat delivery order. Silakan coba lagi.',
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Open Modal Button
        const openModalBtn = document.getElementById('openDeliveryOrderModalBtn');
        if (openModalBtn) {
            openModalBtn.addEventListener('click', openDeliveryOrderModal);
        }

        // Cancel Button
        document.getElementById('cancelDeliveryOrderBtn').addEventListener('click', closeDeliveryOrderModal);

        // Overlay Click
        document.getElementById('deliveryOrderModalOverlay').addEventListener('click', closeDeliveryOrderModal);

        // Submit Button
        document.getElementById('submitDeliveryOrderBtn').addEventListener('click', submitDeliveryOrder);

        // Close on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeliveryOrderModal();
            }
        });

        document.getElementById('deliveryOrderForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                submitDeliveryOrder();
            }
        });
    });
</script>
