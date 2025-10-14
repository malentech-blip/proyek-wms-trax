<x-app-layout>
    <x-slot name="header">
        Reports Center
    </x-slot>

    <div class="space-y-6" x-data="{ activeTab: 'inbound' }">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Reports Center</h2>
                <p class="text-sm text-gray-500 mt-1">Analisis dan ekspor semua data operasional gudang.</p>
            </div>
            <a href="#" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Ekspor PDF
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <a href="#" @click.prevent="activeTab = 'inbound'"
                       :class="{ 'border-blue-600 text-blue-600': activeTab === 'inbound', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'inbound' }"
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Inbound
                    </a>
                    <a href="#" @click.prevent="activeTab = 'production'"
                       :class="{ 'border-blue-600 text-blue-600': activeTab === 'production', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'production' }"
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Production
                    </a>
                    <a href="#" @click.prevent="activeTab = 'inventory'"
                       :class="{ 'border-blue-600 text-blue-600': activeTab === 'inventory', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'inventory' }"
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Inventory
                    </a>
                    <a href="#" @click.prevent="activeTab = 'outbound'"
                       :class="{ 'border-blue-600 text-blue-600': activeTab === 'outbound', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'outbound' }"
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Outbound
                    </a>
                    <a href="#" @click.prevent="activeTab = 'reject'"
                       :class="{ 'border-blue-600 text-blue-600': activeTab === 'reject', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'reject' }"
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Reject
                    </a>
                    <a href="#" @click.prevent="activeTab = 'sync'"
                       :class="{ 'border-blue-600 text-blue-600': activeTab === 'sync', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'sync' }"
                       class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Sync Logs
                    </a>
                </nav>
            </div>

            <div class="mt-6 flex items-center space-x-4">
                <select class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-1/4">
                    <option>Semua Lokasi / Gudang</option>
                </select>
                <select class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-1/4">
                    <option>Semua Kategori Barang</option>
                </select>
                <button class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Tampilkan Laporan
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div x-show="activeTab === 'inbound'">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Table Inbound</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-left font-semibold text-gray-600">Tanggal</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Nomor PO</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Supplier</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Jumlah Barang</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Status QC</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Diterima oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($inboundReports as $report)
                            <tr>
                                <td class="p-4 text-gray-500">{{ $report->date }}</td>
                                <td class="p-4 text-gray-700 font-medium">{{ $report->po_number }}</td>
                                <td class="p-4 text-gray-500">{{ $report->supplier }}</td>
                                <td class="p-4 text-gray-500">{{ $report->quantity }}</td>
                                <td class="p-4">
                                    <span class="{{ $report->qc_status == 'LULUS' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $report->qc_status }}</span>
                                </td>
                                <td class="p-4 text-gray-500">{{ $report->received_by }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center p-12 text-gray-500">Tidak ada data inbound.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="activeTab !== 'inbound'" class="p-12 text-center text-gray-400">
                <p>Tabel untuk <span x-text="activeTab" class="font-semibold capitalize"></span> akan ditampilkan di sini.</p>
            </div>
        </div>
    </div>
</x-app-layout>