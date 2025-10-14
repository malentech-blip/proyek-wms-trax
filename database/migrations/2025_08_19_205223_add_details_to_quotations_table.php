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
        Schema::table('quotations', function (Blueprint $table) {
            // Menambahkan kolom-kolom yang hilang setelah kolom 'accurate_customer_id'
            $table->string('company_name')->nullable()->after('accurate_customer_id');
            $table->string('phone')->nullable()->after('company_name');
            $table->text('notes')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'phone', 'notes']);
        });
    }
};