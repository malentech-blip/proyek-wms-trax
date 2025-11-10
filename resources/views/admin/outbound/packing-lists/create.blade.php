<x-app-layout>
    <x-slot name="header">
        Create Packing List (Outbound)
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Sales Order</h2>
            @if ($salesOrder)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div><span class="text-gray-500">SO Number:</span> <span
                            class="font-medium">{{ $salesOrder['number'] ?? '-' }}</span></div>
                    <div><span class="text-gray-500">Date:</span> <span
                            class="font-medium">{{ $salesOrder['transDate'] }}</span>
                    </div>
                    <div><span class="text-gray-500">Customer:</span> <span
                            class="font-medium">{{ $salesOrder['customer']['name'] ?? '-' }}</span></div>
                </div>
                @php
                    $items = $salesOrder['detailItem'] ?? [];
                @endphp
                @foreach ($items as $item)
                    <div class="grid grid-cols-5 gap-5 mt-10">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-500 text-sm">Item No</p>
                            <p class="font-medium">{{ $item['item']['no'] }}</p>
                        </div>
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
            @else
                <div class="text-sm text-gray-600">Detail SO tidak tersedia. Anda tetap dapat melakukan scan.</div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm space-y-4">
            <h3 class="font-semibold">Scan Finished Goods</h3>
            <div class="flex gap-3 items-start">
                <form id="qrScanForm" class="flex-1 w-full">
                    <input type="text" id="qrCodeInput" name="qr_code"
                        placeholder="Tulis QR Code/Scan QR code di sini"
                        class="border rounded-lg px-3 py-2 w-full flex-1" required />
                </form>
                <button type="button" id="openCameraScanBtn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-1">
                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Camera
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Code</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Qty Out</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Qty Ready</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <button type="button" id="openSubmitModalBtn"
                class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-150">
                Buat Packing List
            </button>
        </div>
    </div>

    <div id="scanConfirmModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div id="scanModalOverlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Konfirmasi Item Scan
                    </h3>
                    <div class="mt-4 space-y-3">
                        <p class="text-sm text-gray-700">Item berikut terdeteksi. Silakan verifikasi kuantitas.</p>

                        <div class="bg-gray-100 p-3 rounded-lg border">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-gray-500">QR Code:</span>
                                <span id="confirm_qr_code" class="font-semibold text-gray-800 break-all"></span>
                            </div>
                            <span id="confirm_fg_id" class="hidden font-semibold text-gray-800 break-all"></span>
                            <input type="hidden" id="original_quantity" value="">
                            <div class="flex justify-between text-sm mt-1">
                                <span class="font-medium text-gray-500">Item Name:</span>
                                <span id="confirm_item_name" class="font-semibold text-blue-600"></span>
                            </div>
                            <div class="flex justify-between text-sm mt-1">
                                <span class="font-medium text-gray-500">Kuantitas Asli QR:</span>
                                <span id="display_original_quantity" class="font-semibold text-gray-600"></span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <label for="scan_input_quantity" class="block text-sm font-medium text-gray-700">
                                Kuantitas Item
                            </label>
                            <input type="number" id="scan_input_quantity" min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Masukkan kuantitas aktual">
                            <p class="text-xs text-red-500 mt-1 hidden" id="qty_error_message">Kuantitas tidak boleh
                                melebihi kuantitas asli.</p>
                        </div>

                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmAddItemBtn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Tambahkan
                    </button>
                    <button type="button" id="cancelScanConfirmBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SUBMIT --}}
    <div id="submitPackingListModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div id="submitModalOverlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Konfirmasi Pembuatan Packing List
                    </h3>
                    <div class="mt-4 space-y-3">
                        <p class="text-sm text-gray-700">Anda akan membuat Packing List berdasarkan **<span
                                id="totalItemsCount">0</span>** item yang sudah di-scan.</p>
                        <p class="text-sm font-semibold text-red-600" id="emptyListWarning" style="display:none;">
                            Daftar item masih kosong! Anda harus menambahkan minimal 1 item.</p>
                        <p class="text-sm text-gray-700">Pastikan semua data sudah benar sebelum melanjutkan.</p>
                        <div class="pt-3 border-t">
                            <label for="packed_by_input" class="block text-sm font-medium text-gray-700 mb-2">
                                Dikemas Oleh <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="packed_by_input" placeholder="Masukkan nama petugas packing"
                                class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                            <p class="text-xs text-gray-500 mt-1">Nama petugas yang melakukan proses packing</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmSubmitBtn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Ya, Buat Packing List
                    </button>
                    <button type="button" id="cancelSubmitBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="scanModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div id="scanModalBg" class="absolute inset-0"></div>
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-lg font-semibold mb-4">Scan Item</h2>
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 id="scanModeTitle" class="font-medium text-gray-700">Mode Input Manual</h3>
            </div>
            <form id="scanForm" class="">
                <input type="text" id="qrInput" placeholder="Scan QR Code di sini..."
                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 p-2">
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" data-action="close-scan-modal"
                        class="px-4 py-2 bg-gray-200 rounded-md text-gray-700">Batal</button>
                    <button type="submit" id="submitScanBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Verifikasi
                    </button>
                </div>
            </form>

            <div id="cameraContainer" class="hidden">
                <div id="reader" class="w-full" style="min-height: 250px;"></div>
                <p class="text-xs text-center text-gray-500 mt-2">Arahkan kamera ke QR/Barcode</p>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="cancelCameraBtn" class="px-4 py-2 bg-red-600 text-white rounded-md">
                        Batalkan Scan
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

