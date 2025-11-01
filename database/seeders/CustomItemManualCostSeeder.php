<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomItemManualCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create manual costs for custom items
        $customItems = \App\Models\CustomItem::all();

        foreach ($customItems->take(100) as $customItem) {
            \App\Models\CustomItemManualCost::factory()
                ->count(1)
                ->create([
                    'custom_item_id' => $customItem->id,
                ]);
        }
    }
}
