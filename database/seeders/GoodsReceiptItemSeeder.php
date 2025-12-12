<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GoodsReceiptItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create goods receipt items for existing goods receipts
        $goodsReceipts = \App\Models\Admin\Inbound\GoodsReceipt::all();

        foreach ($goodsReceipts as $goodsReceipt) {
            \App\Models\Admin\Inbound\GoodsReceiptItem::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create([
                    'goods_receipt_id' => $goodsReceipt->id,
                ]);
        }
    }
}
