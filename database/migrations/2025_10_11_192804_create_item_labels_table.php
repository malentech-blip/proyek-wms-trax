<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_item_id')->constrained('goods_receipt_items');
            $table->string('item_code');
            $table->string('item_name');
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

    public function down(): void
    {
        Schema::dropIfExists('item_labels');
    }
};