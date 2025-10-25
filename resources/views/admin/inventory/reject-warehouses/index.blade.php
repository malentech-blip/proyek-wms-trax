<x-app-layout>
    <x-slot name="header">
        Reject Warehouses
    </x-slot>

    <div class="space-y-6" @user-saved.window="location.reload()">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Reject Warehouses</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel Produk Yang Ditolak.</p>
            </div>
        </div>

        <div class="flex justify-between items-center end-0">
            <div class="flex items-center space-x-4">
                <select class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Filter: Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
                <input type="text" name="search" placeholder="Cari Nama Produk"
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-600">
                        <tr>
                            <th class="p-4 text-left font-semibold text-white">Kode Item</th>
                            <th class="p-4 text-left font-semibold text-white">Nama Item</th>
                            <th class="p-4 text-left font-semibold text-white">Qty</th>
                            <th class="p-4 text-left font-semibold text-white">Alasan</th>
                            <th class="p-4 text-left font-semibold text-white">Asal</th>
                            <th class="p-4 text-left font-semibold text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        {{-- @forelse ($users as $user)
                        <tr>
                            <td class="p-4 text-gray-700 font-medium">{{ $user->name }}</td>
                            <td class="p-4 text-gray-500">{{ $user->email }}</td>
                            <td class="p-4 text-gray-500">{{ $user->getRoleNames()->first() ?? 'N/A' }}</td>
                            <td class="p-4">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Aktif</span>
                            </td>
                            <td class="p-4 text-gray-500">{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="p-4">
                                <a href="#" class="text-blue-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-12 text-gray-500">
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>
                        @endforelse --}}
                    </tbody>
                </table>
            </div>
            {{-- @if($users->hasPages())
            <div class="p-4 border-t">
                {{ $users->links() }}
            </div>
            @endif --}}
        </div>
    </div>
</x-app-layout>
