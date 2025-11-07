<x-app-layout>
    <x-slot name="header">
        Dashboard Production
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold">Ringkasan Tugas Production</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Material Request (Requested)</h3>
                <p class="text-3xl font-bold mt-2">{{ $materialRequestsRequested }}</p> {{-- Akan kita buat dinamis --}}
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Work In Progress (Active)</h3>
                <p class="text-3xl font-bold mt-2">{{ $wipActive }}</p> {{-- Akan kita buat dinamis --}}
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-gray-500">Finished Goods (Today)</h3>
                <p class="text-3xl font-bold mt-2">{{ $finishedGoodsToday }}</p> {{-- Akan kita buat dinamis --}}
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Production Efficiency Analysis</h2>

                <!-- Period Filter -->
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700">Period:</label>
                    <select id="period-filter" onchange="updateCharts()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $period === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="last_7_days" {{ $period === 'last_7_days' ? 'selected' : '' }}>Last 7 Days
                        </option>
                        <option value="this_week" {{ $period === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="last_week" {{ $period === 'last_week' ? 'selected' : '' }}>Last Week</option>
                        <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="last_30_days" {{ $period === 'last_30_days' ? 'selected' : '' }}>Last 30 Days
                        </option>
                    </select>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading-charts" class="hidden text-center py-12">
                <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-600 mt-4 font-medium">Loading charts...</p>
            </div>

            <!-- Charts Container -->
            <div id="charts-container" class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                <!-- Efficiency Rate Chart -->
                <div class="border rounded-xl p-6 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Efficiency Rate by Batch</h3>
                    <canvas id="efficiencyRateChart"></canvas>
                </div>

                <!-- Good vs Reject Chart -->
                <div class="border rounded-xl p-6 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Good vs Reject Quantity</h3>
                    <canvas id="goodRejectChart"></canvas>
                </div>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden text-center py-12">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No production data available</p>
                <p class="text-gray-400 text-sm mt-2">Complete some production batches to see analytics</p>
            </div>
        </div>
    </div>
</x-app-layout>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initial data from backend
    let productionData = @json($productionData);
    let charts = {};

    // Initialize charts on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (productionData.length > 0) {
            initializeCharts(productionData);
        } else {
            showEmptyState();
        }
    });

    // Initialize all charts
    function initializeCharts(data) {
        document.getElementById('charts-container').classList.remove('hidden');
        document.getElementById('empty-state').classList.add('hidden');

        const batches = data.map(d => d.batch);
        const goodQty = data.map(d => d.good);
        const rejectQty = data.map(d => d.reject);
        const efficiency = data.map(d => d.efficiency);
        const rejectRate = data.map(d => d.rejectRate);
        const productionSpeed = data.map(d => d.productionSpeed);

        // Destroy existing charts if they exist
        Object.values(charts).forEach(chart => chart?.destroy());

        // Chart 1: Efficiency Rate Bar Chart
        charts.efficiencyRate = new Chart(document.getElementById('efficiencyRateChart'), {
            type: 'bar',
            data: {
                labels: batches,
                datasets: [{
                    label: 'Efficiency Rate (%)',
                    data: efficiency,
                    backgroundColor: efficiency.map(e => {
                        if (e >= 95) return '#10b981';
                        if (e >= 90) return '#f59e0b';
                        return '#ef4444';
                    }),
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => `Efficiency: ${context.parsed.y}%`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: (value) => value + '%' }
                    }
                }
            }
        });

        // Chart 2: Good vs Reject Stacked Bar
        charts.goodReject = new Chart(document.getElementById('goodRejectChart'), {
            type: 'bar',
            data: {
                labels: batches,
                datasets: [
                    {
                        label: 'Good',
                        data: goodQty,
                        backgroundColor: '#10b981',
                        borderRadius: 8,
                    },
                    {
                        label: 'Reject',
                        data: rejectQty,
                        backgroundColor: '#ef4444',
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });

        // Chart 3: Production Timeline
        charts.timeline = new Chart(document.getElementById('timelineChart'), {
            type: 'line',
            data: {
                labels: batches,
                datasets: [
                    {
                        label: 'Good Quantity',
                        data: goodQty,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Reject Quantity',
                        data: rejectQty,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Chart 4: Batch Performance Radar
        charts.comparison = new Chart(document.getElementById('batchComparisonChart'), {
            type: 'radar',
            data: {
                labels: batches,
                datasets: [{
                    label: 'Efficiency Rate',
                    data: efficiency,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: (value) => value + '%'
                        }
                    }
                }
            }
        });

        // Chart 5: Production Speed
        charts.speed = new Chart(document.getElementById('productionSpeedChart'), {
            type: 'bar',
            data: {
                labels: batches,
                datasets: [{
                    label: 'Units/Hour',
                    data: productionSpeed,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (value) => value + ' units/h' }
                    }
                }
            }
        });

        // Chart 6: Reject Rate Trend
        charts.rejectTrend = new Chart(document.getElementById('rejectTrendChart'), {
            type: 'line',
            data: {
                labels: batches,
                datasets: [{
                    label: 'Reject Rate',
                    data: rejectRate,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: (context) => `Reject Rate: ${context.parsed.y}%` }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (value) => value + '%' }
                    }
                }
            }
        });
    }

    // Update charts via AJAX
    function updateCharts() {
        const period = document.getElementById('period-filter').value;
        const loadingEl = document.getElementById('loading-charts');
        const chartsEl = document.getElementById('charts-container');

        // Show loading
        chartsEl.classList.add('hidden');
        loadingEl.classList.remove('hidden');

        // Fetch new data
        fetch(`{{ route('admin.production.dashboard.chart-data') }}?period=${period}`)
            .then(response => response.json())
            .then(result => {
                productionData = result.data;
                
                if (productionData.length > 0) {
                    initializeCharts(productionData);
                } else {
                    showEmptyState();
                }

                loadingEl.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                loadingEl.classList.add('hidden');
                chartsEl.classList.remove('hidden');
            });
    }

    function showEmptyState() {
        document.getElementById('charts-container').classList.add('hidden');
        document.getElementById('empty-state').classList.remove('hidden');
    }
</script>