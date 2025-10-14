<x-app-layout>
    <x-slot name="header">
        Permissions
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">User Management</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola Akses Pengguna dan Hak Akses Antarmuka.</p>
            </div>
            {{-- Tombol ini bisa kita sembunyikan atau fungsikan nanti --}}
            <a href="#" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pengguna
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <input type="text" name="search" placeholder="Cari Modul/Fitur..." class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Tabel Permissions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="p-4 text-left font-semibold text-gray-600 w-1/4">Modul / Fitur</th>
                            @foreach($roles as $role)
                                <th class="p-4 text-center font-semibold text-gray-600">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($permissions as $group => $permissionList)
                            @foreach ($permissionList as $permission)
                                <tr>
                                    <td class="p-4 text-gray-700 font-medium">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </td>
                                    @foreach ($roles as $role)
                                        <td class="p-4 text-center">
                                            @if (in_array($permission->name, $rolePermissions[$role->id]))
                                                {{-- Tanda centang hijau jika permission ada --}}
                                                <svg class="w-6 h-6 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                {{-- Tanda silang merah jika tidak ada --}}
                                                <svg class="w-6 h-6 text-red-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
