<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            // ==========================================
            // PRIMARY KEY - Sesuai ERD
            // ==========================================
            $table->id('invoice_item_id');
            
            // ==========================================
            // FOREIGN KEYS
            // ==========================================
            $table->unsignedBigInteger('invoice_id');
            
            // ==========================================
            // ITEM TYPE & REFERENCES - Sesuai ERD
            // ==========================================
            $table->enum('item_type', ['SERVICE', 'PRODUCT'])->default('SERVICE');
            
            // Reference ke service (untuk item_type=SERVICE)
            $table->unsignedBigInteger('service_id')->nullable();
            
            // Reference ke inventory (untuk item_type=PRODUCT)
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            
            // ==========================================
            // DENORMALIZED DATA - Sesuai ERD
            // ==========================================
            // Data ini di-copy dari master saat transaksi
            // Tujuan: Freeze data di waktu transaksi (immutable)
            // Kalau harga master berubah, invoice lama tetap sama
            
            $table->string('name', 160);
            $table->string('unit', 20)->nullable();
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            
            // ✨ IMPROVEMENT: Subtotal untuk performa query
            // Tidak ada di ERD, tapi SANGAT DISARANKAN
            // subtotal = (qty * unit_price) - discount
            $table->decimal('subtotal', 12, 2)
                ->storedAs('(qty * unit_price) - discount')
                ->comment('Auto-calculated: (qty × unit_price) - discount');
            
            // ==========================================
            // ADDITIONAL INFO
            // ==========================================
            
            // ✨ SARAN: Tooth codes untuk detail dental treatment
            $table->string('tooth_codes', 50)->nullable()->comment('Contoh: 11,12,21 atau 36-46');
            
            // Notes per item
            $table->text('notes')->nullable();
            
            $table->timestamps(); // created_at only (sesuai ERD)
            
            // ==========================================
            // FOREIGN KEY CONSTRAINTS
            // ==========================================
            $table->foreign('invoice_id')
                ->references('invoice_id')
                ->on('invoices')
                ->cascadeOnDelete(); // Hapus invoice → hapus items
            
            $table->foreign('service_id')
                ->references('service_id')
                ->on('services')
                ->nullOnDelete(); // Hapus service → null (preserve invoice history)

            // ⚠️ COMMENTED: Table inventory_items belum ada
            // Uncomment setelah create migration inventory_items
            // $table->foreign('inventory_item_id')
            //     ->references('inventory_item_id')
            //     ->on('inventory_items')
            //     ->nullOnDelete();
            
            // ==========================================
            // INDEXES
            // ==========================================
            $table->index('invoice_id');
            $table->index('item_type');
            $table->index('service_id');
            $table->index('inventory_item_id');
            
            // ==========================================
            // CHECK CONSTRAINTS (Laravel 10+)
            // ==========================================
            // Pastikan qty > 0
            // Pastikan unit_price >= 0
            // Pastikan discount >= 0
            // Pastikan discount <= (qty * unit_price)
            
            // Note: Check constraint tidak fully supported di semua DB
            // Validasi sebaiknya tetap di Model/Request
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