{{-- GLOBAL FUNCTION --}}
<script>
    window.removeTempItem = function(index) {
        if (!confirm('Anda yakin ingin menghapus item ini?')) return;
        let items = getStoredItems();
        items.splice(index, 1);
        setStoredItems(items);
        renderScannedTable();
        Swal.fire({
            icon: 'warning',
            title: 'Dihapus!',
            text: 'Item berhasil dihapus dari daftar.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
    window.updateItemQuantity = function(index, inputElement) {
        let items = getStoredItems();
        let item = items[index];
        const newQuantity = parseInt(inputElement.value);
        const qtyReady = parseInt(item.quantity_ready);
        if (isNaN(newQuantity) || newQuantity <= 0) {
            inputElement.value = item.quantity;
            Swal.fire({
                icon: 'warning',
                title: 'Kuantitas Gagal Update',
                text: 'Kuantitas harus berupa angka positif.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }
        if (newQuantity > qtyReady) {
            inputElement.value = qtyReady;
            item.quantity = qtyReady;
            setStoredItems(items);
            Swal.fire({
                icon: 'error',
                title: 'Kuantitas Melebihi Batas!',
                text: `Kuantitas (${newQuantity}) tidak boleh melebihi stok siap (${qtyReady}). Kuantitas disetel ke ${qtyReady}.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000
            });
            return;
        }
        item.quantity = newQuantity;
        setStoredItems(items);
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Update!',
            text: `Kuantitas untuk ${item.fg_name} diubah menjadi ${newQuantity}.`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    };
</script>


<script>
    const API_VALIDATE_QR = '/admin/outbound/packing-lists/validate-qr';
    const API_ADD_ITEM = '/admin/outbound/packing-lists/add-temp-item';
    const API_SUBMIT_PACKING_LIST = '/admin/outbound/packing-lists/store';
    const STORAGE_KEY = 'temp_scanned_items';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    // LOCAL STORAGE
    function getStoredItems() {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    function setStoredItems(items) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    }

    // SCANNING 
    const scanConfirmModal = document.getElementById('scanConfirmModal');

    function openScanConfirmModal() {
        scanConfirmModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeScanConfirmModal() {
        scanConfirmModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function renderScannedTable() {
        const items = getStoredItems();
        const tbody = document.querySelector('.overflow-x-auto table tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (items.length === 0) {
            tbody.innerHTML =
                `<tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada item.</td></tr>`;
            return;
        }

        items.forEach((item, index) => {
            const fgCodeDisplay = item?.fg_code ?? '-';
            const qtyReadyDisplay = item?.quantity_ready ?? item
                ?.quantity;
            const locationDisplay = item?.location ?? 'Unknown';

            const row = tbody.insertRow();
            row.classList.add('border-t');

            row.innerHTML = `
            <td class="px-4 py-2 text-xs">${fgCodeDisplay}</td>
            <td class="px-4 py-2">${item?.fg_name}</td>
            
            <td class="px-2 py-2 w-32">
                <input type="number" 
                       value="${item?.quantity}" 
                       min="1" 
                       max="${qtyReadyDisplay}"
                       onchange="updateItemQuantity(${index}, this)"
                       class="font-semibold text-blue-600 border border-gray-300 rounded-md shadow-sm w-full py-1 px-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                >
            </td>

            <td class="px-4 py-2 text-green-600">${qtyReadyDisplay}</td>
            <td class="px-4 py-2 text-sm">${locationDisplay}</td>
            <td class="px-4 py-2">
                <button type="button" 
                        onclick="removeTempItem(${index})" 
                        class="px-2 py-1 text-sm rounded border text-red-600 border-red-300 hover:bg-red-50">
                    Hapus
                </button>
            </td>
        `;
        });
    }
    async function validateQrCode(qrCode) {
        const urlParams = new URLSearchParams(window.location.search);
        const so_id = urlParams.get('so_id') || 'UNKNOWN';
        if (!qrCode) return;
        Swal.fire({
            title: 'Memvalidasi...',
            text: 'Mencari item dengan QR Code: ' + qrCode,
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        try {
            const response = await fetch(API_VALIDATE_QR, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    so_id: so_id
                })
            });

            const result = await response.json();
            Swal.close();
            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Validasi!',
                    text: result.message || 'QR Code tidak valid atau item tidak ditemukan.'
                });
                return;
            }

            showConfirmModal(result.data);

        } catch (error) {
            console.error('Error saat validasi QR:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Gagal terhubung ke server validasi.'
            });
        }
    }

    async function addItemToPackingList(itemData) {
        const urlParams = new URLSearchParams(window.location.search);
        const so_id = urlParams.get('so_id') || 'UNKNOWN';

        const payload = {
            so_id: so_id,
            ...itemData,
        };

        Swal.fire({
            title: 'Menambahkan Item...',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        closeScanConfirmModal();
        try {
            const response = await fetch(API_ADD_ITEM, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: result.message || 'Gagal menambahkan item.'
                });
                return;
            }

            const newItem = result.item;
            let items = getStoredItems();
            items.push(newItem);
            setStoredItems(items);

            renderScannedTable();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `${newItem.fg_name} berhasil ditambahkan.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

        } catch (error) {
            console.error('Error saat mengirim Fetch:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Terhubung',
                text: 'Gagal mengirim data ke server.'
            });
        }
    }

    async function addTempItem() {
        const itemSelect = document.getElementById('fg_id');
        const quantityInput = document.getElementById('quantity');

        const fgId = itemSelect.value;
        const item_name = itemSelect.options[itemSelect.selectedIndex].text;
        const quantity = parseInt(quantityInput.value);

        if (!fgId || isNaN(quantity) || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Harap pilih Item dan masukkan Kuantitas yang valid.'
            });
            return;
        }
        const itemData = {
            fg_id: fgId,
            quantity: quantity,
        }
        await addItemToPackingList(itemData);
    }

    function showConfirmModal(itemData) {
        const confirmBtn = document.getElementById('confirmAddItemBtn');
        const originalQtyInput = document.getElementById('original_quantity');
        const displayOriginalQty = document.getElementById('display_original_quantity');
        const inputQty = document.getElementById('scan_input_quantity');
        const qtyError = document.getElementById('qty_error_message');

        confirmBtn.itemData = itemData;
        document.getElementById('confirm_qr_code').textContent = itemData.qr_code;
        document.getElementById('confirm_fg_id').textContent = itemData.fg_id;
        document.getElementById('confirm_item_name').textContent = itemData.item_name;

        originalQtyInput.value = itemData.quantity;
        displayOriginalQty.textContent = itemData.quantity;

        inputQty.value = itemData.quantity;
        inputQty.max = itemData.quantity;
        qtyError.classList.add('hidden');
        openScanConfirmModal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderScannedTable();
        const qrScanForm = document.getElementById('qrScanForm');
        const qrCodeInput = document.getElementById('qrCodeInput');

        qrScanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            validateQrCode(qrCodeInput.value);
            qrCodeInput.value = ''
        });
        document.getElementById('cancelScanConfirmBtn').addEventListener('click', closeScanConfirmModal);
        document.getElementById('scanModalOverlay').addEventListener('click', closeScanConfirmModal);

        document.getElementById('confirmAddItemBtn').addEventListener('click', function(e) {
            let itemData = e.currentTarget.itemData;
            const inputQtyElement = document.getElementById('scan_input_quantity');
            const originalQty = parseInt(document.getElementById('original_quantity').value);
            const inputQty = parseInt(inputQtyElement.value);
            const qtyError = document.getElementById('qty_error_message');

            if (isNaN(inputQty) || inputQty <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Kuantitas',
                    text: 'Kuantitas harus berupa angka positif.'
                });
                return;
            }
            if (inputQty > originalQty) {
                qtyError.textContent =
                    `Kuantitas tidak boleh melebihi kuantitas asli (${originalQty}).`;
                qtyError.classList.remove('hidden');
                return;
            } else {
                qtyError.classList.add('hidden');
            }
            if (itemData) {
                itemData.quantity = inputQty;
                const transformedData = {
                    ...itemData,
                    fg_id: String(itemData.fg_id),
                }
                addItemToPackingList(transformedData);
            }
        });
    });


    function openSubmitModal() {
        const items = getStoredItems();
        const countDisplay = document.getElementById('totalItemsCount');
        const warning = document.getElementById('emptyListWarning');
        const confirmBtn = document.getElementById('confirmSubmitBtn');
        const packedByInput = document.getElementById('packed_by_input');

        countDisplay.textContent = items.length;

        // Reset input packed_by
        packedByInput.value = '';

        if (items.length === 0) {
            warning.style.display = 'block';
            confirmBtn.disabled = true;
        } else {
            warning.style.display = 'none';
            confirmBtn.disabled = false;
        }

        submitPackingListModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        setTimeout(() => packedByInput.focus(), 100);
    }

    function closeSubmitModal() {
        submitPackingListModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    async function submitPackingList() {
        const items = getStoredItems();
        const urlParams = new URLSearchParams(window.location.search);
        const so_number = '{{ $salesOrder['number'] }}';
        const packedByInput = document.getElementById('packed_by_input');
        const packedBy = packedByInput.value.trim();

        // Validasi input packed_by
        if (!packedBy) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Tidak Lengkap',
                text: 'Harap isi nama petugas packing terlebih dahulu.'
            });
            packedByInput.focus();
            return;
        }

        closeSubmitModal();

        Swal.fire({
            title: 'Memproses Packing List...',
            text: 'Sedang mengirim data item ke server.',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        const payload = {
            so_number: so_number,
            packed_by: packedBy, // Tambahkan packed_by ke payload
            items: items,
        };

        try {
            const response = await fetch(API_SUBMIT_PACKING_LIST, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat PL!',
                    text: result.message || 'Terjadi kesalahan saat menyimpan data Packing List.'
                });
                return;
            }

            // Sukses: Bersihkan local storage dan tampilkan pesan sukses
            localStorage.removeItem(STORAGE_KEY);
            renderScannedTable();

            Swal.fire({
                icon: 'success',
                title: 'Packing List Berhasil Dibuat!',
                text: `Packing list berhasil tersimpan. Dikemas oleh: ${packedBy}`,
                confirmButtonText: 'OK'
            }).then((res) => {
                const id = result?.packingId
                const baseUrl = '/admin/outbound/packing-lists/detail'
                window.location.href = `${baseUrl}/${id}`
            });

        } catch (error) {
            console.error('Error saat submit Packing List:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error Koneksi',
                text: 'Gagal terhubung ke server untuk menyelesaikan proses.'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('openSubmitModalBtn').addEventListener('click', openSubmitModal);
        document.getElementById('cancelSubmitBtn').addEventListener('click', closeSubmitModal);
        document.getElementById('submitModalOverlay').addEventListener('click', closeSubmitModal);
        document.getElementById('confirmSubmitBtn').addEventListener('click', submitPackingList);
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeScanConfirmModal();
                closeSubmitModal();
            }
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        let cameraMode = false;
        let html5QrCode = null;

        const scanModal = document.getElementById('scanModal');
        const scanModalBg = document.getElementById('scanModalBg');
        const qrInput = document.getElementById('qrInput');
        const scanForm = document.getElementById('scanForm');
        const cameraContainer = document.getElementById('cameraContainer');
        const cancelCameraBtn = document.getElementById('cancelCameraBtn');
        const submitScanBtn = document.getElementById('submitScanBtn');
        const scanModeTitle = document.getElementById('scanModeTitle');

        const globalScanBtn = document.getElementById('openGlobalScanBtn');
        const openCameraBtn = document.getElementById('openCameraScanBtn');

        function updateModalView() {
            scanForm.classList.add('hidden');
            cameraContainer.classList.add('hidden');

            if (cameraMode) {
                cameraContainer.classList.remove('hidden');
                scanModeTitle.textContent = 'Mode Kamera';
            } else {
                scanForm.classList.remove('hidden');
                scanModeTitle.textContent = 'Mode Input Manual';
            }
        }

        function openInputModal() {
            cameraMode = false;
            qrInput.value = '';
            updateModalView();
            scanModal.classList.remove('hidden');
            qrInput.focus();
        }

        function openCameraModal() {
            cameraMode = true;
            qrInput.value = '';
            updateModalView();
            scanModal.classList.remove('hidden');
            startScan();
        }

        function closeModal() {
            scanModal.classList.add('hidden');
            stopScan();
        }

        function startScan() {
            if (html5QrCode) {
                stopScan();
            }

            html5QrCode = new Html5Qrcode('reader');
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            html5QrCode.start({
                    facingMode: 'environment'
                }, config,
                (decodedText, decodedResult) => {
                    stopScan();
                    closeModal();

                    if (window.validateQrCode) {
                        window.validateQrCode(decodedText);
                        document.querySelector('#qrCodeInput').value = decodedText
                    } else {
                        alert('Error: Fungsi validasi tidak siap.');
                    }
                },
                (errorMessage) => {}
            ).catch((err) => {
                alert('Gagal memulai kamera. Pastikan Anda memberi izin akses.');
                stopScan();
            });
        }

        function stopScan() {
            if (html5QrCode) {
                try {
                    html5QrCode.stop().then(() => {
                        html5QrCode = null;
                    }).catch(err => {
                        html5QrCode = null;
                    });
                } catch (e) {
                    html5QrCode = null;
                }
            }
            cameraMode = false;
        }

        function handleScanSubmit(event) {
            if (event) event.preventDefault();
            const qrCode = qrInput.value;
            if (!qrCode) return;
            closeModal();
            if (window.validateQrCode) {
                window.validateQrCode(qrCode);
            } else {
                alert('Error: Fungsi validasi tidak siap.');
            }
        }

        if (globalScanBtn) {
            globalScanBtn.addEventListener('click', openInputModal);
        }

        if (openCameraBtn) {
            openCameraBtn.addEventListener('click', openCameraModal);
        }

        document.querySelectorAll('[data-action="close-scan-modal"]').forEach(button => {
            button.addEventListener('click', closeModal);
        });

        if (scanModalBg) {
            scanModalBg.addEventListener('click', closeModal);
        }

        cancelCameraBtn.addEventListener('click', () => {
            scanModal.classList.add('hidden');
            stopScan();
        });
        scanForm.addEventListener('submit', handleScanSubmit);
    });
</script>
