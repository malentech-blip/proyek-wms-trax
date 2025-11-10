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
                    <p class="w-[200px] text-sm text-gray-600">Sales Order:</p>
                    <p class="font-medium">{{ $so['number'] }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Customer:</p>
                    <p class="font-medium">{{ $so['customer']['name'] }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Packed Date:</p>
                    <p class="font-medium">
                        {{ $packingList->packed_at }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Status:</p>
                    <p class="font-medium">{{ $packingList->status }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Barcode:</p>
                    <p class="font-medium">
                      {!! DNS1D::getBarcodeHTML($packingList->barcode, 'C128', 1.5, 40) !!}
                    </p>
                </div>
            </div>
            @if ($packingList->status == 'Packed')
                <div class="flex items-center gap-3 !mt-5">
                    <button
                        onclick="confirmTransit({{ $packingList->id }}, '{{ $packingList->sales_order->so_number ?? 'N/A' }}')"
                        class="flex w-max items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm"
                        title="Lanjut ke Transit">
                        <svg class="w-6 h-6 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6">
                            </path>
                        </svg>
                        Lanjut Transit
                    </button>
                    <button
                        class="flex w-max items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        Buat Delivery Order
                    </button>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Name</th>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentTransitId = null;

    function confirmTransit(packingListId, soNumber) {
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
                    window.location.href =
                        '{{ route('admin.outbound.transit-inventory.detail', $packingList->id) }}'
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
