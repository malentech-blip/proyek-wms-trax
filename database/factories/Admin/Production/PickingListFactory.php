<?php

namespace Database\Factories\Admin\Production;

use App\Models\Admin\Production\PickingList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Production\PickingList>
 */
class PickingListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = PickingList::class;

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
            'pl_no' => "PL-{$year}-{$month}-{$number}",
            'mr_id' => \App\Models\Admin\Production\MaterialRequest::factory(),
            'item_id' => \App\Models\SuperAdmin\MasterData\Item::factory(),
            'quantity' => fake()->numberBetween(1, 500),
            'picked_by' => fake()->name(),
            'date_picked' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
