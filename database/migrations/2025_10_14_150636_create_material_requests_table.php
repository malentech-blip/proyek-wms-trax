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
    Schema::create('material_requests', function (Blueprint $table) {
      $table->id();
      $table->string("mr_no")->unique();
      $table->unsignedBigInteger('so_id'); 
      $table->string('requested_by');
      $table->date('request_date');
      $table->string('status'); //Requested - Picked - Done
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('material_requests');
  }
};
