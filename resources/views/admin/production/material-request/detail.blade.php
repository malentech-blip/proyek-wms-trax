<x-app-layout>
    <x-slot name="header">
        Timeline Production (Material Request)
    </x-slot>

    @php
        $statuses = ['Requested', 'Picked', 'Delivered to WIP'];
    @endphp

    <x-production.tabs-production :mrId="$mr->id" :wip="$wip" />
    <div class="mt-8 bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b flex flex-col gap-2">
            <h3 class="text-lg font-semibold text-gray-800">Detail Material Request</h3>
            <div class="flex flex-col gap-3 mt-2">
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Sales Order:</p>
                    <p class="font-medium">{{ $mr->salesOrder->so_number }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Requested By:</p>
                    <p class="font-medium">{{ $mr->requested_by }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Request Date:</p>
                    <p class="font-medium">
                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $mr->request_date)->format('d/m/Y') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <p class="w-[200px] text-sm text-gray-600">Status:</p>
                    <p class="font-medium">{{ $mr->status }}</p>
                </div>
            </div>
            @if ($wip && $wip->status == 'Completed' && $mr->status == 'Delivered to WIP')
                <button type="button" onclick="showCompleteProductionModal()"
                    class="w-full border-none rounded py-2 px-4 bg-blue-500 text-white hover:bg-blue-600 mt-5">
                    Mark as Completed Production
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">MR. No</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Item Request</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Requested By</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Request Date</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="p-4 text-gray-700 font-medium">{{ $mr->mr_no }}</td>
                        <td class="p-4 text-gray-500">
                            @foreach ($mr->pickingList as $item)
                                <p class="text-gray-700 text-sm mb-1">{{ $item->item->item_name }},
                                    {{ $item->quantity }} qty</p>
                            @endforeach
                        </td>
                        <td class="p-4 text-gray-500">{{ $mr->requested_by }}</td>
                        <td class="p-4 text-gray-500">
                            {{ \Carbon\Carbon::createFromFormat('Y-m-d', $mr['request_date'])->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-gray-500">
                            <span
                                class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">{{ $mr->status }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <div id="completeProductionModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto modal-container"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="hideCompleteProductionModal()"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form id="completeProductionForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Selesaikan Produksi
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menandai produksi ini sebagai **Selesai**? Aksi ini akan
                                        mengubah status Material Request (MR) terkait.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" id="completeProductionSubmit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Ya, Selesai
                        </button>
                        <button type="button" onclick="hideCompleteProductionModal()"
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
    window.showCompleteProductionModal = function() {
        const modal = document.getElementById('completeProductionModal');
        const form = document.getElementById('completeProductionForm');

        if (modal && form) {
            modal.style.display = 'block';
        } else {
            console.error('Elemen modal atau form Complete Production tidak ditemukan.');
        }
    }

    window.hideCompleteProductionModal = function() {
        const modal = document.getElementById('completeProductionModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const completeProductionForm = document.getElementById('completeProductionForm');
        if (completeProductionForm) {
            completeProductionForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                const form = event.target;

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menyelesaikan status produksi.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const formData = new FormData(form);
                    const mrId = '{{ $mr->id }}';
                    const url = `/admin/production/material-request/${mrId}/complete`;
                    const csrfToken = "{{ csrf_token() }}";
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: formData 
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        let errorMessage = errorData.message || 'Gagal menyelesaikan produksi.';
                        throw new Error(errorMessage);
                    }

                    const data = await response.json();

                    Swal.fire({
                        title: "Selesai!",
                        text: data?.message,
                        icon: "success"
                    }).then(() => {
                        hideCompleteProductionModal();
                        window.location = '{{ route("admin.production.material-request.index") }}'
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
