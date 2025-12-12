<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PalletSeeder extends Seeder
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
            DB::table('pallets')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use TRUNCATE TABLE pallets RESTART IDENTITY CASCADE;
            DB::statement('TRUNCATE TABLE pallets RESTART IDENTITY CASCADE;');
        } else {
            DB::table('pallets')->truncate();
        }

        /**
         * Pallet ID 1-20
         * rack_id di sini harus merujuk ke ID dari RackSeeder di atas.
         */
        DB::table('pallets')->insert([
            // Pallet ID 1-10
            [
                'rack_id' => 1,  // A-01
                'code' => 'P001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 2,  // A-02
                'code' => 'P002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 16,  // B-01 (Dari Kain Katun)
                'code' => 'P003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 4,  // D-01 (Dari Pigmen Merah)
                'code' => 'P004',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 5,  // D-02 (Dari Resistor)
                'code' => 'P005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 6,  // E-01 (Dari Botol Mineral)
                'code' => 'P006',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 7,  // E-02 (Dari Kotak Karton)
                'code' => 'P007',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 8,  // F-01 (Dari Kabel Listrik)
                'code' => 'P008',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 17,  // G-01 (Dari Kaos Polos)
                'code' => 'P009',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 10,  // H-01 (Dari Meja Kerja - Dipindah ke FG)
                'code' => 'P010',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pallet ID 11-20
            [
                'rack_id' => 1,  // A-01 (Batch Besi Baru)
                'code' => 'P011',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 18,  // G-02 (Batch Kaos QC)
                'code' => 'P012',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 8,  // F-01 (Batch Kabel Lama)
                'code' => 'P013',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 19,  // C-01 (Dari Papan Kayu Jati)
                'code' => 'P014',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 20,  // C-02 (Dari Karet Lembaran)
                'code' => 'P015',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 9,  // F-02 (Dari Set Kunci Pas)
                'code' => 'P016',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 11,  // H-02 (Dari Cat Tembok)
                'code' => 'P017',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 6,  // E-01 (Batch Botol Holding)
                'code' => 'P018',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 4,  // D-01 (Dari Minyak Pelumas)
                'code' => 'P019',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rack_id' => 5,  // D-02 (Dari Kawat Tembaga)
                'code' => 'P020',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}