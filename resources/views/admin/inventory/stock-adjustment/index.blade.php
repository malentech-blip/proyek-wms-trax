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

                {{-- Statistics Cards --}}
                @if(isset($stats))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Penyesuaian</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_adjustments']) }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Persetujuan</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['pending_approvals']) }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 truncate">Disetujui</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stats['approved_adjustments']) }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 {{ $stats['total_difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Selisih</dt>
                                <dd class="text-2xl font-semibold {{ $stats['total_difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($stats['total_difference'] >= 0 ? '+' : '') . number_format($stats['total_difference']) }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Stock Adjustment Form --}}
                        @livewire('admin.inventory.stock-adjusment-form')

                        {{-- Recent Adjustments Table --}}
                        @if(isset($recentAdjustments) && $recentAdjustments->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Penyesuaian Terbaru</h3>
                                <a href="#" onclick="showAllAdjustments()" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selisih</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentAdjustments as $adjustment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $adjustment->item->item_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $adjustment->item->item_code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $adjustment->location->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm">
                                                    <div class="font-medium {{ $adjustment->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ ($adjustment->difference >= 0 ? '+' : '') . number_format($adjustment->difference) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        Sistem: {{ number_format($adjustment->system_quantity) }} |
                                                        Fisik: {{ number_format($adjustment->physical_quantity) }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($adjustment->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($adjustment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($adjustment->status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($adjustment->status) }}
                                                </span>
                                                @if($adjustment->requires_approval && $adjustment->status === 'pending')
                                                <div class="mt-1">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Perlu Persetujuan
                                                    </span>
                                                </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div>{{ $adjustment->created_at->format('d/m/Y') }}</div>
                                                <div class="text-xs">{{ $adjustment->created_at->format('H:i') }}</div>
                                                @if($adjustment->adjustedBy)
                                                <div class="text-xs text-gray-400">Oleh: {{ $adjustment->adjustedBy->name }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Information and Alerts Panel --}}
                    <div class="space-y-6">
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

                        {{-- Pending Approvals Alert --}}
                        @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-yellow-800">Menunggu Persetujuan</h3>
                                        @can('approve_stock_adjustments')
                                        <button type="button" onclick="showApprovalModal()"
                                            class="text-sm bg-yellow-600 text-white px-3 py-1 rounded-md hover:bg-yellow-700">
                                            Kelola Persetujuan
                                        </button>
                                        @endcan
                                    </div>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>{{ $pendingApprovals->count() }} penyesuaian menunggu persetujuan.</p>
                                        <div class="mt-2 space-y-2">
                                            @foreach($pendingApprovals as $pending)
                                            <div class="bg-white p-2 rounded border">
                                                <div class="font-medium text-gray-900">{{ $pending->item->item_name }}</div>
                                                <div class="text-xs text-gray-600">
                                                    Lokasi: {{ $pending->location->name }} |
                                                    Sistem: {{ number_format($pending->system_quantity) }} |
                                                    Fisik: {{ number_format($pending->physical_quantity) }} |
                                                    Selisih: <span class="{{ $pending->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ ($pending->difference >= 0 ? '+' : '') . number_format($pending->difference) }}
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Oleh: {{ $pending->adjustedBy->name }} |
                                                    {{ $pending->created_at->diffForHumans() }}
                                                </div>
                                                @if($pending->reason)
                                                <div class="text-xs text-gray-500 italic">Alasan: {{ $pending->reason }}</div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Significant Differences Alert --}}
                        @if(isset($significantDifferences) && $significantDifferences->count() > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.84L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.84l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Selisih Signifikan</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>Item dengan selisih besar yang telah disetujui:</p>
                                        <div class="mt-2 space-y-1">
                                            @foreach($significantDifferences->take(3) as $diff)
                                            <div class="text-xs">
                                                • {{ $diff->item->item_name }} -
                                                <span class="{{ $diff->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ ($diff->difference >= 0 ? '+' : '') . number_format($diff->difference) }}
                                                </span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Most Adjusted Items --}}
                        @if(isset($stats) && isset($stats['most_adjusted_items']) && $stats['most_adjusted_items']->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Paling Sering Disesuaikan</h3>
                            <div class="space-y-3">
                                @foreach($stats['most_adjusted_items'] as $item)
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $item->item->item_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->adjustment_count }} kali penyesuaian
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ number_format($item->total_difference) }}
                                        </p>
                                        <p class="text-xs text-gray-500">total selisih</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
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

        // Show all adjustments function
        function showAllAdjustments() {
            // This could open a modal or redirect to a full list page
            // For now, we'll show an alert
            alert('Fitur untuk melihat semua penyesuaian akan segera hadir. Gunakan menu laporan untuk melihat data lengkap.');
        }

        // Approval Modal Functions
        function showApprovalModal() {
            const modal = document.getElementById('approval-modal');
            const content = document.getElementById('approval-content');

            modal.classList.remove('hidden');
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-500">Memuat data persetujuan...</p>
                </div>
            `;

            // Load pending approvals
            loadPendingApprovals();
        }

        function closeApprovalModal() {
            const modal = document.getElementById('approval-modal');
            modal.classList.add('hidden');
        }

        async function loadPendingApprovals() {
            try {
                const response = await fetch('{{ route("admin.inventory.stock-adjustment.pending-approvals") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (result.success) {
                    renderPendingApprovals(result.data.data);
                } else {
                    document.getElementById('approval-content').innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            <p>Gagal memuat data persetujuan</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading pending approvals:', error);
                document.getElementById('approval-content').innerHTML = `
                    <div class="text-center py-8 text-red-600">
                        <p>Terjadi kesalahan saat memuat data</p>
                    </div>
                `;
            }
        }

        function renderPendingApprovals(adjustments) {
            const content = document.getElementById('approval-content');

            if (!adjustments || adjustments.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Tidak ada penyesuaian yang menunggu persetujuan</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="space-y-4">';

            adjustments.forEach(adjustment => {
                const differenceClass = adjustment.difference >= 0 ? 'text-green-600' : 'text-red-600';
                const differenceSign = adjustment.difference >= 0 ? '+' : '';

                html += `
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">${adjustment.item.item_name}</h4>
                                <p class="text-sm text-gray-600">${adjustment.item.item_code} • ${adjustment.location.name}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Selisih</div>
                                <div class="text-lg font-semibold ${differenceClass}">
                                    ${differenceSign}${adjustment.difference.toLocaleString()}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-3 text-sm">
                            <div>
                                <span class="text-gray-500">Jumlah Sistem:</span>
                                <span class="font-medium">${adjustment.system_quantity.toLocaleString()}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Jumlah Fisik:</span>
                                <span class="font-medium">${adjustment.physical_quantity.toLocaleString()}</span>
                            </div>
                        </div>

                        ${adjustment.reason ? `
                            <div class="mb-3">
                                <span class="text-sm text-gray-500">Alasan:</span>
                                <p class="text-sm text-gray-700 italic">${adjustment.reason}</p>
                            </div>
                        ` : ''}

                        <div class="mb-3">
                            <span class="text-sm text-gray-500">Diajukan oleh:</span>
                            <span class="text-sm font-medium">${adjustment.adjusted_by.name}</span>
                            <span class="text-sm text-gray-500">• ${new Date(adjustment.created_at).toLocaleDateString('id-ID')}</span>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="approveAdjustment(${adjustment.id})"
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm font-medium">
                                Setujui
                            </button>
                            <button onclick="rejectAdjustment(${adjustment.id})"
                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm font-medium">
                                Tolak
                            </button>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            content.innerHTML = html;
        }

        async function approveAdjustment(adjustmentId) {
            if (!confirm('Apakah Anda yakin ingin menyetujui penyesuaian stok ini?')) {
                return;
            }

            const notes = prompt('Catatan persetujuan (opsional):', '');

            try {
                const response = await fetch(`{{ url('/admin/inventory/stock-adjustment') }}/${adjustmentId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ notes: notes || '' })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Penyesuaian stok berhasil disetujui');
                    loadPendingApprovals(); // Refresh the list
                    location.reload(); // Refresh the main page
                } else {
                    alert('Gagal menyetujui penyesuaian: ' + result.message);
                }
            } catch (error) {
                console.error('Error approving adjustment:', error);
                alert('Terjadi kesalahan saat menyetujui penyesuaian');
            }
        }

        async function rejectAdjustment(adjustmentId) {
            const notes = prompt('Alasan penolakan:', '');

            if (notes === null) return; // User cancelled

            try {
                const response = await fetch(`{{ url('/admin/inventory/stock-adjustment') }}/${adjustmentId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ notes: notes })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Penyesuaian stok berhasil ditolak');
                    loadPendingApprovals(); // Refresh the list
                    location.reload(); // Refresh the main page
                } else {
                    alert('Gagal menolak penyesuaian: ' + result.message);
                }
            } catch (error) {
                console.error('Error rejecting adjustment:', error);
                alert('Terjadi kesalahan saat menolak penyesuaian');
            }
        }
    </script>

    {{-- Approval Modal --}}
    <div id="approval-modal" class="hidden fixed inset-0 z-50 bg-black/80 px-4 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
                <h3 class="text-lg font-semibold">Kelola Persetujuan Penyesuaian Stok</h3>
                <button type="button" onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="approval-content">
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-500">Memuat data persetujuan...</p>
                </div>
            </div>
        </div>
    </div>

    @endpush
</x-app-layout>
