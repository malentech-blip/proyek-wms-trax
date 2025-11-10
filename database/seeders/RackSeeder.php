<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RackSeeder extends Seeder
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
            DB::table('racks')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use TRUNCATE TABLE racks RESTART IDENTITY CASCADE;
            DB::statement('TRUNCATE TABLE racks RESTART IDENTITY CASCADE;');
        } else {
            DB::table('racks')->truncate();
        }

        DB::table('racks')->insert([
            // --- LOCATION ID 1: Gudang Utama/General ---
            // ID 1
            [
                'code' => 'A-01',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 2
            [
                'code' => 'A-02',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 3 (Kebutuhan dari Pallet ID 3 di seeder sebelumnya, tapi kita anggap sebagai A-03 sekarang)
            [
                'code' => 'A-03',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 4
            [
                'code' => 'D-01',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 5
            [
                'code' => 'D-02',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 6
            [
                'code' => 'E-01',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 7
            [
                'code' => 'E-02',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 8
            [
                'code' => 'F-01',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 9
            [
                'code' => 'F-02',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 10
            [
                'code' => 'H-01',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 11
            [
                'code' => 'H-02',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 12
            [
                'code' => 'A-04',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 13
            [
                'code' => 'F-03',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 14
            [
                'code' => 'E-03',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 15
            [
                'code' => 'D-03',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],


            // --- LOCATION ID 2: Gudang Khusus/Textile ---
            // ID 16
            [
                'code' => 'B-01',
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 17
            [
                'code' => 'G-01',
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 18
            [
                'code' => 'G-02',
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 19
            [
                'code' => 'C-01',
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ID 20
            [
                'code' => 'C-02',
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}