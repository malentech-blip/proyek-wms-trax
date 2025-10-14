<?php

namespace App\Http\Controllers\SuperAdmin; 

use App\Models\Admin\Inbound\GoodsReceipt;
use App\Models\SuperAdmin\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk Kartu Statistik Utama
        $stats = [
            'total_inbound' => GoodsReceipt::count(),
            'progress_produksi' => 0, // Placeholder
            'pesanan_outbound' => 0, // Placeholder
            'total_nilai_stok' => 0, // Placeholder
        ];

        // Data untuk Chart Aktivitas Mingguan (Line Chart)
        $weeklyActivity = GoodsReceipt::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayName = now()->subDays($i)->format('l'); // 'l' for full day name e.g., 'Monday'

            $activity = $weeklyActivity->firstWhere('date', $date);

            $chartData['labels'][] = $dayName;
            $chartData['inbound'][] = $activity ? $activity->count : 0;
            $chartData['produksi'][] = 0; // Placeholder
            $chartData['outbound'][] = 0; // Placeholder
        }

        // Data untuk Distribusi Stok (Pie Chart) - Placeholder
        $stockDistribution = [
            'labels' => ['Bahan Baku', 'Barang Dalam Proses', 'Barang Jadi'],
            'data' => [45, 30, 25], // Placeholder percentages
        ];

        // Data untuk Log Aktivitas Terbaru
        $recentLogs = AuditLog::with('user')->latest()->take(5)->get();

        return view('super-admin.dashboard', compact('stats', 'chartData', 'stockDistribution', 'recentLogs'));
    }
}