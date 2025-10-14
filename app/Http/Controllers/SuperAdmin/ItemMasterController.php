<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\MasterData\Item; // Ganti modelnya

class ItemMasterController extends Controller
{
    public function index()
    {
        $items = Item::latest()->paginate(15);
        return view('super-admin.master-data.item-master.index', compact('items'));
    }
}