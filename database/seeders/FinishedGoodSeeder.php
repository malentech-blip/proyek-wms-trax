<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FinishedGoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create finished goods for completed WIP records
        $wipRecords = \App\Models\Admin\Production\WipRecord::where('status', 'completed')->get();
        $items = \App\Models\SuperAdmin\MasterData\Item::where('item_type', 'Finished Good')->get();

        foreach ($wipRecords->take(100) as $wip) {
            \App\Models\Admin\Production\FinishedGood::factory()
                ->count(1)
                ->create([
                    'wip_id' => $wip->id,
                    'item_id' => $items->random()->id ?? \App\Models\SuperAdmin\MasterData\Item::factory()->create()->id,
                ]);
        }
    }
}
