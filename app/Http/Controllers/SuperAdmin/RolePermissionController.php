<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class RolePermissionController extends Controller
{
     public function index()
    {
        // 1. Ambil semua role yang ada, kecuali "Super Admin" karena hak aksesnya tidak bisa diubah.
        // 2. 'with('permissions')' akan mengambil semua izin yang terhubung dengan setiap role.
        $roles = Role::where('name', '!=', 'Super Admin')
                     ->with('permissions')
                     ->get();

        return view('super-admin.user-management.roles.index', compact('roles'));
    }

     public function permissionsMatrix()
    {
        // 1. Ambil semua permissions yang ada
        $permissions = Permission::all()->groupBy(function($permission) {
            // Kita kelompokkan berdasarkan kata pertama dari nama permission
            // contoh: 'manage_inbound' -> 'manage'
            return explode('_', $permission->name)[0];
        });

        // 2. Ambil semua role yang akan menjadi kolom di tabel
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        // 3. Buat sebuah 'peta' hak akses untuk pengecekan cepat di view
        $rolePermissions = [];
        foreach ($roles as $role) {
            // Ambil semua nama permission untuk role ini dan simpan ke dalam array
            $rolePermissions[$role->id] = $role->permissions->pluck('name')->toArray();
        }

        return view('super-admin.user-management.permissions.index', compact('permissions', 'roles', 'rolePermissions'));
    }

    public function update(Request $request)
    {
        $request->validate(['permissions' => 'sometimes|array']);

        $salesRole = Role::findByName('sales');
        $permissions = $request->input('permissions', []);
        
        $salesRole->syncPermissions($permissions);

        return redirect()->route('super-admin.user-management.roles.index')->with('success', 'Hak akses untuk peran Sales berhasil diperbarui.');
    }
}