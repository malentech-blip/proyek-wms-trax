<x-app-layout>
    <x-slot name="header">Data Supplier</x-slot>

    @livewire('super-admin.master-data.supplier-form')

    <div class="space-y-6" @supplier-saved.window="location.reload()">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Data Supplier</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua data supplier untuk kebutuhan inbound.</p>
            </div>
            <button @click="$dispatch('openSupplierModal')" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                + Tambah Supplier
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tabel Supplier</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Nama Supplier</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Alamat</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Telepon</th>
                            <th class="p-4 text-left font-semibold text-gray-600">PIC</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="p-4 font-medium">{{ $supplier->name }}</td>
                            <td class="p-4 text-gray-500">{{ $supplier->address }}</td>
                            <td class="p-4 text-gray-500">{{ $supplier->phone }}</td>
                            <td class="p-4 text-gray-500">{{ $supplier->pic }}</td>
                            <td class="p-4"><a href="#" class="text-blue-600 hover:underline">Edit</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-12 text-gray-500">Belum ada data supplier.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($suppliers->hasPages())
            <div class="p-4 border-t">{{ $suppliers->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>