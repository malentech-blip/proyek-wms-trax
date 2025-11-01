<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RejectProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create reject production records for WIP records
        $wipRecords = \App\Models\Admin\Production\WipRecord::all();

        foreach ($wipRecords->take(100) as $wip) {
            \App\Models\Admin\Production\RejectProduction::factory()
                ->count(1)
                ->create([
                    'wip_id' => $wip->id,
                ]);
        }
    }
}
