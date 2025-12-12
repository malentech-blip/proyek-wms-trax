<?php

namespace Database\Seeders;

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
                'item_code' => '100009',
                'item_name' => 'Polybutylene',
                'item_type' => 'Raw Material',
                'uom' => 'KG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100008',
                'item_name' => 'Polycarbonat',
                'item_type' => 'Raw Material',
                'uom' => 'KG',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100007',
                'item_name' => 'Polypropylene',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100025',
                'item_name' => 'Hard Coating',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100026',
                'item_name' => 'Basecoat',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100024',
                'item_name' => 'Cat',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100010',
                'item_name' => 'Harness',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100011',
                'item_name' => 'Screw',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100012',
                'item_name' => 'Actuator',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100020',
                'item_name' => 'Adjuster',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100013',
                'item_name' => 'Cover',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100019',
                'item_name' => 'Grommet',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100027',
                'item_name' => 'Spring',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100014',
                'item_name' => 'Socket',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100015',
                'item_name' => 'Bulb Halogen (H4)',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100016',
                'item_name' => 'Bulb Umber',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100017',
                'item_name' => 'Bulb Senja (T10)',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100018',
                'item_name' => 'Pivot',
                'item_type' => 'Raw Material',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100005',
                'item_name' => 'Visor Yamaha Aerox',
                'item_type' => 'Finished Good',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_code' => '100004',
                'item_name' => 'Rear Lamp Honda Brio',
                'item_type' => 'Finished Good',
                'uom' => 'PCS',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
