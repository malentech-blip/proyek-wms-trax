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
        Schema::create('outbound_packing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_id')->constrained("packing_lists");
            $table->foreignId('fg_id')->constrained('finished_goods');
            $table->unsignedBigInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_packing_items');
    }
};
