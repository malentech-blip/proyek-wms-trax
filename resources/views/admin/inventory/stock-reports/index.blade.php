<x-app-layout>
    <x-slot name="header">
        Stock Reports
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Stock Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel stock barang jadi.</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <select <option value="all">Filter: Location</option>
                    @foreach ($stockReports as $stockReport)
                    <option value="{{ $stockReport->id }}">{{ $stockReport->location->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" placeholder="Cari Nama atau Item..."
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
            </div>
            <a href="{{ route('admin.inventory.stock-reports.export') }}"
                class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center">
                Print Stock Report (Excel)
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
<thead class="bg-blue-600">
                    <tr>
<th class="p-4 text-left font-semibold text-white">Kode Produk</th>
<th class="p-4 text-left font-semibold text-white">Nama Produk</th>
<th class="p-4 text-left font-semibold text-white">Batch</th>
<th class="p-4 text-left font-semibold text-white">Qty</th>
<th class="p-4 text-left font-semibold text-white">Lokasi</th>
<th class="p-4 text-left font-semibold text-white">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($stockReports as $stockReport)
                    <tr>
                        <td class="p-4 text-gray-700 font-medium">{{ $stockReport->item->item_code }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->item->item_name }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->item->batch_no }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->quantity }}</td>
                        <td class="p-4 text-gray-500">{{ $stockReport->location->name }}</td>
                        <td class="p-4"><span
                                class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full">Aktif</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-12 text-gray-500">
                            Tidak ada data stock report ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stockReports->hasPages())
        <div class="p-4 border-t">
            {{ $stockReports->links() }}
        </div>
        @endif
    </div>
    </div>
</x-app-layout>
