<?php

namespace App\Livewire\Admin\Inventory;

use App\Exports\InventoryExport;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class StockAdjusmentForm extends Component
{

    #[On('printStockReport')]
    public function printStockReport()
    {
        return Excel::download(new InventoryExport, 'stock-reports.xlsx');
    }

    public function render()
    {
        return view('livewire.admin.inventory.stock-adjusment-form');
    }
}
