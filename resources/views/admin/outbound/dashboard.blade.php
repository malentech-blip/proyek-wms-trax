<x-app-layout>
    <x-slot name="header">
        Dashboard Outbound
    </x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-gray-800">Ringkasan Tugas Outbound</h2>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pending Sales Orders -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-blue-100 text-sm font-medium">Pending Sales Orders</h3>
                        <p class="text-4xl font-bold mt-2">{{ $pendingSalesOrders }}</p>
                        <p class="text-blue-100 text-xs mt-2">Menunggu diproses</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Packing in Progress -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-xl shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-orange-100 text-sm font-medium">Packing in Progress</h3>
                        <p class="text-4xl font-bold mt-2">{{ $packingInProgress }}</p>
                        <p class="text-orange-100 text-xs mt-2">Sedang dikemas</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Shipments -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-green-100 text-sm font-medium">Today's Shipments</h3>
                        <p class="text-4xl font-bold mt-2">{{ $todayShipments }}</p>
                        <p class="text-green-100 text-xs mt-2">Pengiriman hari ini</p>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Outbound Trend</h3>
                    <p class="text-sm text-gray-500 mt-1">Volume pengiriman 7 hari terakhir</p>
                </div>
                <div class="flex gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                        <span class="text-gray-600">Packing Lists</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                        <span class="text-gray-600">Delivery Orders</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-gray-600">Delivered</span>
                    </div>
                </div>
            </div>

            <!-- Canvas for Chart -->
            <div class="relative" style="height: 350px;">
                <canvas id="outboundTrendChart"></canvas>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t">
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600" id="total-packing">0</p>
                    <p class="text-xs text-gray-500 mt-1">Total Packing Lists</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600" id="total-do">0</p>
                    <p class="text-xs text-gray-500 mt-1">Total Delivery Orders</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600" id="total-delivered">0</p>
                    <p class="text-xs text-gray-500 mt-1">Total Delivered</p>
                </div>
            </div>
        </div>

        <!-- Additional Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.outbound.packing-lists.index') }}" 
                        class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">Manage Packing Lists</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ route('admin.outbound.delivery-orders.index') }}" 
                        class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">Delivery Orders</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Completion Rate</span>
                            <span class="font-semibold text-gray-800" id="completion-rate">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="completion-bar" class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Delivery Efficiency</span>
                            <span class="font-semibold text-gray-800" id="delivery-efficiency">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="efficiency-bar" class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Chart Data from Backend
    const chartData = @json($chartData);
    
    // Extract data for chart
    const labels = chartData.map(item => item.label);
    const packingListsData = chartData.map(item => item.packing_lists);
    const deliveryOrdersData = chartData.map(item => item.delivery_orders);
    const deliveredData = chartData.map(item => item.delivered);

    // Calculate totals
    const totalPacking = packingListsData.reduce((a, b) => a + b, 0);
    const totalDO = deliveryOrdersData.reduce((a, b) => a + b, 0);
    const totalDelivered = deliveredData.reduce((a, b) => a + b, 0);

    // Update summary stats
    document.getElementById('total-packing').textContent = totalPacking;
    document.getElementById('total-do').textContent = totalDO;
    document.getElementById('total-delivered').textContent = totalDelivered;

    // Calculate performance metrics
    const completionRate = totalDO > 0 ? Math.round((totalDelivered / totalDO) * 100) : 0;
    const deliveryEfficiency = totalPacking > 0 ? Math.round((totalDO / totalPacking) * 100) : 0;

    document.getElementById('completion-rate').textContent = completionRate + '%';
    document.getElementById('completion-bar').style.width = completionRate + '%';
    document.getElementById('delivery-efficiency').textContent = deliveryEfficiency + '%';
    document.getElementById('efficiency-bar').style.width = deliveryEfficiency + '%';

    // Create Chart
    const ctx = document.getElementById('outboundTrendChart');
    const outboundChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Packing Lists',
                    data: packingListsData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Delivery Orders',
                    data: deliveryOrdersData,
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(99, 102, 241)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Delivered',
                    data: deliveredData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    bodyFont: {
                        size: 14
                    },
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' items';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
</script>