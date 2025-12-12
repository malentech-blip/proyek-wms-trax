<x-app-layout>
    <x-slot name="header">
        Reject Warehouse (Inbound)
    </x-slot>

    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-semibold">Barang Ditolak Saat Penerimaan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua barang yang gagal Quality Check.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Nama Item</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Qty Ditolak</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Alasan</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Tanggal QC</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Status Tindakan</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($rejectedItems as $item)
                        <tr>
                            <td class="p-4 font-medium">{{ $item->goodsReceiptItem->item_name }}</td>
                            <td class="p-4 text-gray-500">{{ $item->rejected_qty }}</td>
                            <td class="p-4 text-gray-500">{{ $item->reason ?? '-' }}</td>
                            <td class="p-4 text-gray-500">{{ $item->created_at->format('d/m/Y') }}</td>
                            <td class="p-4">
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded-full capitalize">
                                    {{ $item->action }}
                                </span>
                            </td>
                            <td class="p-4 space-x-2">
                                <a href="#" class="text-blue-600 hover:underline">Retur</a>
                                <a href="#" class="text-red-600 hover:underline">Dispose</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center p-12 text-gray-500">Tidak ada barang yang ditolak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rejectedItems->hasPages())
            <div class="p-4 border-t">{{ $rejectedItems->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>