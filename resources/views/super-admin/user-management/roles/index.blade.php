<x-app-layout>
    <x-slot name="header">
        Roles
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Roles</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola peran dan izin untuk setiap pengguna dalam sistem.</p>
            </div>
            {{-- Tombol ini bisa kita fungsikan nanti --}}
            {{-- <a href="#" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center">
                + Tambah Role Baru
            </a> --}}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($roles as $role)
            <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                <h3 class="text-lg font-bold text-gray-800">{{ $role->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">Total users with this role:</p>
                
                <ul class="mt-4 space-y-2 text-sm text-gray-600 flex-grow">
                    @forelse ($role->permissions->take(5) as $permission)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                        </li>
                    @empty
                        <li class="text-gray-400 italic">No permissions assigned.</li>
                    @endforelse
                    @if($role->permissions->count() > 5)
                        <li class="text-gray-400 text-xs pl-6">...dan {{ $role->permissions->count() - 5 }} lainnya.</li>
                    @endif
                </ul>
                
                <div class="mt-6 pt-4 border-t flex space-x-2">
                    <a href="#" class="text-center w-full bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        View Role
                    </a>
                    <a href="#" class="text-center w-full bg-gray-200 border border-transparent text-gray-800 font-semibold py-2 px-4 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                        Edit Role
                    </a>
                </div>
            </div>
            @endforeach

            <div class="bg-white rounded-xl border-2 border-dashed p-6 flex flex-col items-center justify-center text-center hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                    <img src="https://img.icons8.com/pastel-glyph/64/a0aec0/clipboard--v1.png" alt="clipboard icon"/>
                </div>
                <h3 class="font-semibold text-gray-800">ADD NEW ROLES</h3>
                <a href="#" class="mt-4 w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700">
                    Tambah Role
                </a>
            </div>

        </div>
    </div>
</x-app-layout>