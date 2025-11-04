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
    // ... (Bagian atas kode Seeder yang sudah ada)

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

// --- Data Tambahan (20 item) ---

      // **10 Data Raw Material Tambahan**
      [
        'item_code' => 'RM-003',
        'item_name' => 'Kertas Kraft Roll',
        'item_type' => 'Raw Material',
        'uom' => 'KG',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-004',
        'item_name' => 'Kawat Tembaga Tipis',
        'item_type' => 'Raw Material',
        'uom' => 'M',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-005',
        'item_name' => 'Kain Katun Putih',
        'item_type' => 'Raw Material',
        'uom' => 'METER',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-006',
        'item_name' => 'Papan Kayu Jati',
        'item_type' => 'Raw Material',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-007',
        'item_name' => 'Pigmen Warna Merah (Bubuk)',
        'item_type' => 'Raw Material',
        'uom' => 'GR',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-008',
        'item_name' => 'Perekat Epoxy Cair',
        'item_type' => 'Raw Material',
        'uom' => 'LITER',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-009',
        'item_name' => 'Karet Lembaran Tebal',
        'item_type' => 'Raw Material',
        'uom' => 'M2',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-010',
        'item_name' => 'Komponen Elektronik Resistor',
        'item_type' => 'Raw Material',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-011',
        'item_name' => 'Minyak Pelumas Industri',
        'item_type' => 'Raw Material',
        'uom' => 'LITER',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'RM-012',
        'item_name' => 'Biji Kopi Arabika Mentah',
        'item_type' => 'Raw Material',
        'uom' => 'KG',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],

      // **10 Data Finished Good Tambahan**
      [
        'item_code' => 'FG-003',
        'item_name' => 'Kotak Karton Kemasan Medium',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-004',
        'item_name' => 'Kabel Listrik 2x1.5mm Roll 50m',
        'item_type' => 'Finished Good',
        'uom' => 'ROLL',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-005',
        'item_name' => 'Kaos Polos Pria Ukuran L',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-006',
        'item_name' => 'Meja Kerja Kayu Minimalis',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-007',
        'item_name' => 'Cat Tembok Interior 5L Merah',
        'item_type' => 'Finished Good',
        'uom' => 'KALENG',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-008',
        'item_name' => 'Set Kunci Pas Mekanik (10pcs)',
        'item_type' => 'Finished Good',
        'uom' => 'SET',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-009',
        'item_name' => 'Ban Mobil Ring 16 Standar',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-010',
        'item_name' => 'Papan Sirkuit Digital (PCB) A-1',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-011',
        'item_name' => 'Hand Sanitizer Gel 500ml',
        'item_type' => 'Finished Good',
        'uom' => 'BOTOL',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'item_code' => 'FG-012',
        'item_name' => 'Kopi Bubuk Arabika Siap Seduh 250g',
        'item_type' => 'Finished Good',
        'uom' => 'PCS',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
