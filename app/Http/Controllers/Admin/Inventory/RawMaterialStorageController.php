<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class RawMaterialStorageController extends Controller
{
    public function index(): View
    {
        return view('admin.inventory.raw-materials.index');
    }
}
