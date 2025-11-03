<?php

namespace App\Livewire\Admin\Outbound;

use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder as LocalSalesOrder;
use App\Models\Admin\Production\FinishedGood;
use App\Models\Admin\Production\ProductionItemLabel;
use App\Services\AccurateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PackingForm extends Component
{
    public int $soId;

    public array $scanned = [];

    #[Validate('required|string')]
    public string $qrCode = '';

    public ?array $salesOrder = null;

    public function mount(int $soId, AccurateService $accurate): void
    {
        $this->soId = $soId;
        try {
            $this->salesOrder = $accurate->getSalesOrderDetail($soId) ?: null;
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch SO detail for packing form', ['soId' => $soId, 'message' => $e->getMessage()]);
            $this->salesOrder = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.outbound.packing-form');
    }

    public function scanFinishedGood(): void
    {
        $this->validateOnly('qrCode');

        $qr = trim($this->qrCode);
        $this->qrCode = '';

        $label = ProductionItemLabel::query()->where('qr_code', $qr)->first();
        if (! $label) {
            $this->addError('qrCode', 'QR code tidak ditemukan.');

            return;
        }

        $finishedGood = FinishedGood::query()->with(['item', 'production_item_label'])->where('label_id', $label->id)->first();
        if (! $finishedGood) {
            $this->addError('qrCode', 'Finished Good untuk QR ini tidak valid.');

            return;
        }

        // Prevent duplicates
        foreach ($this->scanned as $row) {
            if ((int) $row['label_id'] === (int) $label->id) {
                $this->addError('qrCode', 'Label sudah dipindai.');

                return;
            }
        }

        $this->scanned[] = [
            'label_id' => (int) $label->id,
            'qr_code' => $label->qr_code,
            'item_id' => (int) $finishedGood->item_id,
            'item_name' => optional($finishedGood->item)->item_name ?? optional($finishedGood->item)->name ?? 'Item',
            'quantity' => (int) $finishedGood->quantity,
        ];
    }

    public function removeScanned(int $index): void
    {
        if (isset($this->scanned[$index])) {
            array_splice($this->scanned, $index, 1);
        }
    }

    public function savePackingList(AccurateService $accurate)
    {
        if (empty($this->scanned)) {
            $this->addError('qrCode', 'Belum ada item yang dipindai.');

            return;
        }

        return DB::transaction(function () use ($accurate) {
            // Ensure local Sales Order exists
            $remote = $this->salesOrder ?: $accurate->getSalesOrderDetail($this->soId);
            $soNumber = $remote['number'] ?? null;

            if (! $soNumber) {
                $this->addError('qrCode', 'Sales Order tidak valid.');

                return null;
            }

            $localSO = LocalSalesOrder::firstOrCreate(
                ['so_number' => $soNumber],
                ['status' => 'PENDING', 'sync_status' => 'SYNCED']
            );

            // Create packing list (WIP for now)
            $packingList = PackingList::create([
                'so_id' => $localSO->id,
                'packed_by' => Auth::user()?->name ?? 'System',
                'packed_at' => now(),
                'status' => 'WIP',
            ]);

            // Update SO to Packed
            $localSO->update(['status' => 'Packed']);

            // Trigger Delivery Order creation (initial status In Delivery, date today)
            DeliveryOrder::create([
                'packing_list_id' => $packingList->id,
                'delivered_no' => 'DO-'.now()->format('Ymd-His'),
                'driver_name' => '-',
                'delivery_date' => now(),
                'status' => 'In Delivery',
            ]);

            session()->flash('success', 'Packing List berhasil dibuat. Delivery Order telah dibuat.');

            return redirect()->route('admin.outbound.delivery-orders.index');
        });
    }
}
