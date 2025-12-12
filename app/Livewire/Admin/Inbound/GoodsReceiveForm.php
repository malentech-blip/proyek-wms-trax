<?php

// app/Livewire/Admin/Inbound/GoodsReceiveForm.php
namespace App\Livewire\Admin\Inbound;

use App\Services\AccurateService;
use App\Models\Admin\Inbound\GoodsReceipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class GoodsReceiveForm extends Component
{
    public $purchaseOrder;
    public $items = [];
    public $notes;
    // public $photo; // Fitur upload foto bisa ditambahkan nanti

    public function mount($poId, AccurateService $accurate)
    {
        $this->purchaseOrder = $accurate->getPurchaseOrderDetail($poId);
        if (!$this->purchaseOrder) {
            return redirect()->route('admin.inbound.purchase-orders.index')->with('error', 'Purchase Order tidak ditemukan.');
        }

        foreach ($this->purchaseOrder['detailItem'] as $item) {
            $this->items[$item['id']] = [
                'item_id' => $item['item']['id'],
                'item_code' => $item['item']['no'],
                'item_name' => $item['item']['name'],
                'expected_qty' => (int) $item['quantity'],
                'received_qty' => 0, // Mulai dari 0
            ];
        }
    }

    #[On('itemScanned')]
    public function handleItemScan($scannedCode)
    {
        $found = false;
        foreach ($this->items as &$item) {
            if ($item['item_code'] == $scannedCode) {
                $item['received_qty']++;
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->dispatch('play-scan-success-sound');
            session()->flash('scan-success', 'Item ' . $scannedCode . ' berhasil dipindai!');
        } else {
            $this->dispatch('play-scan-error-sound');
            session()->flash('scan-error', 'Item dengan kode ' . $scannedCode . ' tidak ditemukan di PO ini.');
        }
    }

    public function save()
    {
        // Logika penyimpanan yang sudah ada sebelumnya
        DB::beginTransaction();
        try {
            $goodsReceipt = GoodsReceipt::create([
                'receipt_number' => 'GR-' . now()->format('Ymd-His'),
                'po_number' => $this->purchaseOrder['number'],
                'received_by_id' => Auth::id(),
                'receipt_date' => now(),
                'status' => 'pending_qc',
            ]);

            foreach ($this->items as $itemData) {
                if ($itemData['received_qty'] > 0) {
                    $goodsReceipt->items()->create([
                        'item_name' => $itemData['item_name'],
                        'item_code' => $itemData['item_code'],
                        'expected_qty' => $itemData['expected_qty'],
                        'received_qty' => $itemData['received_qty'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.inbound.quality-check.show', $goodsReceipt)
                             ->with('success', 'Barang berhasil diterima! Lanjutkan ke Quality Check.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.inbound.goods-receive-form');
    }
}
