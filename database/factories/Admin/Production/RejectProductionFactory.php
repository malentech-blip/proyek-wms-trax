<?php

namespace Database\Factories\Admin\Production;

use App\Models\Admin\Production\RejectProduction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Production\RejectProduction>
 */
class RejectProductionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = RejectProduction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wip_id' => \App\Models\Admin\Production\WipRecord::factory(),
            'reason' => fake()->sentence(),
            'action' => fake()->randomElement(['rework', 'scrap']),
            'handled_by' => fake()->name(),
            'date' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
