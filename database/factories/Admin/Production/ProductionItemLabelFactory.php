<?php

namespace Database\Factories\Admin\Production;

use App\Models\Admin\Production\ProductionItemLabel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Production\ProductionItemLabel>
 */
class ProductionItemLabelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ProductionItemLabel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => \App\Models\SuperAdmin\MasterData\Item::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'qr_code' => fake()->unique()->uuid(),
            'batch_no' => 'BATCH-'.fake()->date('Ymd').'-'.fake()->numerify('###'),
            'location_id' => \App\Models\SuperAdmin\MasterData\Location::factory(),
            'rack_id' => \App\Models\SuperAdmin\MasterData\Rack::factory(),
            'pallet_id' => \App\Models\SuperAdmin\MasterData\Pallet::factory(),
            'status' => fake()->randomElement(['in_stock', 'reserved', 'picked', 'shipped', 'produced']),
        ];
    }
}
