<form wire:submit.prevent="save" class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800">Penempatan Barang (Putaway)</h3>
        <p class="text-sm text-gray-500 mt-1">
            Tentukan lokasi penyimpanan untuk barang yang telah lolos Quality Check.
            Sistem akan men-generate label QR Code berdasarkan lokasi ini.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
               {{-- Bagian Table Header --}}
<thead class="bg-green-50">
    <tr>
        <th class="p-4 text-left font-semibold text-gray-600">Item Info</th>
        <th class="p-4 text-left font-semibold text-gray-600">Gudang / Zone</th>
        <th class="p-4 text-left font-semibold text-gray-600 w-1/4">Tipe Penyimpanan</th> {{-- Kolom Baru --}}
        <th class="p-4 text-left font-semibold text-gray-600 w-1/4">Detail Lokasi</th>
    </tr>
</thead>
<tbody class="divide-y">
    @forelse ($items as $itemId => $item)
    <tr wire:key="putaway-item-{{ $itemId }}">
        {{-- Info Barang --}}
        <td class="p-4">
            <div class="font-medium text-gray-800">{{ $item['item_name'] }}</div>
            <div class="text-xs text-gray-500 font-mono">{{ $item['item_code'] }}</div>
            <div class="mt-1 text-green-600 font-bold text-xs">Qty: {{ $item['qty_to_store'] }}</div>
        </td>
        
        {{-- Pilih Gudang (Wajib) --}}
        <td class="p-4 align-top">
            <select wire:model="items.{{ $itemId }}.location_id" class="w-full border-gray-300 rounded-md text-sm">
                <option value="">-- Pilih Gudang --</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            @error("items.{$itemId}.location_id") <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
        </td>

        {{-- Pilih Tipe (Rak vs Pallet) --}}
        <td class="p-4 align-top">
            <div class="flex space-x-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="items.{{ $itemId }}.storage_type" value="rack" class="text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Rak</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="items.{{ $itemId }}.storage_type" value="pallet" class="text-green-600 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700">Pallet</span>
                </label>
            </div>
        </td>

        {{-- Dropdown Dinamis (Muncul sesuai pilihan Tipe) --}}
        <td class="p-4 align-top">
            @if($items[$itemId]['storage_type'] === 'rack')
                {{-- TAMPILKAN DROPDOWN RAK --}}
                <select wire:model="items.{{ $itemId }}.rack_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-green-500">
                    <option value="">-- Pilih Rak --</option>
                    @foreach($racks as $rack)
                        <option value="{{ $rack->id }}">{{ $rack->code }}</option>
                    @endforeach
                </select>
                @error("items.{$itemId}.rack_id") <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
            
            @elseif($items[$itemId]['storage_type'] === 'pallet')
                {{-- TAMPILKAN DROPDOWN PALLET --}}
                <select wire:model="items.{{ $itemId }}.pallet_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-green-500">
                    <option value="">-- Pilih Pallet --</option>
                    @foreach($pallets as $pallet)
                        <option value="{{ $pallet->id }}">{{ $pallet->code }}</option>
                    @endforeach
                </select>
                @error("items.{$itemId}.pallet_id") <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="4" class="text-center p-8 text-gray-500">Data kosong.</td></tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
        <p class="font-bold">Gagal Menyimpan:</p>
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    @if(count($items) > 0)
    <div class="flex justify-end">
        <button type="submit" class="bg-green-600 text-white font-semibold py-2 px-6 rounded-lg text-sm hover:bg-green-700 transition duration-200">
            Simpan & Generate Label PDF
        </button>
    </div>
    @endif
</form>

@script
<script>
    $wire.on('putaway-completed', (data) => {
        // Data dikirim dalam array, akses elemen pertama
        const payload = data[0]; 

        // 1. Buka Label PDF di Tab Baru
        window.open(payload.printUrl, '_blank');

        // 2. Redirect halaman saat ini ke Daftar PO (dengan sedikit delay agar user sadar)
        setTimeout(() => {
            window.location.href = payload.redirectUrl;
        }, 500);
    });
</script>
@endscript