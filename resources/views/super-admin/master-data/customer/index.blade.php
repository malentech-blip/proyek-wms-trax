<x-app-layout>
    <x-slot name="header">Data Customer</x-slot>

    @livewire('super-admin.master-data.customer-form')

    <div class="space-y-6" @customer-saved.window="location.reload()">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold">Data Customer</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola semua data customer untuk kebutuhan outbound.</p>
            </div>
            <button @click="$dispatch('openCustomerModal')" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                + Tambah Customer
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold">Tabel Customer</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600">Nama Customer</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Alamat</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Telepon</th>
                            <th class="p-4 text-left font-semibold text-gray-600">PIC</th>
                            <th class="p-4 text-left font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($customers as $customer)
                        <tr>
                            <td class="p-4 font-medium">{{ $customer->name }}</td>
                            <td class="p-4 text-gray-500">{{ $customer->address }}</td>
                            <td class="p-4 text-gray-500">{{ $customer->phone }}</td>
                            <td class="p-4 text-gray-500">{{ $customer->pic }}</td>
                            <td class="p-4"><a href="#" class="text-blue-600 hover:underline">Edit</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-12 text-gray-500">Belum ada data customer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
            <div class="p-4 border-t">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>