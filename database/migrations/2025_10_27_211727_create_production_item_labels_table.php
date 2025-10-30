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
        Schema::create('production_item_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId("item_id")->constrained("items");
            $table->unsignedInteger('quantity');
            $table->string('qr_code')->unique(); // Setiap label punya QR code unik
            $table->string('batch_no')->nullable();
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('rack_id')->constrained('racks');
            $table->foreignId('pallet_id')->constrained('pallets');
            $table->string('status')->default('stored');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_item_labels');
    }
};
