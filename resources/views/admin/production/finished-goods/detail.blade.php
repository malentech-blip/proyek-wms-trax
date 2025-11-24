<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-app-layout>
    <x-slot name="header">
        Timeline Production (Finished Goods)
    </x-slot>


    <x-production.tabs-production :mrId="$mrId" :wip="$wipRecord" />
    
    {{-- HPP COST ANALYSIS CARD --}}
    @if($manufactureCost)
    <div class="bg-white rounded-xl shadow-sm mt-8 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">📊 Analisis Harga Pokok Produksi (HPP)</h3>
            @php
                $statusColor = 'gray';
                $statusIcon = '✓';
                $statusText = 'On Budget';
                
                if ($manufactureCost->variance_status === 'Over Budget') {
                    $statusColor = 'red';
                    $statusIcon = '⚠️';
                    $statusText = 'Over Budget';
                } elseif ($manufactureCost->variance_status === 'Under Budget') {
                    $statusColor = 'green';
                    $statusIcon = '✓';
                    $statusText = 'Under Budget';
                }
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                {{ $statusIcon }} {{ $statusText }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Raw Material Cost --}}
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-xs text-blue-600 font-medium mb-1">Biaya Bahan Baku</p>
                <p class="text-xl font-bold text-blue-900">Rp {{ number_format($manufactureCost->raw_material_cost, 0, ',', '.') }}</p>
            </div>

            {{-- Labor Cost --}}
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-xs text-green-600 font-medium mb-1">Biaya Tenaga Kerja</p>
                <p class="text-xl font-bold text-green-900">Rp {{ number_format($manufactureCost->labor_cost, 0, ',', '.') }}</p>
            </div>

            {{-- Overhead Cost --}}
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-xs text-purple-600 font-medium mb-1">Biaya Overhead</p>
                <p class="text-xl font-bold text-purple-900">Rp {{ number_format($manufactureCost->overhead_cost, 0, ',', '.') }}</p>
            </div>

            {{-- Total Cost --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-600 font-medium mb-1">Total Biaya Produksi</p>
                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($manufactureCost->total_cost, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
            {{-- HPP per Unit --}}
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">HPP Actual per Unit</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($manufactureCost->hpp_actual_per_unit, 0, ',', '.') }}</p>
            </div>

            {{-- Variance Nominal --}}
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Selisih Nominal</p>
                <p class="text-2xl font-bold {{ floatval($manufactureCost->variance_nominal) > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ floatval($manufactureCost->variance_nominal) > 0 ? '+' : '' }}Rp {{ number_format($manufactureCost->variance_nominal, 0, ',', '.') }}
                </p>
            </div>

            {{-- Variance Percentage --}}
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-1">Selisih Persentase</p>
                <p class="text-2xl font-bold {{ $manufactureCost->variance_percentage > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $manufactureCost->variance_percentage > 0 ? '+' : '' }}{{ number_format($manufactureCost->variance_percentage, 2) }}%
                </p>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if($manufactureCost->variance_status === 'Over Budget')
        <div class="mt-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">⚠️ Peringatan: Biaya Produksi Melebihi Target</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>HPP actual lebih tinggi dari standar. Pertimbangkan untuk:</p>
                        <ul class="list-disc list-inside mt-1 ml-2">
                            <li>Review efisiensi penggunaan bahan baku</li>
                            <li>Evaluasi produktivitas tenaga kerja</li>
                            <li>Optimasi biaya overhead pabrik</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @elseif($manufactureCost->variance_status === 'Under Budget')
        <div class="mt-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">✓ Efisiensi Produksi Tercapai</h3>
                    <p class="mt-2 text-sm text-green-700">
                        Biaya produksi lebih rendah dari standar. Pertahankan efisiensi ini untuk batch produksi selanjutnya.
                    </p>
                </div>
            </div>
        </div>
        @else
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">✓ Biaya Produksi Sesuai Target</h3>
                    <p class="mt-2 text-sm text-blue-700">
                        HPP actual sesuai dengan standar yang ditetapkan. Produksi berjalan normal.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm mt-8 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[120px]">Item Code</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[250px]">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[100px]">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[150px]">Batch</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[100px]">Status QC</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[150px]">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[100px]">Rack</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[100px]">Pallet</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[100px]">Status</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[150px]">Barcode</th>
                        <th class="p-4 text-left font-semibold text-gray-600 whitespace-nowrap min-w-[180px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($finishedGood)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 text-gray-700 whitespace-nowrap font-medium">{{ $finishedGood->item->item_code ?? '-' }}</td>
                            <td class="p-4 text-gray-700">{{ $finishedGood->item->item_name ?? '-' }}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">{{ $finishedGood->quantity ?? '-' }}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">{{ $finishedGood->production_item_label->batch_no ?? '-' }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($finishedGood->qc_status ?? '') === 'OK' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $finishedGood->qc_status ?? '-' }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">{{ $finishedGood->production_item_label->location->name ?? '-' }}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">{{ $finishedGood->production_item_label->rack->code ?? '-' }}</td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">{{ $finishedGood->production_item_label->pallet->code ?? '-' }}</td>
                            <td class="p-4 whitespace-nowrap">
                                @if($finishedGood->status)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $finishedGood->status === 'Stored' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $finishedGood->status }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">
                                @if(isset($finishedGood->production_item_label->barcode))
                                    {!! DNS1D::getBarcodeHTML($finishedGood->production_item_label->barcode, 'C128', 1.5, 40) !!}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-4 text-gray-700 whitespace-nowrap">
                                @if (!$finishedGood->stored_at)
                                    <button type="button" onclick="showStoreInventoryModal({{ $finishedGood->id }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                        Store to Inventory
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-gray-600 bg-gray-100">
                                        Already Stored
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="11" class="text-center p-12 text-gray-500">
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

    {{-- STORE TO INVENTORY MODAL --}}
    <div id="storeInventoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="store-modal-title" role="dialog" aria-modal="true">
        <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div onclick="hideStoreInventoryModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
            </div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <form id="storeInventoryForm">
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
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="store-modal-title">
                                    Pindahkan ke Inventory
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-700 mb-2">
                                            Item: <strong id="modalItemName"></strong><br>
                                            Batch: <strong id="modalBatchNo"></strong><br>
                                            Quantity: <strong id="modalQuantity"></strong>
                                        </p>
                                    </div>

                                    <div>
                                        <label for="modal_location_id" class="block text-sm font-medium text-gray-700">Location <span class="text-red-500">*</span></label>
                                        <select required name="location_id" id="modal_location_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-orange-500 focus:border-orange-500">
                                            <option value="">Choose Location</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="modal_rack_id" class="block text-sm font-medium text-gray-700">Rack <span class="text-red-500">*</span></label>
                                        <select required name="rack_id" id="modal_rack_id" disabled
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-orange-500 focus:border-orange-500 disabled:bg-gray-100">
                                            <option value="">Choose Rack</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="modal_pallet_id" class="block text-sm font-medium text-gray-700">Pallet <span class="text-red-500">*</span></label>
                                        <select required name="pallet_id" id="modal_pallet_id" disabled
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-orange-500 focus:border-orange-500 disabled:bg-gray-100">
                                            <option value="">Choose Pallet</option>
                                        </select>
                                    </div>

                                    <p class="text-sm text-red-600 font-semibold">
                                        * Pastikan data Lokasi, Rack, dan Pallet sudah benar.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ya, Pindahkan ke Inventory
                        </button>
                        <button type="button" onclick="hideStoreInventoryModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
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
                        window.location.reload();
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
    let selectedFinishedGoodId = null;

    window.showStoreInventoryModal = function(finishedGoodId) {
        if (!finishedGoodId) {
            Swal.fire('Error', 'ID Finished Good tidak ditemukan.', 'error');
            return;
        }
        
        selectedFinishedGoodId = finishedGoodId;
        
        // Update informasi di modal
        @if($finishedGood)
            document.getElementById('modalItemName').textContent = '{{ $finishedGood->item->item_name ?? "Item" }}';
            document.getElementById('modalBatchNo').textContent = '{{ $finishedGood->production_item_label->batch_no ?? "-" }}';
            document.getElementById('modalQuantity').textContent = '{{ $finishedGood->quantity ?? 0 }}';
        @endif
        
        // Reset form
        document.getElementById('storeInventoryForm').reset();
        document.getElementById('modal_rack_id').disabled = true;
        document.getElementById('modal_pallet_id').disabled = true;
        document.getElementById('modal_rack_id').innerHTML = '<option value="">Choose Rack</option>';
        document.getElementById('modal_pallet_id').innerHTML = '<option value="">Choose Pallet</option>';
        
        document.getElementById('storeInventoryModal').style.display = 'block';
    }
    
    window.hideStoreInventoryModal = function() {
        document.getElementById('storeInventoryModal').style.display = 'none';
        selectedFinishedGoodId = null;
    }
    
    // Handle form submit
    document.addEventListener('DOMContentLoaded', function() {
        const storeInventoryForm = document.getElementById('storeInventoryForm');
        
        if (storeInventoryForm) {
            storeInventoryForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                
                if (!selectedFinishedGoodId) {
                    Swal.fire('Error', 'ID Finished Good tidak ditemukan.', 'error');
                    return;
                }

                const formData = new FormData(event.target);
                const locationId = formData.get('location_id');
                const rackId = formData.get('rack_id');
                const palletId = formData.get('pallet_id');

                if (!locationId || !rackId || !palletId) {
                    Swal.fire('Error', 'Harap pilih Location, Rack, dan Pallet.', 'error');
                    return;
                }

                hideStoreInventoryModal();

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
                    const url = `/admin/production/finished-goods/${selectedFinishedGoodId}/store-to-inventory`;

                    const payload = {
                        fg_id: {{ $finishedGood->id ?? 0 }},
                        item_id: '{{ $finishedGood->item_id ?? "" }}',
                        quantity: {{ $finishedGood->quantity ?? 0 }},
                        location_id: locationId,
                        rack_id: rackId,
                        pallet_id: palletId
                    };

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
            });
        }

        // Location, Rack, Pallet cascade
        const modalLocationSelect = document.getElementById('modal_location_id');
        const modalRackSelect = document.getElementById('modal_rack_id');
        const modalPalletSelect = document.getElementById('modal_pallet_id');

        if (modalLocationSelect) {
            modalLocationSelect.addEventListener('change', function() {
                const locationId = this.value;

                // Reset rack dan pallet
                modalRackSelect.innerHTML = '<option value="">Choose Rack</option>';
                modalPalletSelect.innerHTML = '<option value="">Choose Pallet</option>';
                modalRackSelect.disabled = true;
                modalPalletSelect.disabled = true;

                if (locationId) {
                    // Fetch racks berdasarkan location_id
                    fetch(`/admin/production/finished-goods/racks/by-location/${locationId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(rack => {
                                const option = document.createElement('option');
                                option.value = rack.id;
                                option.textContent = rack.code;
                                modalRackSelect.appendChild(option);
                            });
                            modalRackSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching racks:', error);
                            Swal.fire('Error', 'Gagal memuat data rack', 'error');
                        });
                }
            });
        }

        if (modalRackSelect) {
            modalRackSelect.addEventListener('change', function() {
                const rackId = this.value;

                // Reset pallet
                modalPalletSelect.innerHTML = '<option value="">Choose Pallet</option>';
                modalPalletSelect.disabled = true;

                if (rackId) {
                    // Fetch pallets berdasarkan rack_id
                    fetch(`/admin/production/finished-goods/pallets/by-rack/${rackId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(pallet => {
                                const option = document.createElement('option');
                                option.value = pallet.id;
                                option.textContent = pallet.code;
                                modalPalletSelect.appendChild(option);
                            });
                            modalPalletSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching pallets:', error);
                            Swal.fire('Error', 'Gagal memuat data pallet', 'error');
                        });
                }
            });
        }
    });
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
