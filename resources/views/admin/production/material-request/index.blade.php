<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<x-app-layout>
    <x-slot name="header">
        Create Material Request (Production)
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Permintaan Bahan</h3>
            <form class="flex flex-col gap-3 sm:border border-gray-200 rounded-md sm:p-3">
                <div class="flex max-sm:flex-col max-sm:items-start items-center gap-2">
                    <label for="wo_no" class="w-[200px]">Work Order:</label>
                    <select name="wo_no" id="wo_no" class="border border-gray-200 rounded text-sm min-w-[300px]">
                        <option value="">Pilih Work Order</option>
                        @foreach ($workOrders as $wo)
                            <option value="{{ $wo['number'] }}" {{ request('wo_no') == $wo['number'] ? 'selected' : '' }}>
                                {{ $wo['number'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex max-sm:flex-col max-sm:items-start items-center gap-2">
                    <label for="requested_by" class="w-[200px]">Requested By:</label>
                    <input type="text" name="requested_by" id="requested_by"
                        class="border border-gray-200 rounded text-sm min-w-[300px]">
                </div>
                <div class="flex max-sm:flex-col max-sm:items-start items-center gap-2">
                    <label for="request_date" class="w-[200px]">Requested Date:</label>
                    <input type="date" name="request_date" id="request_date" min="{{ date('Y-m-d') }}"
                        class="border border-gray-200 rounded text-sm min-w-[300px]">
                </div>
                <div class="flex max-sm:flex-col max-sm:items-start items-center gap-2">
                    <label for="picked_by" class="w-[200px]">Picked By:</label>
                    <input type="text" name="picked_by" id="picked_by"
                        class="border border-gray-200 rounded text-sm min-w-[300px]" placeholder="Masukkan nama...">
                </div>
                <div class="mt-5 flex gap-3">
                    <button type="button" id="btnCreateMR" onclick="showConfirmMRModal()" disabled
                        class="w-max border-none bg-gray-400 text-white rounded py-2 px-4 cursor-not-allowed transition-colors">
                        Create MR
                    </button>
                </div>
            </form>
        </div>
        @if (request('wo_no') && count($items) > 0)
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Items dari Work Order</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                            <tr>
                                <th class="p-4 text-left font-semibold text-gray-700">No</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Item No</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Item Name</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Quantity</th>
                                <th class="p-4 text-left font-semibold text-gray-700">Picked By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" id="itemsTableBody">
                            @foreach ($items as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="p-4 text-gray-700 font-medium">{{ $index + 1 }}</td>
                                    <td class="p-4 text-gray-700">{{ $item['item']['no'] ?? '-' }}</td>
                                    <td class="p-4 text-gray-700">{{ $item['item']['name'] ?? '-' }}</td>
                                    <td class="p-4 text-gray-700">{{ ($item['quantity'] * $fgQuantity) ?? 0 }}</td>
                                    <td class="p-4 text-gray-700 picked-by-cell">-</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL CONFIRMATION --}}
    <div id="confirmMRModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="hideConfirmMRModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Konfirmasi Pembuatan MR & Picking List
                            </h3>
                            <div class="mt-2 space-y-4">
                                <p class="text-sm text-gray-500">
                                    Anda akan membuat Material Request (MR) dan Picking List berdasarkan data berikut:
                                </p>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                    <li>Work Order: <span id="confirmSoNumber" class="font-semibold"></span></li>
                                    <li>Requested By: <span id="confirmRequestedBy" class="font-semibold"></span></li>
                                    <li>Request Date: <span id="confirmRequestDate" class="font-semibold"></span></li>
                                    <li>Item Request: <span id="confirmItemCount"
                                            class="font-semibold text-blue-600"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="processCreateMR()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Buat MR & Picking List
                    </button>
                    <button type="button" onclick="hideConfirmMRModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function checkFormValidity() {
        const woNo = document.getElementById('wo_no').value;
        const requestedBy = document.getElementById('requested_by').value.trim();
        const requestDate = document.getElementById('request_date').value;
        const pickedBy = document.getElementById('picked_by').value.trim();
        const createMRBtn = document.getElementById('btnCreateMR');

        const isValid = woNo.length > 0 &&
            requestedBy.length > 0 &&
            requestDate.length > 0 &&
            pickedBy.length > 0;

        if (createMRBtn) {
            createMRBtn.disabled = !isValid;
            createMRBtn.classList.remove('bg-blue-500', 'bg-gray-400', 'hover:bg-blue-600', 'cursor-not-allowed');

            if (isValid) {
                createMRBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
            } else {
                createMRBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
        }
    }

    function updatePickedByInTable(pickedByValue) {
        const pickedByCells = document.querySelectorAll('.picked-by-cell');
        pickedByCells.forEach(cell => {
            cell.textContent = pickedByValue.trim() || '-';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const woSelect = document.getElementById('wo_no');
        const requestedByInput = document.getElementById('requested_by');
        const requestDateInput = document.getElementById('request_date');
        const pickedByInput = document.getElementById('picked_by');

        woSelect.addEventListener('change', function() {
            const selectedWoNo = this.value;
            
            // Reset form fields when WO changes
            requestedByInput.value = '';
            requestDateInput.value = '';
            pickedByInput.value = '';
            
            if (selectedWoNo) {
                const baseUrl = window.location.pathname;
                const newQuery = '?wo_no=' + selectedWoNo;
                window.location.href = baseUrl + newQuery;
            } else {
                window.location.href = window.location.pathname;
            }
        });

        requestedByInput.addEventListener('input', checkFormValidity);
        requestDateInput.addEventListener('change', checkFormValidity);
        pickedByInput.addEventListener('input', function() {
            checkFormValidity();
            updatePickedByInTable(this.value);
        });

        // Initial check on page load
        checkFormValidity();
        
        // Update picked by table on page load if value exists
        if (pickedByInput.value) {
            updatePickedByInTable(pickedByInput.value);
        }
    });

    window.showConfirmMRModal = function() {
        const woNo = document.getElementById('wo_no').value;
        
        if (!woNo) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Harap pilih Work Order terlebih dahulu.'
            });
            return;
        }

        const modal = document.getElementById('confirmMRModal');
        const woNumber = woNo;
        const requestedBy = document.getElementById('requested_by').value;
        const requestDate = document.getElementById('request_date').value;

        document.getElementById('confirmSoNumber').textContent = woNumber;
        document.getElementById('confirmRequestedBy').textContent = requestedBy;
        document.getElementById('confirmRequestDate').textContent = requestDate;
        document.getElementById('confirmItemCount').textContent = `{{ count($items ?? []) }} item`;

        if (modal) {
            modal.style.display = 'block';
        }
    }

    window.hideConfirmMRModal = function() {
        const modal = document.getElementById('confirmMRModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    window.processCreateMR = async function() {
        hideConfirmMRModal();

        const woNo = document.getElementById('wo_no').value;
        const requestedBy = document.getElementById('requested_by').value;
        const requestDate = document.getElementById('request_date').value;
        const pickedBy = document.getElementById('picked_by').value;

        const payload = {
            _token: '{{ csrf_token() }}',
            wo_no: woNo,
            requested_by: requestedBy,
            request_date: requestDate,
            picked_by: pickedBy
        };

        Swal.fire({
            title: 'Membuat MR & Picking List...',
            text: 'Sedang memproses dokumen di server.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await fetch('/admin/production/material-request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                const errorMessage = result.message || 'Gagal membuat MR/PL. Cek log server.';
                Swal.fire('Gagal!', errorMessage, 'error');
                return;
            }

            Swal.fire('Berhasil!', result.message || 'MR dan Picking List berhasil dibuat.', 'success')
                .then(() => {
                    const mrId = result?.mr_id;
                    const url = `{{ route('admin.production.material-request.detail', ':mr_id') }}`
                        .replace(':mr_id', mrId);
                    window.location = url;
                });

        } catch (error) {
            console.error('Final Creation Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan jaringan atau sistem.', 'error');
        }
    }
</script>
