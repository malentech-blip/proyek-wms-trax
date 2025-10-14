<x-app-layout>
    <x-slot name="header">
        Template Label
    </x-slot>

    {{-- KITA AKAN MELETAKKAN KOMPONEN LIVEWIRE MODAL DI SINI NANTI --}}
    @livewire('label-template-form')

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Template Label</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola desain dan elemen untuk semua jenis label QR.</p>
            </div>
            {{-- Tombol ini akan memanggil event Livewire untuk membuka modal --}}
            <button @click="$dispatch('openLabelModal')" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Buat Template Baru
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($templates as $template)
            <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col">
                <h3 class="text-lg font-bold text-gray-800">Label Item</h3>
                
                <ul class="mt-4 space-y-2 text-sm text-gray-600 flex-grow">
                    <li class="font-semibold">Elemen Label:</li>
                    <li class="flex items-start pl-4"><span>&bull;</span> <span class="ml-2">Nama Item</span></li>
                    <li class="flex items-start pl-4"><span>&bull;</span> <span class="ml-2">Kode Item</span></li>
                    <li class="flex items-start pl-4"><span>&bull;</span> <span class="ml-2">Batch / Lot</span></li>
                    <li class="flex items-start pl-4"><span>&bull;</span> <span class="ml-2">QR Code</span></li>
                </ul>
                
                <div class="mt-6 pt-4 border-t flex space-x-2">
                    <button class="text-center w-full bg-gray-200 border border-transparent text-gray-800 font-semibold py-2 px-4 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                        Edit Template
                    </button>
                    <button class="text-center w-full bg-white border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        Preview Cetak
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>