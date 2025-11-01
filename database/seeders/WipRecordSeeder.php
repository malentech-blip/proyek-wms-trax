<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WipRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create WIP records for material requests with approved status
        $materialRequests = \App\Models\Admin\Production\MaterialRequest::where('status', 'approved')
            ->orWhere('status', 'in_progress')
            ->get();

        foreach ($materialRequests->take(100) as $mr) {
            \App\Models\Admin\Production\WipRecord::factory()->create([
                'mr_id' => $mr->id,
            ]);
        }
    }
}
