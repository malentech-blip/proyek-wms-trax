<?php

namespace Database\Factories\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperAdmin\MasterData\Item>
 */
class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemTypes = ['Raw Material', 'Finished Good', 'Semi Finished'];
        $uoms = ['KG', 'PCS', 'BOX', 'CARTON', 'PALLET', 'METER', 'LITER'];

        return [
            'item_code' => 'ITM-'.fake()->unique()->numerify('####'),
            'item_name' => fake()->words(2, true).' '.fake()->randomElement(['Material', 'Product', 'Item']),
            'item_type' => fake()->randomElement($itemTypes),
            'uom' => fake()->randomElement($uoms),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }
}
