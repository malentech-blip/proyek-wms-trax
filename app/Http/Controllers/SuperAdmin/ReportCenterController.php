<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportCenterController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy untuk tabel Inbound
        $inboundReports = collect([
            (object) ['date' => '10/10/2025', 'po_number' => 'PO-01223', 'supplier' => 'PT. Kimia Abadi', 'quantity' => 150, 'qc_status' => 'LULUS', 'received_by' => 'OK'],
            (object) ['date' => '09/10/2025', 'po_number' => 'PO-01224', 'supplier' => 'PT. Sinar Jaya', 'quantity' => 180, 'qc_status' => 'GAGAL', 'received_by' => 'RINA'],
        ]);

        // Variabel lain untuk tab lain bisa ditambahkan di sini nanti
        $productionReports = collect([]);
        $inventoryReports = collect([]);

        return view('super-admin.reports-center.index', compact('inboundReports', 'productionReports', 'inventoryReports'));
    }
}