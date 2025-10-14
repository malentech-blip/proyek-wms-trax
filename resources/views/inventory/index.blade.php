<x-app-layout>
    {{-- DIUBAH: Header dibuat responsif, item akan bertumpuk di layar kecil --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center w-full gap-4">
            <div class="flex items-center space-x-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Inventory') }}
                </h2>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-4 self-end sm:self-center">
                {{-- DIUBAH: Dibuat lebih kecil dan disembunyikan pada layar sangat kecil untuk menghemat ruang --}}
                <div class="relative hidden sm:block">
                    <button class="flex items-center space-x-2 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors duration-200">
                        <span>{{ Auth::user()->name }}</span>
                    </button>
                </div>
                <button class="relative text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.405L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">1</span>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center">
                                <span class="text-sm font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                        <x-dropdown-link href="{{ route('profile.edit') }}">{{ __('Profile') }}</x-dropdown-link>
                        <div class="border-t border-gray-200"></div>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </x-slot>
    
    {{-- Breadcrumb untuk Halaman Inventory --}}
    <x-slot name="breadcrumb">
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
            <span class="ml-2 text-sm font-medium text-gray-500">Inventory</span>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    {{-- DITAMBAHKAN: State Alpine.js untuk mengontrol animasi saat halaman dimuat --}}
    <div 
        class="py-8"
        x-data="{ pageLoaded: false }"
        x-init="setTimeout(() => pageLoaded = true, 100)"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- DIUBAH: Judul dan tombol dibuat responsif dan diberi animasi --}}
            <div 
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 transition-all duration-500 ease-out"
                :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <h2 class="text-2xl font-semibold text-gray-800">Inventory Summary</h2>
                @can('add_new_product')
                <a href="{{ route('items.create') }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add a New Product
                </a>
                @endcan
            </div>

            {{-- DITAMBAHKAN: Wrapper untuk komponen Livewire agar bisa diberi animasi --}}
            <div
                class="transition-all duration-500 ease-out"
                style="transition-delay: 150ms"
                :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                {{-- Komponen Livewire untuk Daftar Item dipanggil di sini --}}
                @livewire('item-list')
            </div>
            
        </div>
    </div>
</x-app-layout>