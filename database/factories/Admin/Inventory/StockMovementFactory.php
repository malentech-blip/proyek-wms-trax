<?php

namespace Database\Factories\Admin\Inventory;

use App\Models\Admin\Inventory\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Inventory\StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = StockMovement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $movementTypes = ['transfer', 'receipt', 'shipment', 'adjustment', 'production', 'consumption'];

        return [
            'item_id' => \App\Models\SuperAdmin\MasterData\Item::factory(),
            'quantity' => fake()->numberBetween(1, 1000),
            'from_location' => fake()->optional()->regexify('[A-Z]{2,3}'),
            'to_location' => fake()->optional()->regexify('[A-Z]{2,3}'),
            'moved_by' => fake()->name(),
            'movement_type' => fake()->randomElement($movementTypes),
            'date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
