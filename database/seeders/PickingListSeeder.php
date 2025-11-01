<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PickingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create picking lists for existing material requests
        $materialRequests = \App\Models\Admin\Production\MaterialRequest::all();
        $items = \App\Models\SuperAdmin\MasterData\Item::all();

        foreach ($materialRequests->take(100) as $mr) {
            \App\Models\Admin\Production\PickingList::factory()
                ->count(1)
                ->create([
                    'mr_id' => $mr->id,
                    'item_id' => $items->random()->id,
                ]);
        }
    }
}
