<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomItemService>
 */
class CustomItemServiceFactory extends Factory
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
            'service_sku' => 'SRV-' . fake()->numerify('######'),
            'service_name' => fake()->words(2, true) . ' Service',
            'cost' => fake()->randomFloat(2, 50, 5000),
            'quantity' => fake()->randomFloat(2, 0.1, 10),
        ];
    }
}
