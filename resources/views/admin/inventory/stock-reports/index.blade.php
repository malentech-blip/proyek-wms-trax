<x-app-layout>
    <x-slot name="header">
        Stock Reports
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Stock Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Tabel stock barang jadi.</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div>
            </div>
            <div class="flex items-center space-x-4">

            <button id="sync-accurate-btn"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center">
                <span id="sync-text">Sinkronkan ke Accurate</span>
                <span id="sync-loading" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            <a href="{{ route('admin.inventory.stock-reports.export') }}"
                class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg text-sm flex items-center">
                Print Stock Report (Excel)
            </a>
            </div>
        </div>
        {{-- Using livewire for handling datatable and filtering --}}
        @livewire('admin.inventory.stock-report-table')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const syncBtn = document.getElementById('sync-accurate-btn');
            const syncText = document.getElementById('sync-text');
            const syncLoading = document.getElementById('sync-loading');

            syncBtn.addEventListener('click', async function(e) {
                e.preventDefault();

                // Show confirmation dialog
                const result = await Swal.fire({
                    title: 'Konfirmasi Sinkronisasi',
                    text: 'Apakah Anda yakin ingin menyinkronkan inventory dengan Accurate? Proses ini akan mengupdate data inventory berdasarkan data Accurate.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Sinkronkan',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) {
                    return;
                }

                // Show loading state
                syncBtn.disabled = true;
                syncText.textContent = 'Menyinkronkan...';
                syncLoading.classList.remove('hidden');

                try {
                    // TEMPORARY: Mock successful sync response for demonstration
                    console.log('🔄 Syncing with Accurate (mocked response)...');

                    // Simulate API delay
                    await new Promise(resolve => setTimeout(resolve, 2000));

                    // Mock successful sync response
                    const data = {
                        success: true,
                        message: 'Sinkronisasi inventory dengan Accurate berhasil.',
                        results: {
                            synced_count: 25,
                            skipped_count: 3,
                            logs: [
                                '📊 Found 28 raw materials in Accurate',
                                '🏢 Using default location: Main Warehouse',
                                '🆕 Created inventory: RM-001 - stock: 150',
                                '📝 Updated inventory: RM-002 - 45 → 67',
                                '⏭️ Would skip (no change): RM-003 - stock: 200',
                                '🆕 Created inventory: RM-004 - stock: 89',
                                '📝 Updated inventory: RM-005 - 120 → 145',
                                '✅ Transaction committed successfully',
                                '📈 Sync Summary: 25 synced, 3 skipped'
                            ]
                        }
                    };

                    if (data.success) {
                        // Show success message with detailed results
                        let resultsHtml = '<div class="text-left">';
                        resultsHtml += '<div class="mb-2"><strong>Ringkasan Sinkronisasi:</strong></div>';
                        resultsHtml += `<div>✅ Berhasil: ${data.results.synced_count} item</div>`;
                        resultsHtml += `<div>⏭️ Dilewati: ${data.results.skipped_count} item</div>`;

                        if (data.results.logs && data.results.logs.length > 0) {
                            resultsHtml += '<div class="mt-3"><strong>Detail:</strong></div>';
                            resultsHtml += '<div class="max-h-40 overflow-y-auto text-xs">';
                            data.results.logs.forEach(log => {
                                resultsHtml += `<div class="mb-1">${log}</div>`;
                            });
                            resultsHtml += '</div>';
                        }
                        resultsHtml += '</div>';

                        await Swal.fire({
                            title: 'Sinkronisasi Berhasil!',
                            html: resultsHtml,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            confirmButtonText: 'OK'
                        });

                        // Refresh the page to show updated data
                        window.location.reload();
                    } else {
                        // Show error message
                        let errorHtml = '<div class="text-left">';
                        errorHtml += '<div class="mb-2"><strong>Kesalahan:</strong></div>';

                        if (data.results && data.results.errors && data.results.errors.length > 0) {
                            data.results.errors.forEach(error => {
                                errorHtml += `<div class="mb-1 text-red-600">❌ ${error}</div>`;
                            });
                        }

                        if (data.results && data.results.logs && data.results.logs.length > 0) {
                            errorHtml += '<div class="mt-3"><strong>Log:</strong></div>';
                            errorHtml += '<div class="max-h-40 overflow-y-auto text-xs">';
                            data.results.logs.forEach(log => {
                                errorHtml += `<div class="mb-1">${log}</div>`;
                            });
                            errorHtml += '</div>';
                        }
                        errorHtml += '</div>';

                        await Swal.fire({
                            title: 'Sinkronisasi Gagal',
                            html: errorHtml,
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                            confirmButtonText: 'OK'
                        });
                    }

                } catch (error) {
                    console.error('Sync error:', error);
                    await Swal.fire({
                        title: 'Terjadi Kesalahan',
                        text: 'Tidak dapat terhubung ke server. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    // Reset loading state
                    syncBtn.disabled = false;
                    syncText.textContent = 'Sinkronkan ke Accurate';
                    syncLoading.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
