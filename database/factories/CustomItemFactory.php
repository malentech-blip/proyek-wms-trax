<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomItem>
 */
class CustomItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_name' => fake()->words(2, true) . ' Product',
            'item_sku' => 'SKU-' . fake()->unique()->numerify('######'),
            'production_cost' => fake()->randomFloat(2, 100, 10000),
            'selling_price' => fake()->randomFloat(2, 150, 15000),
            'overhead_percentage' => fake()->randomFloat(2, 5, 20),
            'profit_percentage' => fake()->randomFloat(2, 10, 50),
        ];
    }
}
