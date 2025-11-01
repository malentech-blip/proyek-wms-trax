<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class QuotationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create quotation items for quotations
        $quotations = \App\Models\Quotation::all();

        foreach ($quotations->take(100) as $quotation) {
            \App\Models\QuotationItem::factory()
                ->count(1)
                ->create([
                    'quotation_id' => $quotation->id,
                ]);
        }
    }
}
