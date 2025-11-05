<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-app-layout>
    <x-slot name="header">
        Timeline Production (Finished Goods)
    </x-slot>

    
    <x-production.tabs-production :mrId="$mrId" :wip="$wipRecord" />
    <div class="bg-white rounded-xl shadow-sm mt-8">
        <div class="p-6 border-b flex items-center gap-3">
            @if ($wipRecord && !$finishedGood)
                <button onclick="showAddFinishedModal()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Tambah finished goods
                </button>
            @endif
            @if ($wipRecord && $finishedGood && !$finishedGood->stored_at)
                <button type="button" id="btnStoreToInventory" onclick="showStoreInventoryModal()"
                    class="w-max border-none rounded py-2 px-4 bg-orange-500 text-white hover:bg-orange-600">
                    Store to Inventory
                </button>
            @endif
            @if ($finishedGood)
              <button type="button" onclick="window.open('{{ route('admin.production.finished-goods.print-label', $finishedGood->production_item_label->id) }}', '_blank')"
                  class="w-max border-none rounded py-2 px-4 bg-blue-500 text-white hover:bg-blue-600">
                  Cetak Production Label
              </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Batch</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Status QC</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Rack</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Pallet</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                        <th class="p-4 text-left font-semibold text-gray-600">QR Code</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($finishedGood)
                        <tr>
                            <td class="p-4 text-gray-500">{{ $finishedGood->item->item_code }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->item->item_name }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->quantity }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->batch_no }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->qc_status }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->location->name }}
                            </td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->rack->code }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->production_item_label->pallet->code }}</td>
                            <td class="p-4 text-gray-500">{{ $finishedGood->status }}</td>
                            <td class="p-4 text-gray-500">
                                {!! QrCode::size(70)->generate($finishedGood->production_item_label->qr_code) !!}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center p-12 text-gray-500">
                                @if (!$wipRecord)
                                    Tahap WIP belum dilalui/belum selesai
                                @else
                                    Belum ada finished goods.
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD FINISHED GOODS MODAL --}}
    <div id="adaFinishedModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div onclick="hideAddFinishedModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true">
            </div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                {{-- Tambahkan ID pada Form --}}
                <form id="finishedGoodForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Tambah Finished Goods
                                </h3>
                                <div class="mt-2 space-y-4">
                                    {{-- INPUT FINISHED GOODS QTY --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Work In
                                            Progress</label>
                                        <input required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm bg-gray-100 focus:ring-0 focus:border-gray-300 focus:outline-none"
                                            value="{{ $wipRecord->wip_no }}" readonly>
                                        <input type="hidden" name="wip_id" value="{{ $wipRecord->id }}">

                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quantity Item</label>
                                        <input required name="quantity"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm bg-gray-100 focus:ring-0 focus:border-gray-300 focus:outline-none"
                                            value="{{ $wipRecord->produced_qty }}" readonly>

                                    </div>
                                    <div>
                                        <label for="item_id"
                                            class="block text-sm font-medium text-gray-700">Item</label>
                                        <select required name="item_id" id="item_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Choose Item</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="location_id"
                                            class="block text-sm font-medium text-gray-700">Location</label>
                                        <select required name="location_id" id="location_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Choose Location</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="rack_id"
                                            class="block text-sm font-medium text-gray-700">Rack</label>
                                        <select required name="rack_id" id="rack_id" disabled
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Choose Rack</option>
                                        </select>

                                    </div>
                                    <div>
                                        <label for="pallet_id"
                                            class="block text-sm font-medium text-gray-700">Pallet</label>
                                        <select required name="pallet_id" id="pallet_id" disabled
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Choose Pallet</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="batch_no" class="block text-sm font-medium text-gray-700">Batch
                                            Production</label>
                                        <input required type="text" name="batch_no" id="batch_no"
                                            placeholder="Type batch number of production"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Submit and Save
                        </button>
                        <button type="button" onclick="hideAddFinishedModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- STORE TO INVENTORY MODAL --}}
    <div id="storeInventoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="store-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div onclick="hideStoreInventoryModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
            </div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">

                {{-- Form tidak diperlukan, kita pakai SweetAlert konfirmasi --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            {{-- Icon --}}
                            <svg class="h-6 w-6 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8zm0 0l2.5-4h7l2.5 4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="store-modal-title">
                                Konfirmasi Penyimpanan ke Inventory
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Anda akan memindahkan Finished Good
                                    **{{ $finishedGood->item->item_name ?? 'Item' }}**
                                    (Batch: **{{ $finishedGood->production_item_label->batch_no ?? '-' }}**)
                                    sejumlah **{{ $finishedGood->quantity ?? 0 }}** unit ke Inventory.
                                </p>
                                <p class="text-sm text-red-600 mt-2 font-semibold">
                                    Pastikan data Lokasi, Rack, dan Pallet sudah benar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    {{-- Tombol YES akan memanggil fungsi JS --}}
                    <button type="button" onclick="handleStoreToInventory({{ $finishedGood->id ?? 'null' }})"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Pindahkan ke Inventory
                    </button>
                    <button type="button" onclick="hideStoreInventoryModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



<script>
    window.showAddFinishedModal = function() {
        const modal = document.getElementById('adaFinishedModal');

        if (modal) {
            modal.style.display = 'block';
        } else {
            console.error('Elemen modal atau form tidak ditemukan.');
        }
    }
    window.hideAddFinishedModal = function() {
        const modal = document.getElementById('adaFinishedModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    document.addEventListener("DOMContentLoaded", function() {
        const finishedGoodForm = document.getElementById('finishedGoodForm');

        if (finishedGoodForm) {
            finishedGoodForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const form = event.target;
                const formData = new FormData(form);
                const url = '/admin/production/finished-goods';

                const payload = Object.fromEntries(formData.entries());

                // 1. SWEETALERT KONFIRMASI (Diperbaiki)
                const confirmation = await Swal.fire({
                    title: 'Konfirmasi Simpan?',
                    html: `Anda akan mencatat Finished Goods dengan:<br>
                           Batch No: <strong>${payload.batch_no}</strong><br>
                           Kuantitas: <strong>${payload.quantity}</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                });

                if (!confirmation.isConfirmed) {
                    return;
                }

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang menyimpan data Finished Goods.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const csrfToken = "{{ csrf_token() }}";
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        let errorMessage = errorData.message || 'Gagal menyimpan finished goods.';
                        throw new Error(errorMessage);
                    }

                    const data = await response.json();
                    Swal.fire({
                        title: "Success!",
                        text: data?.message,
                        icon: "success"
                    }).then(() => {
                        hideAddFinishedModal();
                        // window.location.reload();
                    })
                } catch (error) {
                    Swal.fire(
                        'Gagal!',
                        error.message,
                        'error'
                    );
                }
            });
        }
    });
</script>

<script>
    window.showStoreInventoryModal = function() {
        document.getElementById('storeInventoryModal').style.display = 'block';
    }
    window.hideStoreInventoryModal = function() {
        document.getElementById('storeInventoryModal').style.display = 'none';
    }
    window.handleStoreToInventory = async function(finishedGoodId) {
        if (!finishedGoodId) {
            Swal.fire('Error', 'ID Finished Good tidak ditemukan.', 'error');
            return;
        }

        hideStoreInventoryModal(); // Sembunyikan modal konfirmasi biasa

        // Tampilkan SweetAlert Loading
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang memindahkan barang ke inventory.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const csrfToken = "{{ csrf_token() }}";
            const url = `/admin/production/finished-goods/${finishedGoodId}/store-to-inventory`;

            // Menggunakan method POST atau PUT/PATCH tergantung rute API Anda
            const response = await fetch(url, {
                method: 'POST', // Ganti ke PUT/PATCH jika Anda menggunakan routing resource update
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menyimpan ke inventory.');
            }

            const data = await response.json();
            Swal.fire({
                title: "Berhasil!",
                text: data?.message,
                icon: "success"
            }).then(() => {
                window.location.reload();
            })

        } catch (error) {
            console.error('Store to Inventory Error:', error);
            Swal.fire(
                'Gagal!',
                error.message,
                'error'
            );
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locationSelect = document.getElementById('location_id');
        const rackSelect = document.getElementById('rack_id');
        const palletSelect = document.getElementById('pallet_id');

        // Event listener untuk Location
        locationSelect.addEventListener('change', function() {
            const locationId = this.value;

            // Reset rack dan pallet
            rackSelect.innerHTML = '<option value="">Choose Rack</option>';
            palletSelect.innerHTML = '<option value="">Choose Pallet</option>';
            rackSelect.disabled = true;
            palletSelect.disabled = true;

            if (locationId) {
                // Fetch racks berdasarkan location_id
                fetch(`/admin/production/finished-goods/racks/by-location/${locationId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(rack => {
                            const option = document.createElement('option');
                            option.value = rack.id;
                            option.textContent = rack.code;
                            rackSelect.appendChild(option);
                        });
                        rackSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching racks:', error);
                        alert('Gagal memuat data rack');
                    });
            }
        });

        // Event listener untuk Rack
        rackSelect.addEventListener('change', function() {
            const rackId = this.value;

            // Reset pallet
            palletSelect.innerHTML = '<option value="">Choose Pallet</option>';
            palletSelect.disabled = true;

            if (rackId) {
                // Fetch pallets berdasarkan rack_id
                fetch(`/admin/production/finished-goods/pallets/by-rack/${rackId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(pallet => {
                            const option = document.createElement('option');
                            option.value = pallet.id;
                            option.textContent = pallet.code;
                            palletSelect.appendChild(option);
                        });
                        palletSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching pallets:', error);
                        alert('Gagal memuat data pallet');
                    });
            }
        });
    });
</script>
