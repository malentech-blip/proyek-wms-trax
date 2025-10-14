<?php

namespace App\Http\Controllers\Admin\Inbound;

use App\Http\Controllers\Controller;
use App\Services\AccurateService;
use Illuminate\Http\Request;
use App\Models\Admin\Inbound\GoodsReceipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PurchaseOrderController extends Controller
{
    public function index(Request $request, AccurateService $accurate)
    {
        // Ambil daftar PO dari Accurate
        $purchaseOrders = $accurate->getPurchaseOrders($request);

        return view('admin.inbound.purchase-orders.index', compact('purchaseOrders'));
    }

    public function showReceiveForm(Request $request, AccurateService $accurate, $poId)
{
    // Panggil method baru di service untuk mengambil detail PO
    $purchaseOrder = $accurate->getPurchaseOrderDetail($poId);

    if (!$purchaseOrder) {
        // Jika PO tidak ditemukan, kembali ke halaman daftar dengan pesan error
        return redirect()->route('admin.inbound.purchase-orders.index')->with('error', 'Purchase Order tidak ditemukan.');
    }

    // Tampilkan view form penerimaan barang dengan data detail PO
    return view('admin.inbound.goods-receive.index', compact('purchaseOrder'));
}

public function storeReceiveForm(Request $request, AccurateService $accurate, $poId)
{
    $request->validate([
        'items.*.received_qty' => 'required|integer|min:0',
        'notes' => 'nullable|string',
        'photo' => 'nullable|image|max:2048', // Maksimal 2MB
    ]);

    $purchaseOrder = $accurate->getPurchaseOrderDetail($poId);
    if (!$purchaseOrder) {
        return redirect()->back()->with('error', 'Purchase Order tidak valid.');
    }

    DB::beginTransaction();
    try {
        // 1. Buat record Goods Receipt (header)
        $goodsReceipt = GoodsReceipt::create([
            'receipt_number' => 'GR-' . now()->format('Ymd-His'),
            'po_number' => $purchaseOrder['number'],
            'received_by_id' => Auth::id(),
            'receipt_date' => now(),
            'status' => 'pending_qc', // Status awal: Menunggu Quality Check
        ]);

        // 2. Simpan setiap item yang diterima
        foreach ($request->items as $itemId => $itemData) {
            $goodsReceipt->items()->create([
                'item_name' => $itemData['item_name'],
                'item_code' => $itemData['item_code'],
                'expected_qty' => $itemData['expected_qty'],
                'received_qty' => $itemData['received_qty'],
            ]);
        }

        // 3. (Opsional) Handle upload foto jika ada
        if ($request->hasFile('photo')) {
            // Logika untuk menyimpan file...
        }

        DB::commit();
        return redirect()
            ->route('admin.inbound.quality-check.show', $goodsReceipt)
            ->with('success', 'Barang berhasil diterima! Lanjutkan ke Quality Check.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
    }
}
}