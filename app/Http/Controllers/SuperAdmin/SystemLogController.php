<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
        public function index()
    {
        // Data dummy untuk tabel log
        $logs = collect([
            (object) ['date' => '10/10/2025', 'user' => 'Andi', 'module' => 'Inbound', 'activity' => 'Menerima PO-01223', 'status' => 'SUCCESS'],
            (object) ['date' => '09/10/2025', 'user' => 'Rina', 'module' => 'User Management', 'activity' => 'Menambah user baru: Budi', 'status' => 'SUCCESS'],
            (object) ['date' => '08/10/2025', 'user' => 'System', 'module' => 'Accurate Sync', 'activity' => 'Sinkronisasi SO Gagal', 'status' => 'FAILED'],
        ]);

        return view('super-admin.system-logs.index', compact('logs'));
    }
}