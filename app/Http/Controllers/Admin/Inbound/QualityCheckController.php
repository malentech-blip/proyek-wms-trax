<?php
namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inbound\GoodsReceipt;

class QualityCheckController extends Controller
{
    public function show(GoodsReceipt $goodsReceipt)
    {
        // Kirim data Goods Receipt yang baru dibuat ke view
        return view('admin.inbound.quality-check.show', compact('goodsReceipt'));
    }
}