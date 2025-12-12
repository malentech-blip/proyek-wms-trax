<?php

namespace App\Livewire\Admin\Inbound;

use App\Models\Admin\Inbound\GoodsReceipt;
use App\Models\Admin\Inbound\QualityCheck;
use App\Models\Admin\Inbound\RejectInbound;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Inbound\GoodsReceiptItem;
use Livewire\Component;
use Livewire\WithFileUploads;

class QualityCheckForm extends Component
{
    use WithFileUploads;

    public GoodsReceipt $goodsReceipt;
    public $items = [];

    // Method ini berjalan saat komponen pertama kali dimuat
    public function mount(GoodsReceipt $goodsReceipt)
    {
        // Muat data goods receipt beserta item-itemnya
        $this->goodsReceipt = $goodsReceipt->load('items');
        
        // Siapkan array untuk menampung data input form
        foreach ($this->goodsReceipt->items as $item) {
            $this->items[$item->id] = [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'received_qty' => $item->received_qty,
                'passed_qty' => $item->received_qty, // Default-nya, semua barang dianggap lolos
                'rejected_qty' => 0,
                'notes' => '',
                'photo' => null,
            ];
        }
    }

    // Method ini berjalan SETIAP KALI ada input yang diubah (karena wire:model.live)
    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $itemId = $parts[0];
        $field = $parts[1];

        $receivedQty = (int) $this->items[$itemId]['received_qty'];
        $passedQty = (int) $this->items[$itemId]['passed_qty'];
        $rejectedQty = (int) $this->items[$itemId]['rejected_qty'];

        // Logika kalkulasi otomatis
        if ($field === 'passed_qty') {
            if ($passedQty > $receivedQty) $passedQty = $receivedQty;
            if ($passedQty < 0) $passedQty = 0;
            $this->items[$itemId]['passed_qty'] = $passedQty;
            $this->items[$itemId]['rejected_qty'] = $receivedQty - $passedQty;
        } else { // Jika rejected_qty yang diubah
            if ($rejectedQty > $receivedQty) $rejectedQty = $receivedQty;
            if ($rejectedQty < 0) $rejectedQty = 0;
            $this->items[$itemId]['rejected_qty'] = $rejectedQty;
            $this->items[$itemId]['passed_qty'] = $receivedQty - $rejectedQty;
        }
    }

  public function save()
{
    // 1. Validasi Input
    $this->validate([
        'items.*.passed_qty' => 'required|integer|min:0',
        'items.*.rejected_qty' => 'required|integer|min:0',
        'items.*.notes' => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();
    try {
        foreach ($this->items as $itemId => $itemData) {
            
            // Ambil data item asli untuk verifikasi
            $grItem = GoodsReceiptItem::findOrFail($itemId);
            
            // Validasi: Jumlah pass + reject tidak boleh melebihi jumlah diterima
            if (($itemData['passed_qty'] + $itemData['rejected_qty']) > $grItem->received_qty) {
                throw new \Exception("Jumlah total (Lolos + Reject) untuk item {$grItem->item_name} melebihi jumlah yang diterima.");
            }

            // 2. Simpan Item yang Ditolak (Jika Ada)
            if ($itemData['rejected_qty'] > 0) {
                RejectInbound::create([
                    'goods_receipt_item_id' => $itemId,
                    'qc_by_id' => Auth::id(),
                    'rejected_qty' => $itemData['rejected_qty'],
                    'reason' => $itemData['notes'],
                    'action' => 'pending', // Status awal: Menunggu keputusan (Retur/Dispose)
                ]);
            }

            // 3. Update Barang yang Lolos di tabel goods_receipt_items
            // Kita simpan passed_qty agar nanti di proses Putaway kita tahu berapa yang harus disimpan
            // (Asumsi: Anda perlu menambahkan kolom 'passed_qty' di tabel goods_receipt_items via migrasi, 
            // atau gunakan logic sisa di controller Putaway)
            $grItem->update([
                'passed_qty' => $itemData['passed_qty'], // Pastikan kolom ini ada atau logic disesuaikan
                // Jika kolom passed_qty belum ada di database, langkah ini bisa dilewati 
                // dan nanti dihitung manual: received - rejected
            ]);
        }

        // 4. Update Status Header
        $this->goodsReceipt->update(['status' => 'qc_completed']);
        
        DB::commit();

        session()->flash('success', 'Quality Check selesai. Barang reject telah dicatat.');
        return redirect()->route('admin.inbound.putaway.show', $this->goodsReceipt);

    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    public function render()
    {
        return view('livewire.admin.inbound.quality-check-form');
    }
}