<?php

namespace Database\Factories\Admin\Inbound;

use App\Models\Admin\Inbound\ItemLabel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Inbound\ItemLabel>
 */
class ItemLabelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ItemLabel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'goods_receipt_item_id' => \App\Models\Admin\Inbound\GoodsReceiptItem::factory(),
            'item_code' => 'ITM-'.fake()->numerify('####'),
            'item_name' => fake()->words(2, true).' Product',
            'quantity' => fake()->numberBetween(1, 100),
            'qr_code' => fake()->unique()->uuid(),
            'batch_no' => 'BATCH-'.fake()->date('Ymd').'-'.fake()->numerify('###'),
            'location_id' => \App\Models\SuperAdmin\MasterData\Location::factory(),
            'rack_id' => \App\Models\SuperAdmin\MasterData\Rack::factory(),
            'pallet_id' => \App\Models\SuperAdmin\MasterData\Pallet::factory(),
            'status' => fake()->randomElement(['in_stock', 'reserved', 'picked', 'shipped']),
        ];
    }
}
