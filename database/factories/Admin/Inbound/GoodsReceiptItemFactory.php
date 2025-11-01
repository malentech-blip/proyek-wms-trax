<?php

namespace Database\Factories\Admin\Inbound;

use App\Models\Admin\Inbound\GoodsReceiptItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Inbound\GoodsReceiptItem>
 */
class GoodsReceiptItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = GoodsReceiptItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $expectedQty = fake()->numberBetween(10, 500);
        $receivedQty = fake()->numberBetween($expectedQty * 0.9, $expectedQty * 1.1); // Received can vary ±10%

        return [
            'goods_receipt_id' => \App\Models\Admin\Inbound\GoodsReceipt::factory(),
            'item_name' => fake()->words(2, true).' Product',
            'item_code' => 'ITM-'.fake()->numerify('####'),
            'expected_qty' => $expectedQty,
            'received_qty' => $receivedQty,
        ];
    }
}
