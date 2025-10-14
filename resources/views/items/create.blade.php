<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">
            {{ __('Item Builder') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:item-builder />
        </div>
    </div>

    {{-- TAMBAHKAN KEMBALI BLOK INI --}}
    @push('scripts')
    <script>
        // Kita letakkan event listener di sini, bukan di dalam komponen Livewire
        // untuk memastikan ia selalu ada, bahkan saat Livewire me-render ulang.
        document.addEventListener('livewire:load', function () {
            
            // Fungsi ini akan dijalankan saat Livewire selesai memuat atau memperbarui
            Livewire.on('component.rendered', function () {
                // Hancurkan instance Tom Select yang mungkin sudah ada untuk mencegah duplikasi
                document.querySelectorAll('.tomselected').forEach((el) => {
                    if (el.tomselect) {
                        el.tomselect.destroy();
                    }
                });
                
                // Inisialisasi ulang semua selector
                document.querySelectorAll('select[id^="material-select-"]').forEach(function(element) {
                    const index = element.id.split('-').pop();
                    new TomSelect(element, {
                        valueField: 'value',
                        labelField: 'text',
                        searchField: 'text',
                        load: function(query, callback) {
                            if (!query.length) return callback();
                            fetch(`{{ route('api.search.materials') }}?q=${encodeURIComponent(query)}`)
                                .then(response => response.json())
                                .then(json => {
                                    callback(json);
                                }).catch(() => {
                                    callback();
                                });
                        },
                        onChange: function(value) {
                            if (value) {
                                const selectedData = this.options[value];
                                // Panggil method di komponen Livewire
                                Livewire.emit('materialSelected', index, selectedData);
                            }
                        }
                    });
                });
            });

            // Trigger event pertama kali saat halaman dimuat
            Livewire.emit('component.rendered');
        });
    </script>
    @endpush
</x-app-layout>