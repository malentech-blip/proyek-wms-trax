<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create item materials for custom items
        $customItems = \App\Models\CustomItem::all();

        foreach ($customItems->take(100) as $customItem) {
            \App\Models\ItemMaterial::factory()
                ->count(1)
                ->create([
                    'custom_item_id' => $customItem->id,
                ]);
        }
    }
}
