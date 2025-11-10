<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // First: Roles and Permissions (required for users)
            RolesAndPermissionsSeeder::class,

            // Second: Master Data (locations, items, suppliers, customers)
            LocationSeeder::class,
            ItemsSeeder::class, // Initial hardcoded items
            ItemSeeder::class, // Additional factory-generated items
            SupplierSeeder::class,
            CustomerSeeder::class,

            // Third: Custom Items and related data
            CustomItemSeeder::class,
            ItemMaterialSeeder::class,
            CustomItemManualCostSeeder::class,
            CustomItemServiceSeeder::class,

            // Fifth: Quotations
            QuotationSeeder::class,
            QuotationItemSeeder::class,

            // Sixth: Goods Receipts and Items
            GoodsReceiptSeeder::class,
            GoodsReceiptItemSeeder::class,

            // Seventh: Item Labels (requires goods receipt items)
            ItemLabelsSeeder::class,

            // Eighth: Inventory (requires items and locations)
            InventoriesSeeder::class,

            // Ninth: Material Requests
            MaterialRequestSeeder::class,

            // Tenth: Picking Lists (requires material requests and items)
            PickingListSeeder::class,

            // Eleventh: WIP Records (requires material requests)
            // WipRecordSeeder::class,

            // Twelfth: Finished Goods (requires WIP records and items)
            FinishedGoodSeeder::class,

            // Thirteenth: Production Item Labels
            ProductionItemLabelSeeder::class,

            // Fourteenth: Reject Production (requires WIP records)
            RejectProductionSeeder::class,

            // Fifteenth: Stock Movements (requires items)
            StockMovementSeeder::class,

            // Last: Audit Logs (requires users)
            AuditLogSeeder::class,
        ]);
    }
}
