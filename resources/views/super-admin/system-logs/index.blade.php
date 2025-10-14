<x-app-layout>
    <x-slot name="header">
        System Logs
    </x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">System Logs</h2>
            <p class="text-sm text-gray-500 mt-1">Lacak semua aktivitas pengguna dan sistem yang terjadi.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800">Filter</h3>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-1">
                    <label for="user" class="text-sm font-medium text-gray-700">Pengguna</label>
                    <select id="user" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option>Semua Pengguna</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                    <select id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option>Semua Status</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Tampilkan Laporan
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Pengguna</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Modul</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Aktivitas</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($logs as $log)
                        <tr>
                            <td class="p-4 text-gray-500 whitespace-nowrap">{{ $log->date }}</td>
                            <td class="p-4 text-gray-700 font-medium whitespace-nowrap">{{ $log->user }}</td>
                            <td class="p-4 text-gray-500 whitespace-nowrap">{{ $log->module }}</td>
                            <td class="p-4 text-gray-500 w-full">{{ $log->activity }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="{{ $log->status == 'SUCCESS' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $log->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-12 text-gray-500">
                                Tidak ada data log sistem.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>