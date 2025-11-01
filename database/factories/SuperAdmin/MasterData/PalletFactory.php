<?php

namespace Database\Factories\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Pallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperAdmin\MasterData\Pallet>
 */
class PalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Pallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rack_id' => \App\Models\SuperAdmin\MasterData\Rack::factory(),
            'code' => 'PLT-'.fake()->unique()->numerify('###'),
            'capacity' => fake()->randomElement(['200 Kg', '300 Kg', '400 Kg', '500 Kg', '1000 Kg']),
        ];
    }
}
