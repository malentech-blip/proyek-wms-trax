<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create stock movements for items
        $items = \App\Models\SuperAdmin\MasterData\Item::all();
        $locations = \App\Models\SuperAdmin\MasterData\Location::all();

        foreach ($items->take(100) as $item) {
            \App\Models\Admin\Inventory\StockMovement::factory()
                ->count(1)
                ->create([
                    'item_id' => $item->id,
                    'from_location' => $locations->random()->code ?? 'GA',
                    'to_location' => $locations->random()->code ?? 'GB',
                ]);
        }
    }
}
