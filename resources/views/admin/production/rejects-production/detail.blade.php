<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-app-layout>
    <x-slot name="header">
        Timeline Production (Rejects Production)
    </x-slot>

    <x-production.tabs-production :mrId="$mrId" :wip="$wipRecord" />
    <div class="bg-white rounded-xl shadow-sm mt-8">
        <div class="p-6 border-b">
            @if ($wipRecord && $rejectProduction)
                <button onclick="openUpdateModal()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Update rejection production
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[120px]">WIP. No</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Reason</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Action</th>
                        <th class="p-4 text-left font-semibold text-gray-600 max-sm:min-w-[120px]">Handle By</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @if ($rejectProduction)
                        <tr>
                            <td class="p-4 text-gray-500">{{ $rejectProduction->wip_record->wip_no }}</td>
                            <td class="p-4 text-gray-500">{{ $rejectProduction->wip_record->rejected_qty }}</td>
                            <td class="p-4 text-gray-500">{{ $rejectProduction->reason ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $rejectProduction->action ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $rejectProduction->handled_by ?? '-' }}</td>
                            <td class="p-4 text-gray-500">
                                {{ $rejectProduction->date ? $rejectProduction->date->format('d M Y') : '-' }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center p-12 text-gray-500">
                                @if (!$wipRecord)
                                    Tahap WIP belum dilalui/belum selesai
                                @else
                                    Tidak ada data rejects production ditemukan.
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div id="addRejectModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex max-md:items-center items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div onclick="hideAddRejectModal()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true">
            </div>

            {{-- Modal panel --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block max-sm:w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                {{-- Form untuk Update Reject Production --}}
                <form id="rejectsForm">
                    <input type="hidden" id="reject_id" name="reject_id" value="{{ $rejectProduction->id ?? '' }}">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 sm:mt-0 sm:ml-4 text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Update Rejection Production
                                </h3>
                                <div class="mt-2 space-y-4">
                                    {{-- Display WIP Info (Read Only) --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Work In Progress</label>
                                        <input type="text" id="wip_info" readonly
                                            value="{{ $wipRecord->wip_no }}"
                                            class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm text-sm focus:ring-0 focus:border-gray-300 focus:outline-none">
                                    </div>
                                    {{-- Display Quantity (Read Only) --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Rejected Quantity</label>
                                        <input type="text" id="quantity_info" readonly
                                            value="{{ $wipRecord->rejected_qty }} pcs"
                                            class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm text-sm focus:ring-0 focus:border-gray-300 focus:outline-none">
                                    </div>
                                    <div>
                                        <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                                        <select name="action" id="action"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Choose Action</option>
                                            <option value="rework">Rework</option>
                                            <option value="scrap">Scrap</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                                        <textarea name="reason" id="reason" rows="5" placeholder="Reason of rejects production"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>
                                    <div>
                                        <label for="handled_by" class="block text-sm font-medium text-gray-700">Handled by</label>
                                        <input type="text" name="handled_by" id="handled_by"
                                            placeholder="Type person who handle this rejects item"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" name="date" id="date"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update and Save
                        </button>
                        <button type="button" onclick="hideAddRejectModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>



<script>
    // Fungsi untuk membuka modal update dengan data yang sudah terisi
    window.openUpdateModal = function() {
        const modal = document.getElementById('addRejectModal');
        
        @if($rejectProduction)
        // Set editable fields dengan data yang ada
        document.getElementById('action').value = '{{ $rejectProduction->action ?? '' }}';
        document.getElementById('reason').value = `{{ $rejectProduction->reason ?? '' }}`;
        document.getElementById('handled_by').value = '{{ $rejectProduction->handled_by ?? '' }}';
        document.getElementById('date').value = '{{ $rejectProduction->date ? $rejectProduction->date->format('Y-m-d') : '' }}';
        @endif
        
        if (modal) {
            modal.style.display = 'block';
        }
    }

    window.showAddRejectModal = function() {
        openUpdateModal();
    }
    
    window.hideAddRejectModal = function() {
        const modal = document.getElementById('addRejectModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const rejectsForm = document.getElementById('rejectsForm');

        if (rejectsForm) {
            rejectsForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const form = event.target;
                const formData = new FormData(form);
                const rejectId = document.getElementById('reject_id').value;
                
                if (!rejectId) {
                    Swal.fire('Error!', 'ID Reject tidak ditemukan.', 'error');
                    return;
                }
                
                const url = `/admin/production/rejects-production/${rejectId}`;

                // Ambil data untuk konfirmasi
                const reason = formData.get('reason') || '-';
                const handledBy = formData.get('handled_by') || '-';
                const action = formData.get('action') || '-';

                // 1. SWEETALERT KONFIRMASI
                const confirmation = await Swal.fire({
                    title: 'Konfirmasi Update?',
                    html: `Anda akan mengupdate rejection dengan:<br>
                           Action: <strong>${action}</strong><br>
                           Alasan: <strong>${reason.substring(0, 50)}${reason.length > 50 ? '...' : ''}</strong><br>
                           Ditangani oleh: <strong>${handledBy}</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Update!',
                    cancelButtonText: 'Batal'
                });

                if (!confirmation.isConfirmed) {
                    return;
                }

                // Tampilkan loading SweetAlert
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengupdate data rejection.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const csrfToken = "{{ csrf_token() }}";
                    
                    // Tambahkan _method untuk Laravel PUT request
                    formData.append('_method', 'PUT');

                    const response = await fetch(url, {
                        method: 'POST', // Gunakan POST dengan _method PUT untuk Laravel
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        let errorMessage = errorData.message || 'Gagal mengupdate data karena kesalahan tak terduga.';

                        if (response.status === 422 && errorData.errors) {
                            errorMessage += '<br><br>Detail Validasi:<ul style="text-align: left; margin-left: 20px;">';
                            for (const key in errorData.errors) {
                                errorMessage += `<li>${errorData.errors[key][0]}</li>`;
                            }
                            errorMessage += '</ul>';
                        }

                        throw new Error(errorMessage);
                    }

                    const data = await response.json();
                    Swal.fire({
                        title: "Success!",
                        text: data?.message || 'Data berhasil diupdate.',
                        icon: "success"
                    }).then(() => {
                        hideAddRejectModal();
                        window.location.reload();
                    })

                } catch (error) {
                    console.error('AJAX Error:', error);
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
