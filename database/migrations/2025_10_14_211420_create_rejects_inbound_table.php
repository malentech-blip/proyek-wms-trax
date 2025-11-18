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
    Schema::create('rejects_inbound', function (Blueprint $table) {
        $table->id();
        $table->foreignId('goods_receipt_item_id')->constrained('goods_receipt_items')->onDelete('cascade');
        $table->foreignId('qc_by_id')->nullable()->constrained('users')->onDelete('set null');
        $table->unsignedInteger('rejected_qty');
        $table->string('reason')->nullable();
        $table->string('action')->default('pending'); // Status tindakan: pending, returned, disposed
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rejects_inbound');
    }
};
