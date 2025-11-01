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

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('livewire:init', function() {
            let itemSelect2, locationSelect2;

            // Function to get Livewire component
            function getLivewireComponent() {
                const wireElement = document.querySelector('[wire\\:id]');
                if (wireElement && typeof Livewire !== 'undefined') {
                    try {
                        const wireId = wireElement.getAttribute('wire:id');
                        return Livewire.find(wireId);
                    } catch (e) {
                        console.warn('Could not find Livewire component:', e);
                        return null;
                    }
                }
                return null;
            }

            function initializeSelect2() {
                // Check if select elements exist
                if (!$('#item_id').length || !$('#location_id').length) {
                    console.warn('Select elements not found, retrying...');
                    setTimeout(initializeSelect2, 100);
                    return;
                }

                // Initialize Item Select2
                if (itemSelect2 && itemSelect2.hasClass('select2-hidden-accessible')) {
                    try {
                        itemSelect2.select2('destroy');
                    } catch (e) {
                        // Already destroyed
                    }
                }
                
                itemSelect2 = $('#item_id').select2({
                    placeholder: 'Pilih Item',
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Item tidak ditemukan";
                        }
                    }
                });

                // Initialize Location Select2 (disabled initially)
                if (locationSelect2 && locationSelect2.hasClass('select2-hidden-accessible')) {
                    try {
                        locationSelect2.select2('destroy');
                    } catch (e) {
                        // Already destroyed
                    }
                }

                locationSelect2 = $('#location_id').select2({
                    placeholder: 'Pilih Lokasi',
                    allowClear: true,
                    width: '100%',
                    disabled: true,
                    language: {
                        noResults: function() {
                            return "Lokasi tidak ditemukan";
                        }
                    }
                });

                // Handle Item selection
                itemSelect2.on('select2:select select2:clear', function(e) {
                    const selectedValue = $(this).val();
                    const livewireComponent = getLivewireComponent();
                    
                    if (livewireComponent) {
                        livewireComponent.set('itemId', selectedValue || '');
                    }
                    
                    // Disable and clear location when item changes
                    if (!selectedValue) {
                        locationSelect2.prop('disabled', true).val(null).trigger('change');
                        if (locationSelect2.data('select2')) {
                            locationSelect2.data('select2').$container.addClass('select2-container-disabled');
                        }
                    } else {
                        // Enable location select after item is selected
                        setTimeout(() => {
                            locationSelect2.prop('disabled', false);
                            if (locationSelect2.data('select2')) {
                                locationSelect2.data('select2').$container.removeClass('select2-container-disabled');
                            }
                        }, 100);
                    }
                });

                // Handle Location selection
                locationSelect2.on('select2:select select2:clear', function(e) {
                    const selectedValue = $(this).val();
                    const livewireComponent = getLivewireComponent();
                    if (livewireComponent) {
                        livewireComponent.set('locationId', selectedValue || '');
                    }
                });
            }

            // Wait for DOM and Livewire to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initializeSelect2, 100);
                });
            } else {
                setTimeout(initializeSelect2, 100);
            }

            // Reinitialize after Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                // Check if the select elements exist on the page
                if ($('#item_id').length && $('#location_id').length) {
                    setTimeout(() => {
                        // Get the Livewire component
                        const livewireComponent = getLivewireComponent();

                        // Destroy existing Select2 instances
                        if (itemSelect2 && itemSelect2.hasClass('select2-hidden-accessible')) {
                            try {
                                itemSelect2.select2('destroy');
                            } catch (e) {
                                // Select2 might already be destroyed
                            }
                        }
                        if (locationSelect2 && locationSelect2.hasClass('select2-hidden-accessible')) {
                            try {
                                locationSelect2.select2('destroy');
                            } catch (e) {
                                // Select2 might already be destroyed
                            }
                        }

                        // Reinitialize Select2
                        itemSelect2 = $('#item_id').select2({
                            placeholder: 'Pilih Item',
                            allowClear: true,
                            width: '100%',
                            language: {
                                noResults: function() {
                                    return "Item tidak ditemukan";
                                }
                            }
                        });

                        // Get current itemId value from DOM
                        const currentItemId = document.querySelector('#item_id')?.value || '';
                        const isLocationEnabled = currentItemId !== '';

                        locationSelect2 = $('#location_id').select2({
                            placeholder: 'Pilih Lokasi',
                            allowClear: true,
                            width: '100%',
                            disabled: !isLocationEnabled,
                            language: {
                                noResults: function() {
                                    return "Lokasi tidak ditemukan";
                                }
                            }
                        });

                        if (!isLocationEnabled) {
                            if (locationSelect2.data('select2')) {
                                locationSelect2.data('select2').$container.addClass('select2-container-disabled');
                            }
                        }

                        // Reattach event handlers
                        itemSelect2.off('select2:select select2:clear').on('select2:select select2:clear', function(e) {
                            const selectedValue = $(this).val();
                            if (livewireComponent) {
                                livewireComponent.set('itemId', selectedValue || '');
                            }

                            if (!selectedValue) {
                                locationSelect2.prop('disabled', true).val(null).trigger('change');
                                if (locationSelect2.data('select2')) {
                                    locationSelect2.data('select2').$container.addClass('select2-container-disabled');
                                }
                            } else {
                                setTimeout(() => {
                                    locationSelect2.prop('disabled', false);
                                    if (locationSelect2.data('select2')) {
                                        locationSelect2.data('select2').$container.removeClass('select2-container-disabled');
                                    }
                                }, 100);
                            }
                        });

                        locationSelect2.off('select2:select select2:clear').on('select2:select select2:clear', function(e) {
                            const selectedValue = $(this).val();
                            if (livewireComponent) {
                                livewireComponent.set('locationId', selectedValue || '');
                            }
                        });
                    }, 150);
                }
            });

            // Listen for form reset
            Livewire.on('form-reset', () => {
                if (itemSelect2 && itemSelect2.hasClass('select2-hidden-accessible')) {
                    itemSelect2.val(null).trigger('change');
                }
                if (locationSelect2 && locationSelect2.hasClass('select2-hidden-accessible')) {
                    locationSelect2.val(null).trigger('change');
                    locationSelect2.prop('disabled', true);
                    if (locationSelect2.data('select2')) {
                        locationSelect2.data('select2').$container.addClass('select2-container-disabled');
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
