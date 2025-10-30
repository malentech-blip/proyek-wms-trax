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
    Schema::create('finished_goods', function (Blueprint $table) {
      $table->id();
      $table->foreignId('wip_id')->constrained('wip_records');
      $table->foreignId('item_id')->constrained('items');
      $table->unsignedInteger('quantity');
      $table->string('qc_status');
      $table->dateTime('stored_at')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('finished_goods');
  }
};
