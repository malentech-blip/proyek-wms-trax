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
        Schema::create('transit_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packing_id')->constrained("packing_lists");
            $table->dateTime('scanned_at')->nullable();
            $table->string('scanned_by')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transit_inventories');
    }
};
