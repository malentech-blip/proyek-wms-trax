<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;
use App\Models\Admin\Inbound\ItemLabel;
use Barryvdh\DomPDF\Facade\Pdf;

class LabelPrintController extends Controller
{
 // app/Http/Controllers/Admin/Inbound/LabelPrintController.php
public function print(GoodsReceipt $goodsReceipt)
{
   // --- TAMBAHKAN BARIS INI UNTUK DEBUGGING ---
    // dd('Controller Terpanggil', $goodsReceipt->id); 
    // -------------------------------------------

    $itemLabels = ItemLabel::whereHas('goodsReceiptItem', function ($query) use ($goodsReceipt) {
        $query->where('goods_receipt_id', $goodsReceipt->id);
    })->with(['rack', 'pallet', 'location'])->get();

    // --- TAMBAHKAN INI JUGA ---
    if ($itemLabels->isEmpty()) {
        dd('Data Item Labels Kosong! Cek tabel item_labels di database.');
    }

    if ($itemLabels->isEmpty()) {
        return redirect()->back()->with('error', 'Tidak ada label untuk dicetak.');
    }

    // Load view PDF
    $pdf = Pdf::loadView('admin.inbound.putaway.label-pdf', compact('itemLabels'));

    // Set ukuran kertas (contoh: 52mm x 32mm)
    $widthInPoints = 52 * 2.83465;
    $heightInPoints = 32 * 2.83465;
    $pdf->setPaper([0, 0, $widthInPoints, $heightInPoints]);

    return $pdf->stream('labels-' . $goodsReceipt->receipt_number . '.pdf');
}
}