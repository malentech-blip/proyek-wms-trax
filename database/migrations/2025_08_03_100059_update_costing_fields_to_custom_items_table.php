<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_items', function (Blueprint $table) {
            $table->decimal('production_cost', 15, 2)->default(0)->after('item_sku');
            $table->decimal('selling_price', 15, 2)->default(0)->after('production_cost');
            $table->decimal('overhead_percentage', 5, 2)->default(0)->after('selling_price');
            $table->decimal('profit_percentage', 5, 2)->default(0)->after('overhead_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_items', function (Blueprint $table) {
            // PENYESUAIAN: Melengkapi fungsi down()
            $table->dropColumn([
                'production_cost',
                'selling_price',
                'overhead_percentage',
                'profit_percentage'
            ]);
        });
    }
};