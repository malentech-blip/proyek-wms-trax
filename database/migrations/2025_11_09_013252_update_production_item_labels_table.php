<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('production_item_labels', function (Blueprint $table) {
      $table->dropColumn('qr_code');
      $table->string('barcode')->unique()->after('id');
    });
  }

  public function down(): void
  {
    Schema::table('production_item_labels', function (Blueprint $table) {
      $table->dropColumn('barcode');
      $table->string('qr_code')->unique()->after('id');
    });
  }
};
