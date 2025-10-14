<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\MasterData\Customer; // Ganti modelnya

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('super-admin.master-data.customer.index', compact('customers'));
    }
}