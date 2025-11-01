<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Total Stock KPI --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Stock</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalStock) }}</p>
                                <p class="text-xs text-gray-400 mt-1">Total jumlah stok semua item</p>
                            </div>
                            <div class="bg-blue-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Low Stock Alert KPI --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Low Stock Alert</p>
                                <p class="text-3xl font-bold text-{{ $lowStockAlert > 0 ? 'red' : 'green' }}-600 mt-2">{{ $lowStockAlert }}</p>
                                <p class="text-xs text-gray-400 mt-1">Item dengan stok ≤ 10 unit</p>
                            </div>
                            <div class="bg-{{ $lowStockAlert > 0 ? 'red' : 'green' }}-100 rounded-full p-4">
                                <svg class="w-8 h-8 text-{{ $lowStockAlert > 0 ? 'red' : 'green' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart and QR Scan Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Stock Value per Category Chart --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock Value per Category (%)</h3>
                        <div class="flex flex-col md:flex-row gap-4 items-center md:items-start">
                            <div class="flex-shrink-0" style="width: 300px; height: 300px;">
                                <canvas id="stockValueChart"></canvas>
                            </div>
                            <div id="chart-legend" class="flex-1 flex flex-col gap-2 mt-2">
                                {{-- Legend will be rendered here --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Scan QR --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Scan QR</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Scan atau masukkan QR Code
                                </label>
                                <div class="flex gap-2">
                                    <input type="text"
                                        id="qr_code"
                                        name="qr_code"
                                        placeholder="Scan QR code atau ketik manual"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button"
                                        onclick="startCamera()"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Camera
                                    </button>
                                </div>
                                <button type="button"
                                    onclick="scanQR()"
                                    class="mt-3 w-full px-4 py-2 bg-[#624bff] text-white rounded-md hover:bg-[#5333ff] focus:outline-none focus:ring-2 focus:ring-[#624bff]">
                                    Cari Item
                                </button>
                            </div>

                            {{-- QR Scan Result --}}
                            <div id="qr-result" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900">Hasil Scan</h4>
                                    <button type="button" onclick="closeResult()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div id="qr-result-content" class="text-sm text-gray-600">
                                    {{-- Result content will be populated here --}}
                                </div>
                            </div>

                            {{-- Loading State --}}
                            <div id="qr-loading" class="hidden mt-4 text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p class="mt-2 text-sm text-gray-500">Mencari item...</p>
                            </div>

                            {{-- Error State --}}
                            <div id="qr-error" class="hidden mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                <p id="qr-error-message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Camera Modal for QR Scanning --}}
    <div id="camera-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Scan QR Code</h3>
                <button type="button" onclick="closeCamera()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <video id="camera-video" class="w-full rounded-lg" autoplay playsinline></video>
            <p class="text-sm text-gray-500 mt-4 text-center">Arahkan kamera ke QR code</p>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js"></script>
    <script>
        // Chart.js configuration
        const chartData = @json($chartData);
        
        const ctx = document.getElementById('stockValueChart').getContext('2d');
        const stockValueChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.categories,
                datasets: [{
                    label: 'Stock Percentage (%)',
                    data: chartData.percentages,
                    backgroundColor: [
                        'rgba(98, 75, 255, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                    ],
                    borderColor: [
                        'rgba(98, 75, 255, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const percentage = context.parsed || 0;
                                const index = context.dataIndex;
                                const totalValue = chartData.values[index];
                                return `${label}: ${percentage}% (${totalValue.toLocaleString()} units)`;
                            }
                        }
                    }
                }
            }
        });

        // Render custom legend
        function renderCustomLegend() {
            const legendContainer = document.getElementById('chart-legend');
            if (!legendContainer || !chartData.categories) return;

            legendContainer.innerHTML = '';
            
            chartData.categories.forEach((category, index) => {
                const percentage = chartData.percentages[index];
                const value = chartData.values[index];
                
                const legendItem = document.createElement('div');
                legendItem.className = 'flex items-center gap-3 p-2 rounded hover:bg-gray-50';
                legendItem.innerHTML = `
                    <div class="w-4 h-4 rounded-full" style="background-color: ${stockValueChart.data.datasets[0].backgroundColor[index]}; border: 2px solid ${stockValueChart.data.datasets[0].borderColor[index]};"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${category}</div>
                        <div class="text-sm text-gray-600">${percentage}% (${value.toLocaleString()} units)</div>
                    </div>
                `;
                legendContainer.appendChild(legendItem);
            });
        }

        // Render legend after chart is created
        renderCustomLegend();

        // QR Scanner functions
        let qrScanner = null;
        let stream = null;

        function startCamera() {
            const modal = document.getElementById('camera-modal');
            const video = document.getElementById('camera-video');
            
            modal.classList.remove('hidden');

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    video.srcObject = mediaStream;
                    
                    QrScanner.hasCamera().then(hasCamera => {
                        if (hasCamera) {
                            qrScanner = new QrScanner(
                                video,
                                result => {
                                    document.getElementById('qr_code').value = result.data;
                                    closeCamera();
                                    scanQR();
                                },
                                {
                                    returnDetailedScanResult: true,
                                    highlightScanRegion: true
                                }
                            );
                            qrScanner.start();
                        }
                    });
                })
                .catch(function(err) {
                    console.error('Error accessing camera:', err);
                    alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
                });
        }

        function closeCamera() {
            const modal = document.getElementById('camera-modal');
            
            if (qrScanner) {
                qrScanner.stop();
                qrScanner.destroy();
                qrScanner = null;
            }
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            modal.classList.add('hidden');
            modal.style.display = 'none';
        }

        function scanQR() {
            const qrCode = document.getElementById('qr_code').value.trim();
            
            if (!qrCode) {
                alert('Masukkan QR code terlebih dahulu');
                return;
            }

            const resultDiv = document.getElementById('qr-result');
            const loadingDiv = document.getElementById('qr-loading');
            const errorDiv = document.getElementById('qr-error');
            const resultContent = document.getElementById('qr-result-content');

            // Hide previous results
            resultDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            loadingDiv.classList.remove('hidden');

            fetch('{{ route("admin.inventory.dashboard.scan-qr") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: qrCode })
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.classList.add('hidden');
                
                if (data.success) {
                    const item = data.data;
                    resultContent.innerHTML = `
                        <div class="space-y-2">
                            <div><span class="font-medium">Tipe:</span> ${item.type}</div>
                            <div><span class="font-medium">Kode Item:</span> ${item.item_code}</div>
                            <div><span class="font-medium">Nama Item:</span> ${item.item_name}</div>
                            <div><span class="font-medium">Quantity:</span> ${item.quantity}</div>
                            ${item.batch_no ? `<div><span class="font-medium">Batch:</span> ${item.batch_no}</div>` : ''}
                            <div class="pt-2 border-t">
                                <div><span class="font-medium">Lokasi:</span> ${item.location}</div>
                                <div><span class="font-medium">Rak:</span> ${item.rack}</div>
                                <div><span class="font-medium">Pallet:</span> ${item.pallet}</div>
                            </div>
                            <div class="pt-2"><span class="font-medium">Status:</span> <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">${item.status}</span></div>
                        </div>
                    `;
                    resultDiv.classList.remove('hidden');
                } else {
                    document.getElementById('qr-error-message').textContent = data.message || 'Item tidak ditemukan';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                loadingDiv.classList.add('hidden');
                document.getElementById('qr-error-message').textContent = 'Terjadi kesalahan saat mencari item';
                errorDiv.classList.remove('hidden');
                console.error('Error:', error);
            });
        }

        function closeResult() {
            document.getElementById('qr-result').classList.add('hidden');
            document.getElementById('qr_code').value = '';
        }

        // Allow Enter key to trigger scan
        document.getElementById('qr_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                scanQR();
            }
        });
    </script>
    @endpush
</x-app-layout>

