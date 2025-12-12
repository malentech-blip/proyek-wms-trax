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
    Schema::create('rejects_production', function (Blueprint $table) {
      $table->id();
      $table->foreignId('wip_id')->constrained('wip_records');
      $table->text('reason');
      $table->enum('action', ['rework', 'scrap']);
      $table->string('handled_by');
      $table->dateTime('date');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('rejects_production');
  }
};
