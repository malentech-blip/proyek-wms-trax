<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<x-app-layout>
    <x-slot name="header">
        Create Packing List
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
                <button type="button"
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
                <button type="button" id="openAddItemManualModalBtn"
                    class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 flex items-center gap-1">
                    Tambah Item Manual
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

    {{-- MODAL SCAN CONFIRMATION --}}
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

    {{-- MODAL TAMBAH ITEM MANUAL --}}
    <div id="addManualItemModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        {{-- Overlay (Diberi ID untuk tutup) --}}
        <div id="modalOverlay" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        {{-- Modal Panel --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Tambah Item Manual
                    </h3>
                    <div class="mt-4">
                        {{-- Formulir Manual --}}
                        <form id="addManualItemForm">
                            <div class="space-y-4">
                                <div>
                                    <label for="fg_id" class="block text-sm font-medium text-gray-700">
                                        Kode Item
                                    </label>
                                    <select type="text" id="fg_id" name="fg_id"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Contoh: FG-001" required>
                                        <option value="">Pilih Item</option>
                                        @foreach ($finishedGoods as $fg)
                                            <option value="{{ $fg->id }}">{{ $fg->item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="quantity"
                                        class="block text-sm font-medium text-gray-700">Kuantitas</label>
                                    <input type="number" id="quantity" name="quantity"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Masukkan kuantitas item" min="1" required>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Footer Modal (Tombol Aksi) --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="submitManualItemBtn"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tambahkan Item
                    </button>
                    {{-- Tombol Batal (Diberi ID untuk tutup) --}}
                    <button type="button" id="closeManualModalBtn"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
</x-app-layout>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('addManualItemModal');
        const openBtn = document.getElementById('openAddItemManualModalBtn');
        const closeBtn = document.getElementById('closeManualModalBtn');
        const overlay = document.getElementById('modalOverlay');
        const submitBtn = document.getElementById('submitManualItemBtn');
        const form = document.getElementById('manualItemForm');

        function openModal() {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);

        // Event Listener untuk tombol submit
        submitBtn.addEventListener('click', function() {
            closeModal();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    });
</script>


<script>
    // --- KONFIGURASI ENDPOINTS & STORAGE ---
    const API_VALIDATE_QR = '/admin/outbound/packing-lists/validate-qr';
    const API_ADD_ITEM = '/admin/outbound/packing-lists/add-temp-item';
    const API_SUBMIT_PACKING_LIST = '/admin/outbound/packing-lists/store'; // <--- ENDPOINT BARU
    const STORAGE_KEY = 'temp_scanned_items';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function getStoredItems() {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    }

    function setStoredItems(items) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    }

    const manualModal = document.getElementById('addManualItemModal');
    const scanConfirmModal = document.getElementById('scanConfirmModal');

    function openManualModal() {
        manualModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeManualModal() {
        manualModal.classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('addManualItemForm').reset();
    }

    function openScanConfirmModal() {
        scanConfirmModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeScanConfirmModal() {
        scanConfirmModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

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

        // 1. Validasi Angka Positif
        if (isNaN(newQuantity) || newQuantity <= 0) {
            // Kembalikan ke nilai sebelumnya jika input tidak valid
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

        // 2. Validasi Batas Qty Ready
        if (newQuantity > qtyReady) {
            // Kembalikan ke nilai batas maksimal Qty Ready
            inputElement.value = qtyReady;
            item.quantity = qtyReady; // Update di data
            setStoredItems(items); // Simpan perubahan

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

        // 3. Update dan Simpan Jika Valid
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

    // --- FUNGSI REVISI: RENDER TABEL ---
    function renderScannedTable() {
        const items = getStoredItems();
        const tbody = document.querySelector('.overflow-x-auto table tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (items.length === 0) {
            // Colspan diubah menjadi 7 (QR, Code, Item, Qty Out, Qty Ready, Lokasi, Aksi)
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
                text: `${newItem.item_name} berhasil ditambahkan.`,
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
        closeManualModal();
        const itemData = {
            fg_id: fgId,
            quantity: quantity,
        }
        await addItemToPackingList(itemData);
    }

    function showConfirmModal(itemData) {
        // Ambil elemen yang diperlukan
        const confirmBtn = document.getElementById('confirmAddItemBtn');
        const originalQtyInput = document.getElementById('original_quantity');
        const displayOriginalQty = document.getElementById('display_original_quantity');
        const inputQty = document.getElementById('scan_input_quantity');
        const qtyError = document.getElementById('qty_error_message');

        // Simpan data item ke tombol konfirmasi (untuk diakses nanti)
        confirmBtn.itemData = itemData;

        // Isi detail di modal
        document.getElementById('confirm_qr_code').textContent = itemData.qr_code;
        document.getElementById('confirm_fg_id').textContent = itemData.fg_id;
        document.getElementById('confirm_item_name').textContent = itemData.item_name;

        // Isi dan tampilkan kuantitas asli
        originalQtyInput.value = itemData.quantity; // Hidden field
        displayOriginalQty.textContent = itemData.quantity; // Display

        // Atur input field kuantitas ke nilai default QR dan batasan MAX
        inputQty.value = itemData.quantity;
        inputQty.max = itemData.quantity; // Batasi max input agar tidak melebihi kuantitas asli
        qtyError.classList.add('hidden'); // Sembunyikan pesan error awal

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


        document.getElementById('openAddItemManualModalBtn').addEventListener('click', openManualModal);
        document.getElementById('closeManualModalBtn').addEventListener('click', closeManualModal);
        document.getElementById('submitManualItemBtn').addEventListener('click', addTempItem);

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




    // SUBMIT PACKINGLIST
    function openSubmitModal() {
        const items = getStoredItems();
        const countDisplay = document.getElementById('totalItemsCount');
        const warning = document.getElementById('emptyListWarning');
        const confirmBtn = document.getElementById('confirmSubmitBtn');

        countDisplay.textContent = items.length;

        if (items.length === 0) {
            warning.style.display = 'block';
            confirmBtn.disabled = true;
        } else {
            warning.style.display = 'none';
            confirmBtn.disabled = false;
        }

        submitPackingListModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSubmitModal() {
        submitPackingListModal.classList.add('hidden');
        document.body.style.overflow = '';
    }


    async function submitPackingList() {
        const items = getStoredItems();
        const urlParams = new URLSearchParams(window.location.search);
        const so_id = urlParams.get('so_id') || 'UNKNOWN';

        closeSubmitModal();

        Swal.fire({
            title: 'Memproses Packing List...',
            text: 'Sedang mengirim data item ke server.',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        const payload = {
            so_id: so_id,
            items: items,
            // Tambahkan data lain yang mungkin dibutuhkan (e.g., operator_id, notes, dll.)
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
            renderScannedTable(); // Perbarui tampilan tabel kosong

            Swal.fire({
                icon: 'success',
                title: 'Packing List Berhasil Dibuat!',
                text: `Nomor PL: ${result.packing_list_number || 'Tersimpan'}`,
                confirmButtonText: 'OK'
            }).then(() => {
                // Opsional: Redirect ke halaman detail/daftar Packing List
                // window.location.href = result.redirect_url || '/admin/outbound/packing-lists'; 
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
                closeManualModal();
                closeScanConfirmModal();
                closeSubmitModal();
            }
        });
    });
</script>
