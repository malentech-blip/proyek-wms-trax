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
        // Handle truncation based on database driver
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('inventories')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use TRUNCATE TABLE inventories RESTART IDENTITY CASCADE;
            DB::statement('TRUNCATE TABLE inventories RESTART IDENTITY CASCADE;');
        } else {
            DB::table('inventories')->truncate();
        }

        /**
         * Data agregat Inventory berdasarkan ItemLabelsSeeder.
         * Diasumsikan item_id adalah ID dari tabel 'items' (1-20).
         */
        DB::table('inventories')->insert([
            // --- RAW MATERIAL (RM) - Lokasi 1 (Gudang Utama) ---
            [
                'item_id' => 1, // RM-001 Besi Batangan
                'location_id' => 1,
                'quantity' => 850, // 500 + 350
                'updated_at' => now(),
            ],
            [
                'item_id' => 2, // RM-002 Plastik Granul
                'location_id' => 1,
                'quantity' => 1200,
                'updated_at' => now(),
            ],
            [
                'item_id' => 4, // RM-004 Kawat Tembaga Tipis
                'location_id' => 1,
                'quantity' => 200,
                'updated_at' => now(),
            ],
            [
                'item_id' => 7, // RM-007 Pigmen Warna Merah
                'location_id' => 1,
                'quantity' => 25,
                'updated_at' => now(),
            ],
            [
                'item_id' => 10, // RM-010 Komponen Resistor
                'location_id' => 1,
                'quantity' => 5000,
                'updated_at' => now(),
            ],
            [
                'item_id' => 11, // RM-011 Minyak Pelumas
                'location_id' => 1,
                'quantity' => 50,
                'updated_at' => now(),
            ],

            // --- RAW MATERIAL (RM) - Lokasi 2 (Gudang Khusus) ---
            [
                'item_id' => 5, // RM-005 Kain Katun Putih
                'location_id' => 2,
                'quantity' => 850,
                'updated_at' => now(),
            ],
            [
                'item_id' => 6, // RM-006 Papan Kayu Jati
                'location_id' => 2,
                'quantity' => 150,
                'updated_at' => now(),
            ],
            [
                'item_id' => 9, // RM-009 Karet Lembaran Tebal
                'location_id' => 2,
                'quantity' => 200,
                'updated_at' => now(),
            ],

            // --- FINISHED GOOD (FG) - Lokasi 1 (Gudang Utama) ---
            [
                'item_id' => 13, // FG-001 Botol Air Mineral
                'location_id' => 1,
                'quantity' => 2400, // 1500 + 900
                'updated_at' => now(),
            ],
            [
                'item_id' => 15, // FG-003 Kotak Karton
                'location_id' => 1,
                'quantity' => 800,
                'updated_at' => now(),
            ],
            [
                'item_id' => 16, // FG-004 Kabel Listrik
                'location_id' => 1,
                'quantity' => 125, // 50 + 75
                'updated_at' => now(),
            ],
            [
                'item_id' => 20, // FG-008 Set Kunci Pas
                'location_id' => 1,
                'quantity' => 100,
                'updated_at' => now(),
            ],
            
            // --- FINISHED GOOD (FG) - Lokasi 2 (Gudang Khusus) ---
            [
                'item_id' => 17, // FG-005 Kaos Polos Pria
                'location_id' => 2,
                'quantity' => 450, // 300 + 150
                'updated_at' => now(),
            ],
            [
                'item_id' => 18, // FG-006 Meja Kerja
                'location_id' => 2,
                'quantity' => 30,
                'updated_at' => now(),
            ],
            [
                'item_id' => 19, // FG-007 Cat Tembok
                'location_id' => 2,
                'quantity' => 80,
                'updated_at' => now(),
            ],
        ]);
    }
}