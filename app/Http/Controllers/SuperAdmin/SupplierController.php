<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\MasterData\Supplier; // Ganti modelnya

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(15);
        return view('super-admin.master-data.supplier.index', compact('suppliers'));
    }
}