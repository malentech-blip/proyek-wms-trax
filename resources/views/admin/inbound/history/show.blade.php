<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Penerimaan: {{ $goodsReceipt->receipt_number }}
            </h2>
            <a href="{{ route('admin.inbound.history.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">No. PO</p>
                        <p class="font-bold text-gray-800">{{ $goodsReceipt->po_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Tanggal Terima</p>
                        <p class="font-bold text-gray-800">{{ $goodsReceipt->receipt_date }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Penerima</p>
                        <p class="font-bold text-gray-800">{{ $goodsReceipt->receivedBy->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Status</p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($goodsReceipt->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.inbound.putaway.print-labels', $goodsReceipt) }}" target="_blank" class="bg-gray-800 text-white text-xs px-4 py-2 rounded hover:bg-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Ulang Label
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Penyimpanan Barang</h3>
                    <table class="min-w-full text-sm text-left text-gray-500">
                        <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                            <tr>
                                <th class="px-4 py-2">Nama Barang</th>
                                <th class="px-4 py-2">Kode</th>
                                <th class="px-4 py-2">Diterima</th>
                                <th class="px-4 py-2">Lolos QC</th>
                                <th class="px-4 py-2">Lokasi Simpan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($goodsReceipt->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->item_name }}</td>
                                <td class="px-4 py-3">{{ $item->item_code }}</td>
                                <td class="px-4 py-3">{{ $item->received_qty }}</td>
                                <td class="px-4 py-3 text-green-600 font-bold">{{ $item->passed_qty }}</td>
                                <td class="px-4 py-3">
                                    {{-- Loop cek label yang tersimpan --}}
                                    @foreach($item->itemLabels as $label)
                                        <div class="mb-1">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded border border-blue-200">
                                                {{ $label->location->name ?? 'Gudang' }} / {{ $label->rack->code ?? 'Rak' }}
                                            </span>
                                            <span class="text-xs text-gray-500">(Qty: {{ $label->quantity }})</span>
                                        </div>
                                    @endforeach
                                    @if($item->itemLabels->isEmpty())
                                        <span class="text-xs text-red-500 italic">Belum ditempatkan</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>