<?php

namespace App\Livewire\Admin\Inbound;

use App\Models\Admin\Inbound\GoodsReceipt;
use App\Models\Admin\Inbound\ItemLabel;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Pallet;
use App\Models\SuperAdmin\MasterData\Rack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class PutawayForm extends Component
{
    public GoodsReceipt $goodsReceipt;
    public $items = [];

    // Data Master untuk Dropdown
    public $locations = [];
    public $racks = [];
    public $pallets = [];

    public function mount(GoodsReceipt $goodsReceipt)
    {
        // 1. Load Master Data untuk Pilihan Lokasi
        $this->locations = Location::all();
        $this->racks = Rack::all();
        $this->pallets = Pallet::all();

        // 2. Load Item dari Goods Receipt
        $this->goodsReceipt = $goodsReceipt->load('items');

        // 3. Siapkan data item untuk form, HANYA yang passed_qty > 0
        foreach ($this->goodsReceipt->items as $item) {
            
            // Kita gunakan kolom 'passed_qty' yang baru dibuat
            $qtyToStore = $item->passed_qty; 

            if ($qtyToStore > 0) {
                $this->items[$item->id] = [
                    'goods_receipt_item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'qty_to_store' => $qtyToStore, // Menampilkan jumlah yang harus disimpan
                    'storage_type' => 'rack',
                    'location_id' => '',
                    'rack_id' => '',
                    'pallet_id' => '',
                ];
            }
        }
    }

    public function save()
    {
        
        // Validasi input lokasi
        $this->validate([
            'items.*.location_id' => 'required',
            'items.*.storage_type' => 'required|in:rack,pallet',
            
            // Jika storage_type = rack, maka rack_id wajib. Pallet boleh kosong.
            'items.*.rack_id' => 'required_if:items.*.storage_type,rack',
            
            // Jika storage_type = pallet, maka pallet_id wajib. Rack boleh kosong.
            'items.*.pallet_id' => 'required_if:items.*.storage_type,pallet',
        ], [
            'items.*.location_id.required' => 'Lokasi wajib dipilih.',
            'items.*.rack_id.required' => 'Rak wajib dipilih.',
            'items.*.pallet_id.required' => 'Pallet wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            foreach ($this->items as $itemData) {
                // Generate QR Code Unik
                // Format: ITEMCODE-TIMESTAMP-RANDOM
                $qrString = $itemData['item_code'] . '-' . time() . '-' . Str::random(4);
                $rackId = ($itemData['storage_type'] === 'rack') ? $itemData['rack_id'] : null;
                $palletId = ($itemData['storage_type'] === 'pallet') ? $itemData['pallet_id'] : null;

                // Simpan ke tabel item_labels (Ini yang menjadi stok fisik nanti)
                ItemLabel::create([
                    'goods_receipt_item_id' => $itemData['goods_receipt_item_id'],
                    'item_name' => $itemData['item_name'],
                    'item_code' => $itemData['item_code'],
                    'quantity' => $itemData['qty_to_store'], // Menggunakan passed_qty
                    'qr_code' => $qrString, 
                    'location_id' => $itemData['location_id'],
                    'rack_id' => $rackId,
                    'pallet_id' => $palletId,
                    'status' => 'stored', // Status awal stok
                ]);

                // TODO: Di tahap Inventory nanti, kita juga akan update tabel 'inventories' 
                // untuk pencatatan stok agregat/total.
            }

            // Update status header penerimaan menjadi completed
           // Update status header penerimaan menjadi completed
            $this->goodsReceipt->update(['status' => 'completed']);

            DB::commit();

            // 1. Simpan pesan sukses ke session (agar muncul di halaman PO nanti)
            session()->flash('success', 'Putaway berhasil! Label sedang dibuka di tab baru.');

            // 2. Kirim event ke browser untuk handle buka tab & redirect
            $this->dispatch('putaway-completed', [
                'printUrl' => route('admin.inbound.putaway.print-labels', $this->goodsReceipt),
                'redirectUrl' => route('admin.inbound.purchase-orders.index'), // Pastikan nama route ini benar sesuai web.php Anda
            ]);
           
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan lokasi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.inbound.putaway-form');
    }
}