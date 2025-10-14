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
    Schema::create('custom_item_manual_costs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('custom_item_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->decimal('cost', 15, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_item_manual_costs');
    }
};
