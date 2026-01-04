<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            // ==========================================
            // PRIMARY KEY - Sesuai ERD
            // ==========================================
            $table->id('invoice_id');
            
            // ==========================================
            // FOREIGN KEYS
            // ==========================================
            // Link ke visit (WAJIB, karena invoice dari visit)
            $table->unsignedBigInteger('visit_id');
            
            // Denormalized patient_id untuk quick access (optional, bisa via visit->patient)
            // Tapi BAGUS ada untuk performance & query langsung
            // $table->unsignedBigInteger('patient_id'); // OPTIONAL
            
            // ==========================================
            // INVOICE NUMBER - Sesuai ERD
            // ==========================================
            $table->string('number', 30)->unique()->comment('Invoice number: INV-YYYYMM-XXXXX');
            
            // ==========================================
            // BREAKDOWN AMOUNT - Sesuai ERD
            // ==========================================
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Sum of all items before discount');
            $table->decimal('discount', 12, 2)->default(0)->comment('Total discount');
            
            // ✨ IMPROVEMENT: Tax/PPN (jika klinik PKP)
            $table->decimal('tax', 12, 2)->default(0)->comment('PPN 11% (optional)');
            
            // Total = subtotal - discount + tax
            $table->decimal('total', 12, 2)->default(0)->comment('Final amount to pay');
            
            // ==========================================
            // PAYMENT TRACKING (Denormalized)
            // ==========================================
            // Paid amount di-update dari table payments
            $table->decimal('paid_amount', 12, 2)->default(0)->comment('Sum from payments table');
            
            // Outstanding = total - paid_amount (virtual column)
            $table->decimal('outstanding', 12, 2)
                ->virtualAs('total - paid_amount')
                ->comment('Remaining amount to pay');
            
            // ==========================================
            // STATUS - IMPROVED dari ERD
            // ==========================================
            // DRAFT: Invoice belum final (bisa edit items)
            // UNPAID: Invoice final, belum ada pembayaran
            // PARTIALLY_PAID: Sudah bayar sebagian
            // PAID: Lunas
            // CANCELLED: Dibatalkan
            // REFUNDED: Sudah di-refund (return)
            $table->enum('status', [
                'DRAFT',
                'UNPAID',
                'PARTIALLY_PAID',
                'PAID',
                'CANCELLED',
                'REFUNDED'
            ])->default('DRAFT');
            
            // ==========================================
            // DATES
            // ==========================================
            $table->date('invoice_date')->default(DB::raw('CURRENT_DATE'))->comment('Tanggal invoice dibuat');
            $table->date('due_date')->nullable()->comment('Batas waktu pembayaran');
            $table->timestamp('paid_at')->nullable()->comment('Waktu lunas (status=PAID)');
            
            // ==========================================
            // ADDITIONAL INFO
            // ==========================================
            $table->text('notes')->nullable()->comment('Catatan untuk pasien/internal');
            $table->text('terms')->nullable()->comment('Syarat & ketentuan (opsional)');
            
            // ==========================================
            // DISCOUNT INFO (Opsional)
            // ==========================================
            $table->enum('discount_type', ['FIXED', 'PERCENTAGE'])->nullable();
            $table->decimal('discount_value', 12, 2)->nullable()->comment('Nominal atau % diskon');
            $table->string('discount_reason')->nullable()->comment('Alasan diskon');
            
            // ==========================================
            // REFUND INFO (Opsional)
            // ==========================================
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->text('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedBigInteger('refunded_by')->nullable();
            
            // ==========================================
            // AUDIT TRAIL
            // ==========================================
            $table->unsignedBigInteger('created_by')->nullable()->comment('User yang buat invoice');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('User yang approve (DRAFT→UNPAID)');
            $table->timestamp('approved_at')->nullable();
            
            // ==========================================
            // TIMESTAMPS & SOFT DELETE
            // ==========================================
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at (preserve history)
            
            // ==========================================
            // FOREIGN KEY CONSTRAINTS
            // ==========================================
            $table->foreign('visit_id')
                ->references('visit_id')
                ->on('visits')
                ->restrictOnDelete(); // Jangan hapus visit yang sudah ada invoice
            
            // Optional: Jika pakai denormalized patient_id
            // $table->foreign('patient_id')
            //     ->references('patient_id')
            //     ->on('patients')
            //     ->cascadeOnDelete();
            
            $table->foreign('created_by')
                ->references('id') // ✅ users PK = id
                ->on('users')
                ->nullOnDelete();

            $table->foreign('approved_by')
                ->references('id') // ✅ users PK = id
                ->on('users')
                ->nullOnDelete();

            $table->foreign('refunded_by')
                ->references('id') // ✅ users PK = id
                ->on('users')
                ->nullOnDelete();
            
            // ==========================================
            // INDEXES untuk Performance
            // ==========================================
            $table->index('visit_id');
            $table->index('status');
            $table->index('invoice_date');
            $table->index('due_date');
            $table->index(['status', 'invoice_date']); // Laporan invoice per status & tanggal
            $table->index('created_by'); // Laporan per user
            
            // Full-text search untuk invoice number (optional)
            // $table->fullText('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
