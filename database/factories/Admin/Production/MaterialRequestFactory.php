<?php

namespace Database\Factories\Admin\Production;

use App\Models\Admin\Production\MaterialRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Production\MaterialRequest>
 */
class MaterialRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = MaterialRequest::class;

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
            'mr_no' => "MR-{$year}-{$month}-{$number}",
            'so_id' => fake()->numberBetween(1000, 9999),
            'requested_by' => fake()->name(),
            'request_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'status' => fake()->randomElement(['pending', 'approved', 'in_progress', 'completed', 'cancelled']),
        ];
    }
}
