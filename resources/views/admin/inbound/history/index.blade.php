<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Inbound') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">No. Penerimaan</th>
                                    <th class="px-6 py-3">No. PO</th>
                                    <th class="px-6 py-3">Tanggal Terima</th>
                                    <th class="px-6 py-3">Penerima</th>
                                    <th class="px-6 py-3">Total Item</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $record)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $record->receipt_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $record->po_number }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($record->receipt_date)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $record->receivedBy->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $record->items->count() }} Jenis Barang
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.inbound.history.show', $record) }}" class="font-medium text-blue-600 hover:underline">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">Belum ada riwayat inbound.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $history->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>