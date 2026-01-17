<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id('transaction_id');

            // Foreign Key
            $table->unsignedBigInteger('inventory_item_id');
            $table->foreign('inventory_item_id')
                ->references('inventory_item_id')
                ->on('inventory_items')
                ->cascadeOnDelete();

            // Transaction Type & Reference
            $table->enum('transaction_type', ['IN', 'OUT', 'ADJUSTMENT'])->comment('IN=Stock masuk, OUT=Stock keluar, ADJUSTMENT=Koreksi');
            $table->string('reference_type', 30)->nullable()->comment('PURCHASE, SALE, ADJUSTMENT, RETURN, EXPIRED, etc.');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('invoice_id, po_id, etc.');

            // Stock Changes
            $table->integer('qty_before')->comment('Stock sebelum transaksi');
            $table->integer('qty_change')->comment('Perubahan stock (+/-)');
            $table->integer('qty_after')->comment('Stock setelah transaksi');

            // Cost Information
            $table->decimal('unit_cost', 12, 2)->nullable()->comment('Cost per unit saat transaksi');
            $table->decimal('total_cost', 12, 2)->nullable()->comment('qty_change × unit_cost');

            // Details
            $table->string('reason', 200)->nullable()->comment('Alasan transaksi');
            $table->text('notes')->nullable();

            // Audit Trail
            $table->datetime('transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Waktu transaksi');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['inventory_item_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_by');
            $table->index('transaction_type');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
