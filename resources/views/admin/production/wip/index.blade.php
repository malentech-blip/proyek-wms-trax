<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-app-layout>
    <x-slot name="header">
        Work In Progress
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Filter Picking List</h3>

            {{-- FILTER FORM --}}
            <form method="GET">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="start_date" class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="search" class="text-sm font-medium text-gray-700">Cari No. SO / Pelanggan</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="flex-1 block w-full border-gray-300 rounded-none rounded-l-md text-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari nomor atau nama pemasok...">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 rounded-r-md hover:bg-gray-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        {{-- Table --}}
        <div class="w-full overflow-x-auto">
            <table class="min-w-max text-sm border-collapse">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[60px]">No</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[200px]">WIP. No</th>
                        {{-- <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">MR. No</th> --}}
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[120px]">Started At</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Finished At</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Timer</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[140px]">Produced Qty</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Reject Qty</th>
                        <th class="p-4 text-left font-semibold text-gray-600 min-w-[160px]">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($wipRecords as $wip)
                        <tr class="cursor-pointer" onclick="window.location='{{ route('admin.production.wip.detail', $wip->mr_id) }}'"">
                            <td class="p-4 text-gray-700 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wip->wip_no }}</td>
                            {{-- <td class="p-4 text-gray-700 font-medium">{{ $wip->material_request->mr_no }}</td> --}}
                            <td class="p-4 text-gray-700 font-medium">
                                {{ $wip->started_at ?? '--:--:--' }}
                            </td>
                            <td class="p-4 text-gray-700 font-medium">
                                {{ $wip->finished_at ?? '--:--:--' }}
                            </td>
                            <td class="p-4 text-gray-700 font-medium">
                                <span class="timer" data-status="{{ $wip->status }}"
                                    data-started-at="{{ $wip->started_at ? \Carbon\Carbon::parse($wip->started_at)->format('Y-m-d\TH:i:s') : '' }}"
                                    data-elapsed="{{ $wip->elapsed_seconds }}">
                                    00:00:00
                                </span>
                            </td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wip->produced_qty }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wip->rejected_qty }}</td>
                            <td class="p-4 text-gray-700 font-medium">{{ $wip->status }}</td>
                        </tr>
                    @empty
                        <tr class="bg-white">
                            <td colspan="8" class="text-center p-12 text-gray-500">
                                Tidak ada data WIP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>


<script>
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
</script>
