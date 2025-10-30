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
    Schema::table('finished_goods', function (Blueprint $table) {
      $table->foreignId('label_id')->constrained('production_item_labels');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('finished_goods', function (Blueprint $table) {
      $table->dropForeign(['label_id']);
    });
  }
};
