<?php

namespace Database\Factories\Admin\Outbound;

use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\PackingList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Outbound\DeliveryOrder>
 */
class DeliveryOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = DeliveryOrder::class;

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
            'packing_list_id' => PackingList::factory(),
            'delivered_no' => "DO-{$year}-{$month}-{$number}",
            'driver_name' => fake()->name(),
            'delivery_date' => fake()->optional()->dateTimeBetween('-1 month', '+1 week'),
            'status' => fake()->randomElement(['In Delivery', 'Delivered']),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}
