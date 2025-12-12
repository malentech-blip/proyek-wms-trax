<?php

namespace Database\Factories\Admin\Production;

use App\Models\Admin\Production\WipRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Production\WipRecord>
 */
class WipRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = WipRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $number = str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return [
            'wip_no' => "WIP-{$year}-{$month}-{$number}",
            'mr_id' => \App\Models\Admin\Production\MaterialRequest::factory(),
            'started_at' => fake()->optional()->dateTimeBetween('-2 months', 'now'),
            'finished_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(['pending', 'started', 'paused', 'completed', 'cancelled']),
            'produced_qty' => fake()->numberBetween(0, 1000),
            'rejected_qty' => fake()->numberBetween(0, 100),
            'elapsed_seconds' => fake()->numberBetween(3600, 86400), // 1 hour to 24 hours
            'paused_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
