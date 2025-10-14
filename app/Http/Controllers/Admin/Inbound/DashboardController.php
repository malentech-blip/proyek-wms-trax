<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Di sini nanti kita akan menambahkan logika untuk mengambil
        // data PO Pending, QC Pending, dll.
        // Untuk sekarang, kita hanya tampilkan view-nya.
        return view('admin.inbound.dashboard');
    }
}