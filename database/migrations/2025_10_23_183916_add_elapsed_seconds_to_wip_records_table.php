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
    Schema::table('wip_records', function (Blueprint $table) {
      $table->unsignedBigInteger('elapsed_seconds')->default(0); // durasi total (dalam detik)
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('wip_records', function (Blueprint $table) {
      $table->dropColumn('elapsed_seconds');
    });
  }
};
