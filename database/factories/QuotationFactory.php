<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quotation>
 */
class QuotationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'accurate_customer_id' => fake()->numberBetween(1, 1000),
            'customer_no' => 'CUST-'.fake()->numerify('######'),
            'company_name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['draft', 'sent', 'approved', 'rejected', 'expired']),
        ];
    }
}
