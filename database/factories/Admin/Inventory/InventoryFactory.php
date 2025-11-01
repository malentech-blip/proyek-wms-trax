<?php

namespace Database\Factories\Admin\Inventory;

use App\Models\Admin\Inventory\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Inventory\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => \App\Models\SuperAdmin\MasterData\Item::factory(),
            'location_id' => \App\Models\SuperAdmin\MasterData\Location::factory(),
            'quantity' => fake()->numberBetween(0, 10000),
        ];
    }
}
