<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;
use App\Models\Admin\Inbound\ItemLabel;
use Barryvdh\DomPDF\Facade\Pdf;

class LabelPrintController extends Controller
{
    public function print(GoodsReceipt $goodsReceipt)
    {
        // Ambil semua item label yang baru saja dibuat untuk Goods Receipt ini
        $itemLabels = ItemLabel::whereHas('goodsReceiptItem', function ($query) use ($goodsReceipt) {
            $query->where('goods_receipt_id', $goodsReceipt->id);
        })->get();

        if ($itemLabels->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada label untuk dicetak.');
        }

        // Muat view PDF dengan data label
        $pdf = Pdf::loadView('admin.inbound.putaway.label-pdf', compact('itemLabels'));

            $widthInPoints = 52 * 2.83465;  // Konversi mm ke points
            $heightInPoints = 32 * 2.83465; // Konversi mm ke points
            $pdf->setPaper([0, 0, $widthInPoints, $heightInPoints]);

        return $pdf->stream('labels-' . $goodsReceipt->receipt_number . '.pdf');
    }
}