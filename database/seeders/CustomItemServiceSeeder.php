<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomItemServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create services for custom items
        $customItems = \App\Models\CustomItem::all();

        foreach ($customItems->take(100) as $customItem) {
            \App\Models\CustomItemService::factory()
                ->count(1)
                ->create([
                    'custom_item_id' => $customItem->id,
                ]);
        }
    }
}
