<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GoodsReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = \App\Models\SuperAdmin\MasterData\Supplier::all();
        $users = \App\Models\User::all();

        \App\Models\Admin\Inbound\GoodsReceipt::factory()
            ->count(100)
            ->create([
                'supplier_id' => $suppliers->random()->id ?? \App\Models\SuperAdmin\MasterData\Supplier::factory()->create()->id,
                'received_by_id' => $users->random()->id ?? \App\Models\User::factory()->create()->id,
            ]);
    }
}
