<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemMaterial>
 */
class ItemMaterialFactory extends Factory
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
            'material_sku' => 'MAT-' . fake()->numerify('######'),
            'quantity' => fake()->randomFloat(2, 0.1, 100),
        ];
    }
}
