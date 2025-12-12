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
    Schema::table('item_labels', function (Blueprint $table) {
        // Ubah kolom menjadi nullable (boleh kosong)
        $table->foreignId('rack_id')->nullable()->change();
        $table->foreignId('pallet_id')->nullable()->change();
    });
}

public function down(): void
{
    // Kembalikan ke required (jika perlu rollback)
    Schema::table('item_labels', function (Blueprint $table) {
        $table->foreignId('rack_id')->nullable(false)->change();
        $table->foreignId('pallet_id')->nullable(false)->change();
    });
}
};
