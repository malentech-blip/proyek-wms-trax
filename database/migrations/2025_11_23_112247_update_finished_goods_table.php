<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finished_goods', function (Blueprint $table) {
            // Drop FK dulu
            $table->dropForeign(['label_id']);
        });

        Schema::table('finished_goods', function (Blueprint $table) {
            // Modify kolom jadi nullable
            $table->foreignId('label_id')->nullable()->change();

            // Optional → Tambah FK lagi jika masih diperlukan
            $table->foreign('label_id')
                ->references('id')
                ->on('production_item_labels')
                ->nullOnDelete(); // Optional behaviour
        });
    }

    public function down(): void
    {
        Schema::table('finished_goods', function (Blueprint $table) {
            $table->dropForeign(['label_id']);
        });

        Schema::table('finished_goods', function (Blueprint $table) {
            $table->foreignId('label_id')->nullable(false)->change();

            $table->foreign('label_id')
                ->references('id')
                ->on('production_item_labels');
        });
    }
};
