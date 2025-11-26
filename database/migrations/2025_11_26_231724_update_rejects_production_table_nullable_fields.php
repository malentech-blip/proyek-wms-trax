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
        Schema::table('rejects_production', function (Blueprint $table) {
            $table->text('reason')->nullable()->change();
            $table->enum('action', ['rework', 'scrap'])->nullable()->change();
            $table->string('handled_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rejects_production', function (Blueprint $table) {
            $table->text('reason')->nullable(false)->change();
            $table->enum('action', ['rework', 'scrap'])->nullable(false)->change();
            $table->string('handled_by')->nullable(false)->change();
        });
    }
};
