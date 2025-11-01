<?php

namespace Database\Factories\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperAdmin\MasterData\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'pic' => fake()->name(), // Person in Charge
            'is_active' => fake()->boolean(85), // 85% chance of being active
        ];
    }
}
