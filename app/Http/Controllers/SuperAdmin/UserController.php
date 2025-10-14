<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar untuk mengambil user beserta rolenya
        $query = User::with('roles')->latest();

        // Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        // Terapkan filter status jika ada
        if ($request->filled('status') && $request->status != 'all') {
            // Asumsi kita akan punya kolom 'status' di tabel user nanti
            // Untuk sekarang, kita lewati dulu
        }

        $users = $query->paginate(15)->withQueryString();

        return view('super-admin.user-management.users.index', compact('users'));
    }
}