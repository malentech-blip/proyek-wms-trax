<?php

namespace Database\Factories\SuperAdmin\MasterData;

use App\Models\SuperAdmin\MasterData\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SuperAdmin\MasterData\Location>
 */
class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locations = ['Gudang A', 'Gudang B', 'Gudang C', 'Storage Area 1', 'Storage Area 2', 'Warehouse Main', 'Cold Storage'];

        return [
            'name' => fake()->randomElement($locations),
            'code' => fake()->unique()->regexify('[A-Z]{2,3}'),
        ];
    }
}
