<?php

namespace Database\Factories\Admin\Inbound;

use App\Models\Admin\Inbound\GoodsReceipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Inbound\GoodsReceipt>
 */
class GoodsReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = GoodsReceipt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        // Use a combination of unique number and random to ensure uniqueness
        $number = str_pad(fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT);

        return [
            'receipt_number' => "GR-{$year}-{$month}-{$number}",
            'po_number' => 'PO-'.fake()->numerify('######'),
            'supplier_id' => \App\Models\SuperAdmin\MasterData\Supplier::factory(),
            'received_by_id' => \App\Models\User::factory(),
            'receipt_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'status' => fake()->randomElement(['pending', 'received', 'qc_pending', 'qc_passed', 'qc_failed', 'putaway_completed']),
        ];
    }
}
