<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique(); // No. Penerimaan unik yang kita generate
            $table->string('po_number'); // No. PO dari Accurate
            $table->foreignId('supplier_id')->nullable(); // ID Supplier
            $table->foreignId('received_by_id'); // User yang menerima
            $table->date('receipt_date'); // Tanggal terima
            $table->string('status')->default('pending_qc'); // Status: pending_qc, completed, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};