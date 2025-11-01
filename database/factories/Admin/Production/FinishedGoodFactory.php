<?php

namespace Database\Factories\Admin\Production;

use App\Models\Admin\Production\FinishedGood;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Production\FinishedGood>
 */
class FinishedGoodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = FinishedGood::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wip_id' => \App\Models\Admin\Production\WipRecord::factory(),
            'item_id' => \App\Models\SuperAdmin\MasterData\Item::factory(),
            'quantity' => fake()->numberBetween(1, 500),
            'qc_status' => fake()->randomElement(['pending', 'passed', 'failed', 'rework']),
            'stored_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'label_id' => \App\Models\Admin\Production\ProductionItemLabel::factory(),
        ];
    }
}
