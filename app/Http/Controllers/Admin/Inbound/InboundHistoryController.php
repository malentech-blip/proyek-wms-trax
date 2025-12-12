<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;
use Illuminate\Http\Request;

class InboundHistoryController extends Controller
{
    public function index()
    {
        
        // Ambil data penerimaan barang yang statusnya sudah 'completed'
        // Kita load 'items' untuk menghitung jumlah item, dan 'receivedBy' untuk tahu siapa yang terima
        $history = GoodsReceipt::with(['items', 'receivedBy'])
            ->where('status', 'completed')
            ->latest() // Urutkan dari yang terbaru
            ->paginate(10);

        return view('admin.inbound.history.index', compact('history'));
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        // Pastikan kita meload relasi item labels juga agar bisa lihat detail lokasi penyimpanan
        $goodsReceipt->load(['items.itemLabels.location', 'items.itemLabels.rack', 'receivedBy']);

        return view('admin.inbound.history.show', compact('goodsReceipt'));
    }
}