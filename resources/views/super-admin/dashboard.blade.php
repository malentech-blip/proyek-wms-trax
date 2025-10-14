<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="space-y-8">
        <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Super Admin</h2>
                {{-- Tombol ini bisa kita fungsikan nanti --}}
                {{-- <a href="#" class="bg-white text-blue-600 font-semibold py-2 px-4 rounded-lg text-sm hover:bg-gray-100">
                    + Buat Perintah Kerja
                </a> --}}
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-blue-700/50 p-4 rounded-lg">
                    <p class="text-sm text-blue-200">Total Inbound</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_inbound'] }}</p>
                </div>
                <div class="bg-blue-700/50 p-4 rounded-lg">
                    <p class="text-sm text-blue-200">Progress Produksi</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['progress_produksi'] }}</p>
                </div>
                <div class="bg-blue-700/50 p-4 rounded-lg">
                    <p class="text-sm text-blue-200">Pesanan Outbound</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['pesanan_outbound'] }}</p>
                </div>
                <div class="bg-blue-700/50 p-4 rounded-lg">
                    <p class="text-sm text-blue-200">Total Nilai Stok</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_nilai_stok']) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800">Aktivitas Gudang Mingguan</h3>
                <div class="mt-4"><canvas id="weeklyActivityChart"></canvas></div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800">Distribusi Stok Gudang</h3>
                <div class="mt-4"><canvas id="stockDistributionChart"></canvas></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b"><h3 class="text-lg font-semibold text-gray-800">Log Aktivitas Terbaru</h3></div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left font-semibold text-gray-600">Pengguna</th>
                            <th class="p-3 text-left font-semibold text-gray-600">Aksi</th>
                            <th class="p-3 text-left font-semibold text-gray-600">Modul</th>
                            <th class="p-3 text-left font-semibold text-gray-600">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($recentLogs as $log)
                        <tr>
                            <td class="p-3">{{ $log->user->name ?? 'System' }}</td>
                            <td class="p-3">{{ $log->action }}</td>
                            <td class="p-3">{{ $log->module }}</td>
                            <td class="p-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="p-3 text-center text-gray-500">Belum ada aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Data dari Controller
        const weeklyData = @json($chartData);
        const stockData = @json($stockDistribution);

        // Line Chart - Aktivitas Mingguan
        new Chart(document.getElementById('weeklyActivityChart'), {
            type: 'line',
            data: {
                labels: weeklyData.labels,
                datasets: [
                    { label: 'Inbound', data: weeklyData.inbound, borderColor: '#3b82f6', tension: 0.1 },
                    { label: 'Produksi', data: weeklyData.produksi, borderColor: '#8b5cf6', tension: 0.1 },
                    { label: 'Outbound', data: weeklyData.outbound, borderColor: '#10b981', tension: 0.1 },
                ]
            }
        });

        // Pie Chart - Distribusi Stok
        new Chart(document.getElementById('stockDistributionChart'), {
            type: 'pie',
            data: {
                labels: stockData.labels,
                datasets: [{
                    label: 'Distribusi Stok',
                    data: stockData.data,
                    backgroundColor: ['#3b82f6', '#8b5cf6', '#10b981'],
                }]
            }
        });
    </script>
    @endpush
</x-app-layout>