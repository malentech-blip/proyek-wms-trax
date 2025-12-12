<x-app-layout>
    <x-slot name="header">
        Purchase Orders (Inbound)
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Filter Purchase Order</h3>

            {{-- PERBAIKAN ADA DI BARIS DI BAWAH INI --}}
            <form method="GET" action="{{ route('admin.inbound.purchase-orders.index') }}">
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="start_date" class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="search" class="text-sm font-medium text-gray-700">Cari No. PO / Pemasok</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" class="flex-1 block w-full border-gray-300 rounded-none rounded-l-md text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Cari nomor atau nama pemasok...">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 rounded-r-md hover:bg-gray-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">No. Pesanan (PO)</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Tanggal</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Pemasok</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($purchaseOrders as $po)
                    <tr>
                        <td class="p-4 text-gray-700 font-medium">{{ $po['number'] }}</td>
                        <td class="p-4 text-gray-500">{{ \Carbon\Carbon::createFromFormat('d/m/Y', $po['transDate'])->format('d/m/Y') }}</td>
                        <td class="p-4 text-gray-500">{{ $po['vendor']['name'] ?? 'N/A' }}</td>
                        <td class="p-4 text-gray-500">
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Synced</span>
                        </td>
                        <td class="p-4">
                            <a href="{{ route('admin.inbound.purchase-orders.receive', ['poId' => $po['id']]) }}" class="text-blue-600 hover:underline font-semibold">Receive Goods</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-12 text-gray-500">
                            Tidak ada data Purchase Order ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
