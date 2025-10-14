<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;
use Illuminate\Http\Request;

class PutawayController extends Controller
{
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