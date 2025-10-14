<form wire:submit.prevent="save" class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800">Pilih Lokasi Penyimpanan</h3>
        <p class="text-sm text-gray-500 mt-1">Pilih rak dan pallet untuk setiap item yang lolos Quality Check.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-blue-50">
                    <tr>
                        <th class="p-4 text-left font-semibold text-gray-600">Nama Item</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Qty Lolos QC</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Lokasi</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Rak</th>
                        <th class="p-4 text-left font-semibold text-gray-600">Pallet</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($items as $itemId => $item)
                    <tr wire:key="putaway-item-{{ $itemId }}">
                        <td class="p-4 text-gray-700 font-medium">{{ $item['item_name'] }}</td>
                        <td class="p-4 text-gray-500">{{ $item['passed_qty'] }}</td>
                        <td class="p-4">
                            <select wire:model="items.{{ $itemId }}.location_id" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">Pilih Lokasi</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-4">
                            <select wire:model="items.{{ $itemId }}.rack_id" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">Pilih Rak</option>
                                @foreach($racks as $rack)
                                    <option value="{{ $rack->id }}">{{ $rack->code }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-4">
                            <select wire:model="items.{{ $itemId }}.pallet_id" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">Pilih Pallet</option>
                                @foreach($pallets as $pallet)
                                    <option value="{{ $pallet->id }}">{{ $pallet->code }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center p-12">Tidak ada item untuk disimpan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg text-sm hover:bg-blue-700">
            Simpan Lokasi & Generate Label
        </button>
    </div>
</form>