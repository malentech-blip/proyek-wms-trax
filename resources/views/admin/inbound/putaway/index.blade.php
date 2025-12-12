<x-app-layout>
    <x-slot name="header">Antrian Putaway (Penempatan)</x-slot>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-green-50 text-green-900 font-semibold">
                <tr>
                    <th class="p-4">No. Penerimaan</th>
                    <th class="p-4">No. PO</th>
                    <th class="p-4">Total Item</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendingPutaway as $gr)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-medium">{{ $gr->receipt_number }}</td>
                    <td class="p-4">{{ $gr->po_number }}</td>
                    <td class="p-4">{{ $gr->items->count() }} Jenis</td>
                    <td class="p-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Siap Putaway</span></td>
                    <td class="p-4">
                        <a href="{{ route('admin.inbound.putaway.show', $gr) }}" 
                           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-xs">
                           Tempatkan Barang &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-gray-500">Tidak ada antrian Putaway.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $pendingPutaway->links() }}</div>
    </div>
</x-app-layout>