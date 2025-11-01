<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomItemManualCost>
 */
class CustomItemManualCostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'custom_item_id' => \App\Models\CustomItem::factory(),
            'name' => fake()->words(2, true) . ' Cost',
            'cost' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
