<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\MasterData\Pallet; // Kita ambil dari Pallet

class LocationController extends Controller
{
    public function index()
    {
        // Ambil semua pallet dan relasinya ke rak dan lokasi
        $pallets = Pallet::with(['rack.location'])->latest()->paginate(15);
        return view('super-admin.master-data.location.index', compact('pallets'));
    }
}