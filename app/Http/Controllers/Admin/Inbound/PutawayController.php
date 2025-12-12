<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;
use Illuminate\Http\Request;

class PutawayController extends Controller
{
    public function index()
{
    // Ambil barang yang sudah selesai QC tapi belum di-Putaway ('qc_completed')
    $pendingPutaway = GoodsReceipt::with('items')
        ->where('status', 'qc_completed')
        ->latest()
        ->paginate(10);

    return view('admin.inbound.putaway.index', compact('pendingPutaway'));
}
    public function show(GoodsReceipt $goodsReceipt)
    {
        // Pastikan kita hanya memproses barang yang sudah lolos QC
        if ($goodsReceipt->status !== 'qc_completed') {
            return redirect()->back()->with('error', 'Proses ini hanya untuk barang yang sudah lolos Quality Check.');
        }

        // Kirim data ke view
        return view('admin.inbound.putaway.show', compact('goodsReceipt'));
    }
}