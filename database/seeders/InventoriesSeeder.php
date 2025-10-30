<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class InventoriesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('inventories')->truncate();
    DB::table('inventories')->insert([
      [
        'item_id' => 1, // RM-001 - Besi Batangan
        'location_id' => 1,
        'quantity' => 100,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_id' => 2, // RM-002 - Plastik Granul
        'location_id' => 1,
        'quantity' => 200,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_id' => 3, // FG-001 - Botol Air Mineral 600ml
        'location_id' => 2,
        'quantity' => 500,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_id' => 4, // FG-002 - Tutup Botol Biru
        'location_id' => 2,
        'quantity' => 300,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
