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
    Schema::create('wip_records', function (Blueprint $table) {
      $table->id();
      $table->foreignId('mr_id')->constrained('material_requests')->cascadeOnDelete();
      $table->dateTime('started_at')->nullable();
      $table->dateTime('finished_at')->nullable();
      $table->string('result_status');
      $table->unsignedInteger('produced_qty')->default(0);
      $table->unsignedInteger('rejected_qty')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('wip_records');
  }
};
