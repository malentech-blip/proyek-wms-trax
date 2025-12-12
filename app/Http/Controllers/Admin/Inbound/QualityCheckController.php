<?php
namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;


class QualityCheckController extends Controller
{
    public function index()
{
    // Ambil penerimaan barang yang statusnya 'pending_qc' (Menunggu QC)
    $pendingQC = GoodsReceipt::with('receivedBy')
        ->where('status', 'pending_qc')
        ->latest()
        ->paginate(10);

    return view('admin.inbound.quality-check.index', compact('pendingQC'));
}

    public function show(GoodsReceipt $goodsReceipt)
    {
        // Kirim data Goods Receipt yang baru dibuat ke view
        return view('admin.inbound.quality-check.show', compact('goodsReceipt'));
    }
}