<?php

namespace Database\Factories\Admin\Outbound;

use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Outbound\PackingList>
 */
class PackingListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = PackingList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'so_id' => SalesOrder::factory(),
            'packed_by' => fake()->name(),
            'packed_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
            'status' => fake()->randomElement(['WIP', 'COMPLETED', 'CANCELLED']),
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
