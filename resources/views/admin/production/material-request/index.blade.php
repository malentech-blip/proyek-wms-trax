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
                    <label for="so_id" class="w-[200px]">Sales Order:</label>
                    <select name="so_id" id="so_id" class="border border-gray-200 rounded text-sm min-w-[300px]">
                        <option value="">Pilih Sales Order</option>
                        @foreach ($salesOrders as $so)
                            <option value="{{ $so->id }}" {{ request('so_id') == $so->id ? 'selected' : '' }}>
                                {{ $so->so_number }}</option>
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
                <div class="mt-5 flex gap-3">
                    <button onclick="showRequestItemModal()" id="btnRequestItem" type="button"
                        class="w-max border-none bg-blue-500 text-white rounded py-2 px-4 hover:bg-blue-600 transition-colors">
                        Request Item
                    </button>
                    <button type="button" id="btnCreateMR" onclick="showConfirmMRModal()"
                        class="w-max border-none bg-blue-500 text-white rounded py-2 px-4 hover:bg-blue-600 transition-colors">
                        Create MR
                    </button>
                </div>
            </form>
        </div>
        @if (request('so_id'))
            @php
                $items = $selectedSalesOrderDetail['detailItem'] ?? [];
            @endphp
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Detail Items</h3>
                <div class="mt-4">
                    @foreach ($items as $item)
                        <div class="grid grid-cols-4 gap-5 mb-4">
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-500 text-sm">Item Name</p>
                                <p class="font-medium">{{ $item['detailName'] }}</p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-500 text-sm">Item Quantity</p>
                                <p class="font-medium">{{ $item['quantity'] }}</p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-500 text-sm">Item Price</p>
                                <p class="font-medium">{{ formatRupiah($item['unitPrice']) }}</p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-500 text-sm">Total Price</p>
                                <p class="font-medium">{{ formatRupiah($item['totalPrice']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-700">No</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Item Name</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[150px]">Quantity Ready</th>
                        <th class="p-4 text-left font-semibold text-gray-700 max-sm:min-w-[120px]">Picked By</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Location</th>
                        <th class="p-4 text-left font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody id="requestedItemsTable" class="divide-y">
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL ITEM REQUEST --}}
    <div id="requestItemModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div
            class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="hideRequestItemModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="materialRequestForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 sm:mt-0 sm:ml-4 text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Buat Request Item
                                </h3>
                                <div class="mt-2 space-y-4">
                                    <p class="text-sm text-gray-500">
                                        Membuat request item berdasarkan detail item pada Sales Order **<span
                                            id="soNumberDisplay" class="font-semibold text-blue-600"></span>**.
                                        Pastikan detail permintaan sudah benar.
                                    </p>
                                    <div>
                                        <label for="item_id"
                                            class="block text-sm font-medium text-gray-700">Item:</label>
                                        <select name="item_id" id="item_id" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm bg-gray-100">
                                            <option value="">Pilih item request</option>
                                            @foreach ($rawItems as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="quantity"
                                            class="block text-sm font-medium text-gray-700">Quantity:</label>
                                        <input type="number" min="1" name="quantity" id="quantity" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm bg-gray-100">
                                    </div>
                                    <div>
                                        <label for="picked_by" class="block text-sm font-medium text-gray-700">Picked
                                            By:</label>
                                        <input type="text" name="picked_by" id="picked_by" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm bg-gray-100">
                                    </div>
                                    <input type="hidden" name="so_id" id="mr_so_id_hidden">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Proses Material Request
                        </button>
                        <button type="button" onclick="hideRequestItemModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                                    <li>Sales Order: <span id="confirmSoNumber" class="font-semibold"></span></li>
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

{{-- SCRIPT FOR LS --}}
<script>
    const STORAGE_KEY = 'temp_material_requests';
    function getStoredItems() {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }
    function setStoredItems(items) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        checkFormValidity();
    }
</script>

<script>
    function checkFormValidity() {
        const soId = document.getElementById('so_id').value;
        const requestedBy = document.getElementById('requested_by').value.trim();
        const requestDate = document.getElementById('request_date').value;
        const createMRBtn = document.getElementById('btnCreateMR');
        const items = getStoredItems();

        const hasRequestedItems = items.some(item => item.so_id === soId);

        const isValid = soId.length > 0 &&
            requestedBy.length > 0 &&
            requestDate.length > 0 &&
            hasRequestedItems;

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

    window.updateQuantity = function(index, newQuantity) {
        const items = getStoredItems();
        const soId = document.getElementById('so_id').value;
        const filteredItems = items.filter(item => item.so_id === soId);

        if (filteredItems[index]) {
            const actualIndex = items.indexOf(filteredItems[index]);
            items[actualIndex].quantity = parseInt(newQuantity);
            setStoredItems(items);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Quantity berhasil diperbarui',
                timer: 1500,
                showConfirmButton: false
            });
        }
    }

    // Delete item with confirmation
    window.deleteItem = async function(index) {
        const result = await Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus item ini dari daftar request?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            const items = getStoredItems();
            const soId = document.getElementById('so_id').value;
            const filteredItems = items.filter(item => item.so_id === soId);

            if (filteredItems[index]) {
                const actualIndex = items.indexOf(filteredItems[index]);
                const deletedItem = items[actualIndex];

                items.splice(actualIndex, 1);
                setStoredItems(items);
                renderTable();

                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus!',
                    text: `Item "${deletedItem.item_name}" berhasil dihapus`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    }

    function renderTable() {
        const items = getStoredItems();
        const tbody = document.getElementById('requestedItemsTable');
        const soId = document.getElementById('so_id').value;
        if (!tbody) return;

        tbody.innerHTML = '';
        checkFormValidity();

        if (items.length === 0 || !items.some(item => item.so_id === soId)) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center p-12">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-lg font-medium">Belum ada item yang di-request</p>
                            <p class="text-sm mt-1">Silakan klik "Request Item" untuk menambahkan</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        items.filter(item => item.so_id === soId).forEach((item, index) => {
            const row = tbody.insertRow();
            row.className = 'hover:bg-gray-50 transition-colors';
            row.innerHTML = `
                <td class="p-4 text-gray-700 font-medium">${index + 1}</td>
                <td class="p-4 text-gray-700">${item.item_name}</td>
                <td class="p-4">
                    <input type="number" 
                           min="1" 
                           value="${item.quantity}" 
                           onchange="updateQuantity(${index}, this.value)"
                           class="w-20 border border-gray-300 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </td>
                <td class="p-4 text-gray-700">${item.quantity_ready}</td>
                <td class="p-4 text-gray-700">${item.picked_by}</td>
                <td class="p-4 text-gray-700">${item.location}</td>
                <td class="p-4">
                    <button type="button" 
                            onclick="deleteItem(${index})" 
                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                </td>
            `;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const soSelect = document.getElementById('so_id');
        const requestedByInput = document.getElementById('requested_by');
        const requestDateInput = document.getElementById('request_date');

        soSelect.addEventListener('change', function() {
            const selectedSoId = this.value;
            if (selectedSoId) {
                const baseUrl = window.location.pathname;
                const newQuery = '?so_id=' + selectedSoId;
                window.location.href = baseUrl + newQuery;
            } else {
                window.location.href = window.location.pathname;
            }
            checkFormValidity();
        });

        requestedByInput.addEventListener('input', checkFormValidity);
        requestDateInput.addEventListener('change', checkFormValidity);

        renderTable();

        window.showRequestItemModal = function() {
            const modal = document.getElementById('requestItemModal');
            const soId = document.getElementById('so_id').value;
            const soNumber = document.getElementById('so_id').options[document.getElementById('so_id')
                .selectedIndex].text.trim();

            if (!soId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Harap pilih Sales Order terlebih dahulu.'
                });
                return;
            }

            document.getElementById('soNumberDisplay').textContent = soNumber;
            document.getElementById('mr_so_id_hidden').value = soId;
            if (modal) {
                modal.style.display = 'block';
            }
        }

        window.hideRequestItemModal = function() {
            const modal = document.getElementById('requestItemModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        const mrForm = document.getElementById('materialRequestForm');
        const csrfToken = "{{ csrf_token() }}";
        if (mrForm) {
            mrForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mengambil data item dari inventori.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(
                        '/admin/production/material-request/add-temp-item', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        const errorMessage = result.message ||
                            'Gagal menambahkan item. Periksa input Anda.';
                        Swal.fire('Gagal!', errorMessage, 'error');
                        return;
                    }

                    const newItem = result.item;
                    const items = getStoredItems();
                    items.push(newItem);
                    setStoredItems(items);

                    renderTable();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Item berhasil ditambahkan ke daftar sementara',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    hideRequestItemModal();
                    form.reset();

                } catch (error) {
                    console.error('Fetch Error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan jaringan atau sistem.', 'error');
                }
            });
        }
    });

    window.showConfirmMRModal = function() {
        const modal = document.getElementById('confirmMRModal');
        const soNumber = document.getElementById('so_id').options[document.getElementById('so_id').selectedIndex]
            .text.trim();
        const requestedBy = document.getElementById('requested_by').value;
        const requestDate = document.getElementById('request_date').value;
        const soId = document.getElementById('so_id').value;
        const currentItems = getStoredItems().filter(item => item.so_id === soId);

        document.getElementById('confirmSoNumber').textContent = soNumber;
        document.getElementById('confirmRequestedBy').textContent = requestedBy;
        document.getElementById('confirmRequestDate').textContent = requestDate;
        document.getElementById('confirmItemCount').textContent = `${currentItems.length} item`;

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

        const soId = document.getElementById('so_id').value;
        const requestedBy = document.getElementById('requested_by').value;
        const requestDate = document.getElementById('request_date').value;
        const itemsToProcess = getStoredItems().filter(item => item.so_id === soId);

        if (itemsToProcess.length === 0) {
            Swal.fire('Gagal!', 'Tidak ada item yang akan diproses.', 'error');
            return;
        }

        const payload = {
            _token: '{{ csrf_token() }}',
            so_id: soId,
            requested_by: requestedBy,
            request_date: requestDate,
            items: itemsToProcess
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

            localStorage.removeItem(STORAGE_KEY);

            document.getElementById('so_id').value = '';
            document.getElementById('requested_by').value = '';
            document.getElementById('request_date').value = '';

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
