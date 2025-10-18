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
    Schema::create('picking_lists', function (Blueprint $table) {
      $table->id();
      $table->foreignId('mr_id')->constrained('material_requests')->cascadeOnDelete();
      $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
      $table->integer('quantity');
      $table->string('picked_by'); 
      $table->dateTime('date_picked')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('picking_lists');
  }
};
