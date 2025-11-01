<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penyesuaian Stok') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Penyesuaian Stok</h2>
                    <p class="text-sm text-gray-500 mt-1">Sesuaikan jumlah stok berdasarkan inventaris fisik</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        @livewire('admin.inventory.stock-adjusment-form')
                    </div>

                    {{-- Information Panel --}}
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi</h3>
                        <div class="space-y-4 text-sm text-gray-600">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Cara Kerja:</h4>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Pilih item dan lokasi</li>
                                    <li>Masukkan jumlah stok fisik</li>
                                    <li>Sistem akan menghitung selisih</li>
                                    <li>Pilih apakah memerlukan persetujuan</li>
                                </ul>
                            </div>
                            <div class="pt-4 border-t">
                                <h4 class="font-medium text-gray-900 mb-2">Catatan:</h4>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Jika memerlukan persetujuan, Super Admin akan diberitahu</li>
                                    <li>Penyesuaian yang menunggu tidak dapat mengubah inventaris</li>
                                    <li>Penyesuaian yang disetujui akan memperbarui inventaris secara otomatis</li>
                                    <li>Catatan pergerakan stok akan dibuat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
