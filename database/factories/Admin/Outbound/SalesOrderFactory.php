<?php

namespace Database\Factories\Admin\Outbound;

use App\Models\Admin\Outbound\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Outbound\SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = SalesOrder::class;

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
            'so_number' => "SO-{$year}-{$month}-{$number}",
            'status' => fake()->randomElement(['PENDING', 'PROCESSING', 'COMPLETED', 'CANCELLED']),
            'sync_status' => fake()->randomElement(['ON GOING', 'SYNCED', 'FAILED']),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
