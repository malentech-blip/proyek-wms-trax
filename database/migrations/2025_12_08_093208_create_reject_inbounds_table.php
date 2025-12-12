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
        Schema::create('reject_inbounds', function (Blueprint $table) {
            $table->id();
            // Relasi ke item penerimaan barang
            $table->foreignId('goods_receipt_item_id')->constrained('goods_receipt_items')->onDelete('cascade');
            
            // Siapa yang melakukan reject (User ID)
            $table->foreignId('qc_by_id')->constrained('users');
            
            $table->integer('rejected_qty');
            $table->text('reason')->nullable(); // Alasan reject (Rusak, Expired, dll)
            
            // Status tindak lanjut: pending (menunggu keputusan), returned (retur ke vendor), disposed (dimusnahkan)
            $table->string('action')->default('pending'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reject_inbounds');
    }
};