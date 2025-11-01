<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Handle truncation based on database driver
    $driver = DB::getDriverName();

    if ($driver === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    } elseif ($driver === 'pgsql') {
        // PostgreSQL: Use TRUNCATE CASCADE to handle foreign key constraints
        DB::statement('TRUNCATE TABLE items RESTART IDENTITY CASCADE;');
    } else {
        DB::table('items')->truncate();
    }
    DB::table('items')->insert([
      [
        'item_code' => 'RM-001',
        'item_name' => 'Besi Batangan',
        'item_type' => 'Raw Material',
        'uom' => 'KG',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-002',
        'item_name' => 'Plastik Granul',
        'item_type' => 'Raw Material',
        'uom' => 'KG',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-001',
        'item_name' => 'Botol Air Mineral 600ml',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-002',
        'item_name' => 'Tutup Botol Biru',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
