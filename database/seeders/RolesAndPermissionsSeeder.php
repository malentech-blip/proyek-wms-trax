<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
  public function run(): void
  {
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // --- DEFINISI SEMUA PERMISSIONS UNTUK WMS ---
    $permissions = [
      // General
      'view_dashboard',

      // Super Admin
      'manage_master_data',
      'view_item_master',
      'create_item_master',
      'edit_item_master',
      'manage_users',
      'manage_integration',
      'manage_templates',
      'view_reports',
      'view_system_logs',

      // Inbound
      'manage_inbound',
      'receive_goods',
      'perform_quality_check',
      'putaway_items',

      // Inventory
      'manage_inventory',
      'move_stock',
      'adjust_stock',

      // Production
      'manage_production',
      'request_material',
      'view_picking_list',
      'manage_wip',

      // Outbound
      'manage_outbound',
      'create_packing_list',
      'manage_delivery_order',
    ];

    foreach ($permissions as $permission) {
      Permission::firstOrCreate(['name' => $permission]);
    }
    $this->command->info('WMS Permissions created.');

    // --- DEFINISI SEMUA ROLES UNTUK WMS ---
    $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
    $superAdminRole->givePermissionTo(Permission::all());

    Role::firstOrCreate(['name' => 'Admin Inbound'])->givePermissionTo(['view_dashboard', 'manage_inbound', 'receive_goods', 'perform_quality_check', 'putaway_items']);
    Role::firstOrCreate(['name' => 'Admin Inventory'])->givePermissionTo(['view_dashboard', 'manage_inventory', 'move_stock', 'adjust_stock']);
    Role::firstOrCreate(['name' => 'Admin Production'])->givePermissionTo(['view_dashboard', 'manage_production', 'request_material', 'view_picking_list', 'manage_wip']);
    Role::firstOrCreate(['name' => 'Admin Outbound'])->givePermissionTo(['view_dashboard', 'manage_outbound', 'create_packing_list', 'manage_delivery_order']);
    $this->command->info('WMS Roles created.');

    // --- BUAT USER SUPER ADMIN ---
    $adminUser = User::firstOrCreate(
      ['email' => 'admin@example.com'],
      ['name' => 'Super Admin', 'password' => Hash::make('password'), 'role' => 'Super Admin']
    );
    $adminUser->assignRole($superAdminRole);
    $productionUser = User::firstOrCreate(
      ['email' => 'production@example.com'],
      ['name' => 'Admin Production', 'password' => Hash::make('password'), 'role' => 'Admin Production']
    );
    $productionUser->assignRole('Admin Production');
    $this->command->info('Super Admin user created/found and role assigned.');

    // Hapus user 'sales' jika sudah tidak diperlukan lagi atau sesuaikan
    // User::where('email', 'sales@example.com')->delete();
  }
}
