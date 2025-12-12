<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            // Rename dulu
            $table->renameColumn('so_id', 'wo_no');
        });

        Schema::table('material_requests', function (Blueprint $table) {
            // Lalu ubah tipe
            $table->string('wo_no')->change();
        });
    }

    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            // Balikin tipe ke aslinya (misalnya integer, silakan sesuaikan)
            $table->integer('wo_no')->change();
        });

        Schema::table('material_requests', function (Blueprint $table) {
            $table->renameColumn('wo_no', 'so_id');
        });
    }
};
