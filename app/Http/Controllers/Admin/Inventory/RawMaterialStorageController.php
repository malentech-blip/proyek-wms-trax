<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Traits\LogsActivity;
use Illuminate\Contracts\View\View;

class RawMaterialStorageController extends Controller
{
    use LogsActivity;

    public function index(): View
    {
        $this->logActivity('View Raw Material Storage', 'Inventory');

        return view('admin.inventory.raw-materials.index');
    }
}
