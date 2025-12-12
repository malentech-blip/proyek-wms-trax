<?php

namespace Database\Seeders;

use App\Models\Admin\Outbound\DeliveryOrder;
use App\Models\Admin\Outbound\PackingList;
use App\Models\Admin\Outbound\SalesOrder;
use Illuminate\Database\Seeder;

class OutboundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 Sales Orders with different statuses
        $salesOrders = SalesOrder::factory()->count(10)->create();

        // Create 15 Packing Lists - some with WIP status for dashboard testing
        $packingLists = PackingList::factory()->count(5)->create([
            'status' => 'WIP',
        ]);

        // Create more Packing Lists with different statuses
        foreach ($salesOrders->take(10) as $salesOrder) {
            PackingList::factory()->count(1)->create([
                'so_id' => $salesOrder->id,
                'status' => fake()->randomElement(['WIP', 'COMPLETED', 'CANCELLED']),
            ]);
        }

        // Create Delivery Orders - some with "In Delivery" status
        $inDeliveryCount = 0;
        foreach ($packingLists as $packingList) {
            if ($inDeliveryCount < 3) {
                DeliveryOrder::factory()->create([
                    'packing_list_id' => $packingList->id,
                    'status' => 'In Delivery',
                    'delivery_date' => fake()->dateTimeBetween('now', '+1 week'),
                ]);
                $inDeliveryCount++;
            } else {
                DeliveryOrder::factory()->create([
                    'packing_list_id' => $packingList->id,
                    'status' => fake()->randomElement(['In Delivery', 'Delivered']),
                ]);
            }
        }

        // Create some Delivery Orders with today's date for dashboard testing
        $allPackingLists = PackingList::all();
        foreach ($allPackingLists->take(3) as $packingList) {
            DeliveryOrder::factory()->create([
                'packing_list_id' => $packingList->id,
                'delivery_date' => today()->setTime(fake()->numberBetween(8, 18), fake()->numberBetween(0, 59)),
                'status' => fake()->randomElement(['In Delivery', 'Delivered']),
            ]);
        }

        // Create more Delivery Orders with various statuses for testing
        foreach ($allPackingLists->take(10) as $packingList) {
            DeliveryOrder::factory()->count(1)->create([
                'packing_list_id' => $packingList->id,
            ]);
        }

        // Ensure we have some PENDING Sales Orders for dashboard
        SalesOrder::factory()->count(3)->create([
            'status' => 'PENDING',
        ]);

        // Ensure we have more WIP Packing Lists for dashboard
        $pendingSalesOrders = SalesOrder::where('status', 'PENDING')->get();
        foreach ($pendingSalesOrders as $salesOrder) {
            PackingList::factory()->count(2)->create([
                'so_id' => $salesOrder->id,
                'status' => 'WIP',
            ]);
        }
    }
}
