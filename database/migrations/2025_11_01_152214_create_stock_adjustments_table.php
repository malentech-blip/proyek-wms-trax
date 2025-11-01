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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->foreignId('location_id')->constrained('locations');
            $table->integer('system_quantity')->comment('Quantity in system before adjustment');
            $table->integer('physical_quantity')->comment('Actual physical count');
            $table->integer('difference')->comment('physical_quantity - system_quantity');
            $table->text('reason')->nullable()->comment('Reason for adjustment');
            $table->foreignId('adjusted_by')->constrained('users')->comment('Admin who made the adjustment');
            $table->boolean('requires_approval')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('Super Admin who approved');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
