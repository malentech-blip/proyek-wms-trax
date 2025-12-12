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
        Schema::create('manufacture_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("wip_id")->constrained("wip_records");
            $table->decimal("raw_material_cost", 15, 2);
            $table->decimal("labor_cost", 15, 2)->default(0);
            $table->decimal("overhead_cost", 15, 2)->default(0);
            $table->decimal("total_cost", 15, 2);
            $table-> decimal("hpp_actual_per_unit", 15, 2);
            $table->string("variance_nominal")->default("0");
            $table->decimal("variance_percentage", 8, 2)->default(0);
            $table->string("variance_status")->default("On Budget");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacture_costs');
    }
};
