<x-app-layout>
    <x-slot name="header">Antrian Quality Check</x-slot>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-indigo-50 text-indigo-900 font-semibold">
                <tr>
                    <th class="p-4">No. Penerimaan</th>
                    <th class="p-4">No. PO</th>
                    <th class="p-4">Tanggal Terima</th>
                    <th class="p-4">Penerima</th>
                    <th class="p-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendingQC as $gr)
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-medium">{{ $gr->receipt_number }}</td>
                    <td class="p-4">{{ $gr->po_number }}</td>
                    <td class="p-4">{{ $gr->receipt_date }}</td>
                    <td class="p-4">{{ $gr->receivedBy->name ?? '-' }}</td>
                    <td class="p-4">
                        <a href="{{ route('admin.inbound.quality-check.show', $gr) }}" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-xs">
                           Proses QC &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-8 text-center text-gray-500">Tidak ada antrian Quality Check.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $pendingQC->links() }}</div>
    </div>
</x-app-layout>