<x-app-layout>
    {{-- DIUBAH: Header dibuat responsif, item akan bertumpuk di layar kecil --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center w-full gap-4">
            <div class="flex items-center space-x-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Raw Materials') }}
                </h2>
            </div>

        </div>
    </x-slot>

    {{-- Breadcrumb untuk Halaman Inventory --}}
    <x-slot name="breadcrumb">
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                    clip-rule="evenodd" />
            </svg>
            <span class="ml-2 text-sm font-medium text-gray-500">Raw Materials</span>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    {{-- DITAMBAHKAN: State Alpine.js untuk mengontrol animasi saat halaman dimuat --}}
    <div class="py-8" x-data="{ pageLoaded: false }" x-init="setTimeout(() => pageLoaded = true, 100)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- DIUBAH: Judul dan tombol dibuat responsif dan diberi animasi --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 transition-all duration-500 ease-out"
                :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                <h2 class="text-2xl font-semibold text-gray-800">Raw Materials</h2>
                @can('add_new_product')
                <a href="{{ route('items.create') }}"
                    class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add a New Product
                </a>
                @endcan
            </div>

            {{-- DITAMBAHKAN: Wrapper untuk komponen Livewire agar bisa diberi animasi --}}
            <div class="transition-all duration-500 ease-out" style="transition-delay: 150ms"
                :class="pageLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                {{-- Komponen Livewire untuk Daftar Item dipanggil di sini --}}
                {{-- @livewire('item-list') --}}
            </div>

        </div>
    </div>
</x-app-layout>
