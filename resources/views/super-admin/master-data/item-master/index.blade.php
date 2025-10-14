<x-app-layout>
    <x-slot name="header">Item Master</x-slot>

    @livewire('super-admin.master-data.item-form')

    <div class="space-y-6" @item-saved.window="location.reload()">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Item Master</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua data item bahan baku dan barang jadi.</p>
            </div>
            <button @click="$dispatch('openItemModal')" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center">
                + Tambah Item
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tabel Data Barang</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Kode Item</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Nama Item</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Kategori</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Satuan</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($items as $item)
                        <tr>
                            <td class="p-4 text-gray-700">{{ $item->item_code }}</td>
                            <td class="p-4 font-medium">{{ $item->item_name }}</td>
                            <td class="p-4 text-gray-500">{{ $item->item_type }}</td>
                            <td class="p-4 text-gray-500">{{ $item->uom }}</td>
                            <td class="p-4">
                                <span class="{{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs px-2.5 py-0.5 rounded-full">
                                    {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="p-4"><a href="#" class="text-blue-600 hover:underline">Edit</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center p-12 text-gray-500">Belum ada data item. Klik "Tambah Item" untuk memulai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($items->hasPages())
            <div class="p-4 border-t">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>