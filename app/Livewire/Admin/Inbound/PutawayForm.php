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

    public $locations = [];
    public $racks = [];
    public $pallets = [];

    public function mount(GoodsReceipt $goodsReceipt)
    {
        $this->locations = Location::all();
        $this->racks = Rack::all();
        $this->pallets = Pallet::all();

        // Muat item dari Goods Receipt yang sudah lolos QC
        $this->goodsReceipt = $goodsReceipt->load('items');

        foreach ($this->goodsReceipt->items as $item) {
            // TODO: Nanti kita ambil qty yang lolos dari tabel quality_checks
            $passedQty = $item->received_qty; 

            if ($passedQty > 0) {
                $this->items[$item->id] = [
                    'goods_receipt_item_id' => $item->id,
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'passed_qty' => $passedQty,
                    'location_id' => '',
                    'rack_id' => '',
                    'pallet_id' => '',
                ];
            }
        }
    }

    public function save()
    {
        $this->validate([
            'items.*.location_id' => 'required',
            'items.*.rack_id' => 'required',
            'items.*.pallet_id' => 'required',
        ]);

        DB::beginTransaction();
        try {
            foreach ($this->items as $itemData) {
                ItemLabel::create([
                    'goods_receipt_item_id' => $itemData['goods_receipt_item_id'],
                    'item_name' => $itemData['item_name'],
                    'item_code' => $itemData['item_code'],
                    'quantity' => $itemData['passed_qty'],
                    'qr_code' => Str::uuid(), // Generate QR code unik
                    'location_id' => $itemData['location_id'],
                    'rack_id' => $itemData['rack_id'],
                    'pallet_id' => $itemData['pallet_id'],
                ]);

                // TODO: Tambahkan logika untuk update stok di tabel 'inventories'
            }

            // Update status header penerimaan menjadi 'completed'
            $this->goodsReceipt->update(['status' => 'completed']);

            DB::commit();

           return redirect()->route('admin.inbound.putaway.print-labels', $this->goodsReceipt);
           
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