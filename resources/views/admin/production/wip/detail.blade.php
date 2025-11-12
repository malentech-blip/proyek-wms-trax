<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .disabled-btn {
        background-color: #9CA3AF !important;
        /* Warna abu-abu */
        cursor: not-allowed !important;
        /* Hilangkan efek hover untuk disabled */
        pointer-events: none;
        opacity: 0.6;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        Timeline Production (Work In Progress)
    </x-slot>

    <x-production.tabs-production :mrId="$mrId" :wip="$wipRecord" />
    <div class="bg-white rounded-xl shadow-sm mt-8">
        @if ($wipRecord)
            <div class="p-6 border-b flex flex-col gap-2">
                <h3 class="text-lg font-semibold text-gray-800">Detail WIP</h3>
                <div class="flex flex-col gap-3 mt-2">
                    <div class="flex items-center gap-2">
                        <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">WIP. No:</p>
                        <p class="font-medium">{{ $wipRecord->wip_no }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Status:</p>
                        <p class="font-medium">{{ $wipRecord->status }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Started At:</p>
                        <p class="font-medium">{{ $wipRecord->started_at ?? '--:--:--' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Finished At:</p>
                        <p class="font-medium">{{ $wipRecord->finished_at ?? '--:--:--' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <p class="max-sm:w-[100px] w-[200px] text-sm text-gray-600">Timer:</p>
                        <p class="timer font-medium" data-status="{{ $wipRecord->status }}"
                            data-started-at="{{ $wipRecord->started_at ? \Carbon\Carbon::parse($wipRecord->started_at)->format('Y-m-d\TH:i:s') : '' }}"
                            data-elapsed="{{ $wipRecord->elapsed_seconds }}">00:00:00</p>
                    </div>
                    <div class="flex items-center gap-3 mt-3">
                        @if ($wipRecord->status === 'Pending')
                            <form method="POST" action="{{ route('admin.production.wip.start', $wipRecord->id) }}"
                                class="inline">
                                @csrf
                                <button type="submit" class="py-2 px-4 bg-blue-500 text-white rounded">
                                    Start
                                </button>
                            </form>
                        @elseif ($wipRecord->status === 'Running')
                            <form method="POST" action="{{ route('admin.production.wip.pause', $wipRecord->id) }}">
                                @csrf
                                <button type="submit" class="py-2 px-4 bg-yellow-500 text-white rounded mt-3">
                                    Pause
                                </button>
                            </form>
                            <button type="button" onclick="showFinishModal({{ $wipRecord->id }})"
                                class="py-2 px-4 bg-green-600 text-white rounded">
                                Finish
                            </button>
                        @elseif ($wipRecord->status === 'Paused')
                            <form method="POST" action="{{ route('admin.production.wip.resume', $wipRecord->id) }}"
                                class="inline">
                                @csrf
                                <button type="submit" class="py-2 px-4 bg-green-500 text-white rounded">
                                    Resume
                                </button>
                            </form>
                        @elseif ($wipRecord->status === 'Completed' && $wipRecord->material_request->status === 'Delivered to WIP')
                            <button type="button"
                                onclick="showEditQtyModal({{ $wipRecord->id }}, {{ $wipRecord->produced_qty }}, {{ $wipRecord->rejected_qty }})"
                                class="py-2 px-4 bg-blue-500 text-white rounded">
                                Edit Quantity
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[200px]">WIP. No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[120px]">Started At</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Finished At</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Timer</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Produced Qty</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Reject Qty</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @if ($wipRecord)
                        <tr>
                            <td class="p-4 text-gray-700 font-medium">{{ $wipRecord->wip_no }}</td>
                            <td class="p-4 text-gray-700 font-medium">
                                {{ $wipRecord->started_at ?? '--:--:--' }}
                            </td>
                            <td class="p-4 text-gray-700 font-medium">
                                {{ $wipRecord->finished_at ?? '--:--:--' }}
                            </td>
                            <td class="p-4 text-gray-700 font-medium">
                                <span class="timer" data-status="{{ $wipRecord->status }}"
                                    data-started-at="{{ $wipRecord->started_at ? \Carbon\Carbon::parse($wipRecord->started_at)->format('Y-m-d\TH:i:s') : '' }}"
                                    data-elapsed="{{ $wipRecord->elapsed_seconds }}">
                                    00:00:00
                                </span>
                            </td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wipRecord->produced_qty }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wipRecord->rejected_qty }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wipRecord->status }}</td>
                        </tr>
                    @endif
                    {{-- <tr class="bg-white">
                        <td colspan="8" class="text-center p-12 text-gray-500">
                            Tidak ada data WIP.
                        </td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- FINISH MODAL --}}
    <div id="finishModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div onclick="hideFinishModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true">
            </div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block max-sm:w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                {{-- Tambahkan ID pada Form --}}
                <form id="finishForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 sm:mt-0 sm:ml-4 text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Selesaikan Produksi
                                </h3>
                                <div class="mt-2 space-y-4">
                                    {{-- INPUT FINISHED GOODS QTY --}}
                                    <div>
                                        <label for="finished_qty"
                                            class="block text-sm font-medium text-gray-700">Finished Goods Qty</label>
                                        <input type="number" name="finished_qty" id="finished_qty" required
                                            min="0" value="0"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    {{-- INPUT REJECTS GOODS QTY --}}
                                    <div>
                                        <label for="rejects_qty" class="block text-sm font-medium text-gray-700">Rejects
                                            Goods Qty</label>
                                        <input type="number" name="rejects_qty" id="rejects_qty" required
                                            min="0" value="0"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="finishSubmitButton"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Finish dan Simpan Data
                        </button>
                        <button type="button" onclick="hideFinishModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT QUANTITY MODAL --}}
    <div id="editQtyModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div onclick="hideEditQtyModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true">
            </div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block max-sm:w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <form id="editQtyForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 sm:mt-0 sm:ml-4 text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Edit Quantity Produksi
                                </h3>
                                <div class="mt-2 space-y-4">
                                    {{-- INPUT FINISHED GOODS QTY --}}
                                    <div>
                                        <label for="edit_finished_qty"
                                            class="block text-sm font-medium text-gray-700">Finished Goods Qty</label>
                                        {{-- Tambahkan ID unik untuk input Edit Qty --}}
                                        <input type="number" name="produced_qty" id="edit_produced_qty" required
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    {{-- INPUT REJECTS GOODS QTY --}}
                                    <div>
                                        <label for="edit_rejected_qty"
                                            class="block text-sm font-medium text-gray-700">Rejects
                                            Goods Qty</label>
                                        {{-- Tambahkan ID unik untuk input Edit Qty --}}
                                        <input type="number" name="rejected_qty" id="edit_rejected_qty" required
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="editQtySubmitButton"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Perubahan
                        </button>
                        <button type="button" onclick="hideEditQtyModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END OF MODAL EDIT QUANTITY --}}
</x-app-layout>

{{-- FINISH MODAL --}}
<script>
    // timer
    document.addEventListener("DOMContentLoaded", function() {
        const timers = document.querySelectorAll(".timer");

        timers.forEach(el => {
            const status = (el.dataset.status || "").toLowerCase();
            const startedAt = el.dataset.startedAt;
            const elapsed = parseInt(el.dataset.elapsed || 0);

            if (status === "running" && startedAt) {
                const startTime = new Date(startedAt).getTime();

                const updateTimer = () => {
                    const now = new Date().getTime();
                    const diff = Math.floor((now - startTime) / 1000) + elapsed;

                    const hours = String(Math.floor(diff / 3600)).padStart(2, "0");
                    const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, "0");
                    const seconds = String(diff % 60).padStart(2, "0");

                    el.textContent = `${hours}:${minutes}:${seconds}`;
                };

                updateTimer();
                setInterval(updateTimer, 1000);
            } else {
                const diff = elapsed;
                const hours = String(Math.floor(diff / 3600)).padStart(2, "0");
                const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, "0");
                const seconds = String(diff % 60).padStart(2, "0");
                el.textContent = `${hours}:${minutes}:${seconds}`;
            }
        });
    });
    // show finish modal
    window.showFinishModal = function(wipId) {
        const modal = document.getElementById('finishModal');
        const form = document.getElementById('finishForm');

        if (modal && form) {
            modal.style.display = 'block';
            form.action = `/admin/production/work-in-progress/${wipId}/finish`;
            form.method = 'POST';
            form.reset();
            const finishedQtyInput = document.getElementById('finished_qty');
            if (finishedQtyInput) {
                finishedQtyInput.focus();
                finishedQtyInput.select();
            }
        } else {
            console.error('Elemen modal atau form tidak ditemukan.');
        }
    }
    // hide finish modal
    window.hideFinishModal = function() {
        const modal = document.getElementById('finishModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const finishForm = document.getElementById('finishForm');

        if (finishForm) {
            finishForm.addEventListener('submit', async function(event) {
                event.preventDefault(); // Mencegah form submit standar

                const form = event.target;
                const finishedQty = document.getElementById('finished_qty').value;
                const rejectsQty = document.getElementById('rejects_qty').value;

                // --- 1. SWEETALERT KONFIRMASI DIMULAI DI SINI ---
                const confirmation = await Swal.fire({
                    title: 'Konfirmasi Penyelesaian?',
                    html: `Anda akan menyelesaikan WIP ini dengan: <br>
                           <strong class="text-green-600">${finishedQty}</strong> Finished Goods<br>
                           <strong class="text-red-600">${rejectsQty}</strong> Rejects Goods.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Selesaikan!',
                    cancelButtonText: 'Batal'
                });

                // Cek jika pengguna membatalkan (menekan 'Batal')
                if (!confirmation.isConfirmed) {
                    return; // Hentikan eksekusi, modal tidak jadi di-submit
                }
                // --- SWEETALERT KONFIRMASI SELESAI ---

                const formData = new FormData(form);
                const url = form.action;

                // Tampilkan loading SweetAlert (Setelah konfirmasi berhasil)
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // Pastikan ambil token CSRF dari form yang benar
                    const csrfToken = "{{ csrf_token() }}";

                    // 1. Lakukan request menggunakan Fetch API
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    // 2. Cek status respons dan error handling
                    if (!response.ok) {
                        const errorData = await response.json();
                        let errorMessage = errorData.message || 'Terjadi kesalahan tidak terduga.';

                        // Error validasi 422
                        if (response.status === 422 && errorData.errors) {
                            // ... (sama seperti sebelumnya) ...
                            errorMessage +=
                                '<br><br>Detail Kesalahan:<ul style="text-align: left; margin-left: 20px;">';
                            for (const key in errorData.errors) {
                                errorMessage += `<li>${errorData.errors[key].join(', ')}</li>`;
                            }
                            errorMessage += '</ul>';
                        }

                        throw new Error(errorMessage);
                    }

                    // 3. Ambil data JSON sukses
                    const data = await response.json();

                    // Tampilkan SweetAlert sukses
                    Swal.fire('Berhasil!', data.message, 'success');
                    Swal.fire({
                        title: "Success!",
                        text: data?.message,
                        icon: "success"
                    }).then(() => {
                        hideFinishModal();
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

{{-- VALIDASI FINISH MODAL --}}
<script>
    const finishedQtyInput = document.getElementById('finished_qty');
    const rejectsQtyInput = document.getElementById('rejects_qty');
    const submitButton = document.getElementById('finishSubmitButton');

    function checkFormValidity() {
        const finishedQty = parseInt(finishedQtyInput.value, 10) || 0;
        const rejectsQty = parseInt(rejectsQtyInput.value, 10) || 0;
        const isValid = finishedQty > 0 || rejectsQty > 0;

        if (isValid) {
            submitButton.disabled = false;
            submitButton.classList.remove('disabled-btn');
            submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('disabled-btn');
            submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        }
    }
    finishedQtyInput.addEventListener('input', checkFormValidity);
    rejectsQtyInput.addEventListener('input', checkFormValidity);
    checkFormValidity();
</script>

{{-- EDIT QUANTITY MODAL --}}
<script>
    window.showEditQtyModal = function(wipId, currentProducedQty, currentRejectedQty) {
        const modal = document.getElementById('editQtyModal');
        const form = document.getElementById('editQtyForm');
        const producedInput = document.getElementById('edit_produced_qty');
        const rejectedInput = document.getElementById('edit_rejected_qty');

        if (modal && form) {
            modal.style.display = 'block';
            form.action = `/admin/production/work-in-progress/${wipId}/update-quantity`;
            producedInput.value = currentProducedQty;
            rejectedInput.value = currentRejectedQty;

            checkEditQtyFormValidity();
            producedInput.focus();
            producedInput.select();
        } else {
            console.error('Elemen modal atau form Edit Quantity tidak ditemukan.');
        }
    }

    window.hideEditQtyModal = function() {
        const modal = document.getElementById('editQtyModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    const editProducedInput = document.getElementById('edit_produced_qty');
    const editRejectedInput = document.getElementById('edit_rejected_qty');
    const editSubmitButton = document.getElementById('editQtySubmitButton');

    function checkEditQtyFormValidity() {
        const producedQty = parseInt(editProducedInput.value, 10) || 0;
        const rejectedQty = parseInt(editRejectedInput.value, 10) || 0;

        const isValid = producedQty > 0 || rejectedQty > 0;

        if (isValid) {
            editSubmitButton.disabled = false;
            editSubmitButton.classList.remove('disabled-btn');
            editSubmitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else {
            editSubmitButton.disabled = true;
            editSubmitButton.classList.add('disabled-btn');
            editSubmitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        }
    }

    if (editProducedInput && editRejectedInput) {
        editProducedInput.addEventListener('input', checkEditQtyFormValidity);
        editRejectedInput.addEventListener('input', checkEditQtyFormValidity);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const editQtyForm = document.getElementById('editQtyForm');
        const wipId = "{{ $wipRecord->id ?? null }}"

        if (editQtyForm && wipId) {
            editQtyForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const form = event.target;
                const producedQty = document.getElementById('edit_produced_qty').value;
                const rejectedQty = document.getElementById('edit_rejected_qty').value;

                // SWEETALERT KONFIRMASI
                const confirmation = await Swal.fire({
                    title: 'Konfirmasi Perubahan Quantity?',
                    html: `Anda akan mengubah data menjadi: <br>
                           <strong class="text-green-600">${producedQty}</strong> Produced Qty<br>
                           <strong class="text-red-600">${rejectedQty}</strong> Rejects Qty.`,
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

                // Tampilkan loading SweetAlert
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const formData = new FormData(form);
                    const url = form.action;
                    const csrfToken = "{{ csrf_token() }}";

                    // Fetch API untuk PUT request
                    const response = await fetch(
                        `/admin/production/work-in-progress/${wipId}/change-quantity`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message ||
                            'Terjadi kesalahan saat mengedit quantity.');
                    }

                    const data = await response.json();

                    Swal.fire({
                        title: "Success!",
                        text: data?.message,
                        icon: "success"
                    }).then(() => {
                        hideEditQtyModal();
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
