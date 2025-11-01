<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin\MasterData\Location;
use App\Models\SuperAdmin\MasterData\Rack;
use App\Models\SuperAdmin\MasterData\Pallet;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Handle truncation based on database driver
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Location::truncate();
            Rack::truncate();
            Pallet::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: Use TRUNCATE CASCADE to handle foreign key constraints
            DB::statement('TRUNCATE TABLE locations, racks, pallets RESTART IDENTITY CASCADE;');
        } else {
            // For other databases, use standard truncate
            Location::truncate();
            Rack::truncate();
            Pallet::truncate();
        }

        $gudangA = Location::create(['name' => 'Gudang A', 'code' => 'GA']);
        $gudangB = Location::create(['name' => 'Gudang B', 'code' => 'GB']);

        $rak1 = $gudangA->racks()->create(['code' => 'RAK-01']);
        $rak2 = $gudangB->racks()->create(['code' => 'RAK-02']);

        $rak1->pallets()->create(['code' => 'PLT-001', 'capacity' => '500 Kg']);
        $rak2->pallets()->create(['code' => 'PLT-002', 'capacity' => '400 Kg']);
    }
}
