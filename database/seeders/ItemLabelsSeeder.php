<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemLabelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 100 item labels using factories
        $goodsReceiptItems = \App\Models\Admin\Inbound\GoodsReceiptItem::all();
        $locations = \App\Models\SuperAdmin\MasterData\Location::all();
        $racks = \App\Models\SuperAdmin\MasterData\Rack::all();
        $pallets = \App\Models\SuperAdmin\MasterData\Pallet::all();

        // Create item labels
        for ($i = 0; $i < 100; $i++) {
            \App\Models\Admin\Inbound\ItemLabel::factory()->create([
                'goods_receipt_item_id' => $goodsReceiptItems->random()->id ?? \App\Models\Admin\Inbound\GoodsReceiptItem::factory()->create()->id,
                'location_id' => $locations->random()->id ?? \App\Models\SuperAdmin\MasterData\Location::factory()->create()->id,
                'rack_id' => $racks->random()->id ?? \App\Models\SuperAdmin\MasterData\Rack::factory()->create()->id,
                'pallet_id' => $pallets->random()->id ?? \App\Models\SuperAdmin\MasterData\Pallet::factory()->create()->id,
            ]);
        }
    }
}
