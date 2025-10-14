<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Impor DB untuk proses bulk

class LogController extends Controller
{
   public function index(Request $request)
    {
        // ... (kode validasi Anda tetap sama)
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:draft,processed,submitted,pending,in-progress',
            'time_filter' => 'nullable|string|in:this_week,this_month,this_year',
            'per_page' => 'nullable|integer|in:10,25,50,100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (!session()->has('accurate_database')) {
            return view('logs.index');
        }

        // --- DIUBAH: Logika Perhitungan Statistik dan Persentase ---

        // 1. Tentukan Periode Waktu
        $timeFilter = $request->input('time_filter', 'this_week'); // Default 'this_week'
        $currentPeriod = [];
        $previousPeriod = [];

        switch ($timeFilter) {
            case 'this_month':
                $currentPeriod = [now()->startOfMonth(), now()->endOfMonth()];
                $previousPeriod = [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
                break;
            case 'this_year':
                $currentPeriod = [now()->startOfYear(), now()->endOfYear()];
                $previousPeriod = [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()];
                break;
            case 'this_week':
            default:
                $currentPeriod = [now()->startOfWeek(), now()->endOfWeek()];
                $previousPeriod = [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()];
                break;
        }

        // 2. Hitung Statistik untuk Periode Saat Ini
        $totalQuotations = Quotation::whereBetween('created_at', $currentPeriod)->count();
        $processedQuotations = Quotation::where('status', 'processed')->whereBetween('created_at', $currentPeriod)->count();
        $submittedQuotations = Quotation::where('status', 'submitted')->whereBetween('created_at', $currentPeriod)->count();
        
        // 3. Hitung Data Periode Sebelumnya untuk Perbandingan
        $submittedPrevious = Quotation::where('status', 'submitted')->whereBetween('created_at', $previousPeriod)->count();

        // 4. Hitung Persentase Perubahan
        if ($submittedPrevious > 0) {
            $percentageChange = (($submittedQuotations - $submittedPrevious) / $submittedPrevious) * 100;
        } elseif ($submittedQuotations > 0) {
            $percentageChange = 100; // Jika sebelumnya 0 dan sekarang ada, anggap naik 100%
        } else {
            $percentageChange = 0; // Jika keduanya 0
        }

        // --- Akhir Logika Perhitungan ---

        // Query untuk tabel log tetap sama
        $query = Quotation::latest();
        $searchTerm = $request->input('search');
        $statusFilter = $request->input('status');
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($searchTerm) { $query->where('accurate_customer_id', 'like', '%' . $searchTerm . '%'); }
        if ($statusFilter) { $query->where('status', $statusFilter); }
        if ($startDate && $endDate) { $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']); }

        // Jika tidak ada filter tanggal spesifik, gunakan filter waktu umum
        elseif ($timeFilter) {
            $query->whereBetween('created_at', $currentPeriod);
        }

        $activities = $query->paginate($perPage)->appends($request->query());

        // Kirim semua data, termasuk variabel baru $percentageChange
        return view('logs.index', compact(
            'totalQuotations', 
            'processedQuotations', 
            'submittedQuotations',
            'percentageChange', // <-- Variabel baru
            'activities'
        ));
    }
    

    // --- FUNGSI BARU UNTUK BULK ACTION ---
    public function bulkProcess(Request $request)
    {
        // Validasi bahwa 'selected_ids' adalah array dan setiap isinya adalah angka
        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'integer|exists:quotations,id',
        ]);

        $selectedIds = $request->input('selected_ids');

        try {
            // Contoh: Mengubah status semua quotation yang dipilih menjadi 'processed'
            // Ganti logika ini sesuai dengan kebutuhan bulk action Anda
            DB::table('quotations')
                ->whereIn('id', $selectedIds)
                ->where('status', 'draft') // Hanya proses yang masih draft
                ->update(['status' => 'processed']);

            return back()->with('success', count($selectedIds) . ' item berhasil diproses.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat melakukan bulk action: ' . $e->getMessage());
        }
    }
}