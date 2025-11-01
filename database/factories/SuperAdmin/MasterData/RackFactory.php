<?php

namespace Database\Factories\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Rack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperAdmin\MasterData\Rack>
 */
class RackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Rack::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => \App\Models\SuperAdmin\MasterData\Location::factory(),
            'code' => 'RAK-'.fake()->unique()->numerify('###'),
        ];
    }
}
