<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 100 inventory records using factories
        $items = \App\Models\SuperAdmin\MasterData\Item::all();
        $locations = \App\Models\SuperAdmin\MasterData\Location::all();

        // Create inventory records
        for ($i = 0; $i < 100; $i++) {
            \App\Models\Admin\Inventory\Inventory::factory()->create([
                'item_id' => $items->random()->id ?? \App\Models\SuperAdmin\MasterData\Item::factory()->create()->id,
                'location_id' => $locations->random()->id ?? \App\Models\SuperAdmin\MasterData\Location::factory()->create()->id,
            ]);
        }
    }
}
