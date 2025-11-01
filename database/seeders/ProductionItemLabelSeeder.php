<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionItemLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create production item labels for finished goods items
        $items = \App\Models\SuperAdmin\MasterData\Item::where('item_type', 'Finished Good')->get();
        $locations = \App\Models\SuperAdmin\MasterData\Location::all();
        $racks = \App\Models\SuperAdmin\MasterData\Rack::all();
        $pallets = \App\Models\SuperAdmin\MasterData\Pallet::all();

        foreach ($items->take(100) as $item) {
            \App\Models\Admin\Production\ProductionItemLabel::factory()
                ->count(1)
                ->create([
                    'item_id' => $item->id,
                    'location_id' => $locations->random()->id ?? \App\Models\SuperAdmin\MasterData\Location::factory()->create()->id,
                    'rack_id' => $racks->random()->id ?? \App\Models\SuperAdmin\MasterData\Rack::factory()->create()->id,
                    'pallet_id' => $pallets->random()->id ?? \App\Models\SuperAdmin\MasterData\Pallet::factory()->create()->id,
                ]);
        }
    }
}
