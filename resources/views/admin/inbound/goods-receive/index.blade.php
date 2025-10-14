<x-app-layout>
    <x-slot name="header">
        Receive Goods from PO: {{ $purchaseOrder['number'] }}
    </x-slot>
    <form action="{{ route('admin.inbound.purchase-orders.receive.store', ['poId' => $purchaseOrder['id']]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-6">
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Nomor PO</dt>
                        <dd class="mt-1 font-semibold text-gray-800">{{ $purchaseOrder['number'] }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Supplier</dt>
                        <dd class="mt-1 font-semibold text-gray-800">{{ $purchaseOrder['vendor']['name'] }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Tanggal PO</dt>
                        <dd class="mt-1 font-semibold text-gray-800">{{ \Carbon\Carbon::parse($purchaseOrder['transDate'])->format('d F Y') }}</dd>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Detail Item Diterima</h3>
                    <p class="text-sm text-gray-500 mt-1">Masukkan jumlah barang yang diterima secara fisik.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-blue-50">
                            <tr>
                                <th class="p-4 text-left font-semibold text-gray-600 w-1/2">Nama Item</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Qty Dipesan</th>
                                <th class="p-4 text-left font-semibold text-gray-600">Qty Diterima</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($purchaseOrder['detailItem'] as $item)
                            <tr>
                                <td class="p-4 text-gray-700 font-medium">{{ $item['item']['name'] }}</td>
                                <td class="p-4 text-gray-500">{{ (int)$item['quantity'] }} {{ $item['itemUnit']['name'] }}</td>
                                <td class="p-4">
                                    {{-- PASTIKAN SEMUA INPUT INI ADA --}}
                                    <input type="number" name="items[{{ $item['item']['id'] }}][received_qty]" class="w-24 border-gray-300 rounded-md shadow-sm text-sm" value="{{ (int)$item['quantity'] }}">
                                    
                                    <input type="hidden" name="items[{{ $item['item']['id'] }}][item_name]" value="{{ $item['item']['name'] }}">
                                    <input type="hidden" name="items[{{ $item['item']['id'] }}][item_code]" value="{{ $item['item']['no'] }}">
                                    <input type="hidden" name="items[{{ $item['item']['id'] }}][expected_qty]" value="{{ (int)$item['quantity'] }}">
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center p-12 text-gray-500">Tidak ada item di dalam Purchase Order ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Tambahan</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Penerimaan</label>
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                    </div>
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Upload Bukti Foto</label>
                        <input type="file" id="photo" name="photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>


            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                    Submit & Lanjut ke Quality Check
                </button>
            </div>
        </div>
    </form>
</x-app-layout>