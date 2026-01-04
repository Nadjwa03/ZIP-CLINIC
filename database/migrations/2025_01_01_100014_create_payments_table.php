<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            // ==========================================
            // PRIMARY KEY - Sesuai ERD
            // ==========================================
            $table->id('payment_id');
            
            // ==========================================
            // FOREIGN KEYS
            // ==========================================
            $table->unsignedBigInteger('invoice_id');
            
            // ==========================================
            // PAYMENT METHOD - IMPROVED
            // ==========================================
            $table->enum('method', [
                'CASH',      // Tunai
                'QRIS',      // QRIS manual (scan via app)
                'TRANSFER',  // Transfer bank manual
                'DEBIT',     // Debit card (EDC)
                'CREDIT',    // Credit card (EDC)
                'MIDTRANS',  // Payment via Midtrans (all digital payments)
            ])->default('CASH');
            
            // ==========================================
            // AMOUNT - Sesuai ERD
            // ==========================================
            $table->decimal('amount', 12, 2);
            
            // ==========================================
            // VERIFICATION STATUS - Sesuai ERD
            // ==========================================
            $table->enum('verify_status', ['VERIFIED', 'PENDING', 'FAILED'])
                ->default('VERIFIED');
            
            // ==========================================
            // REFERENCE INFO
            // ==========================================
            // Nomor referensi transaksi
            $table->string('ref_no', 80)->nullable()->comment('Reference/approval number');
            
            // Bank/Provider info
            $table->string('bank_name', 50)->nullable()->comment('Bank name or payment provider');
            
            // Account number (untuk transfer, simpan 4 digit terakhir saja)
            $table->string('account_number', 30)->nullable()->comment('Sender account (last 4 digits)');
            
            // Card info (untuk debit/credit, simpan 4 digit terakhir saja)
            $table->string('card_last4', 4)->nullable()->comment('Last 4 digits of card');
            $table->string('card_type', 20)->nullable()->comment('Visa, Mastercard, JCB, etc');
            
            // ==========================================
            // PAYMENT GATEWAY INTEGRATION (MIDTRANS)
            // ==========================================
            
            // Provider gateway
            $table->string('gateway_provider', 30)->nullable()->comment('midtrans, xendit, dll');
            
            // Transaction ID dari gateway (unique untuk prevent duplicate)
            $table->string('gateway_transaction_id', 100)->nullable()->comment('Order ID dari gateway');
            
            // Payment type dari gateway (gopay, shopeepay, bank_transfer, dll)
            $table->string('gateway_payment_type', 50)->nullable()->comment('Payment method dari gateway');
            
            // Status dari gateway (settlement, pending, expire, dll)
            $table->string('gateway_status', 50)->nullable()->comment('Status dari gateway');
            
            // Raw response dari gateway (untuk debugging & audit trail)
            $table->json('gateway_response')->nullable()->comment('Full response dari gateway');
            
            // Webhook tracking
            $table->timestamp('gateway_notified_at')->nullable()->comment('Kapan gateway kirim notifikasi');
            
            // Fee dari gateway (MDR - Merchant Discount Rate)
            $table->decimal('gateway_fee', 12, 2)->default(0)->comment('Fee charged by gateway');
            
            // Net amount setelah dipotong fee (virtual/computed column)
            $table->decimal('net_amount', 12, 2)
                ->storedAs('amount - gateway_fee')
                ->comment('Net amount after gateway fee');
            
            // Expiry untuk pending payment
            $table->timestamp('expires_at')->nullable()->comment('Payment expiration time');
            
            // ==========================================
            // PROOF OF PAYMENT
            // ==========================================
            // Upload bukti transfer/screenshot
            $table->string('proof_image_path')->nullable()->comment('Bukti transfer/struk');
            
            // ==========================================
            // ADDITIONAL INFO
            // ==========================================
            $table->text('notes')->nullable()->comment('Catatan dari kasir atau sistem');
            
            // ==========================================
            // AUDIT TRAIL
            // ==========================================
            
            // Siapa yang input/proses pembayaran ini (kasir)
            $table->unsignedBigInteger('processed_by')->nullable();
            
            // Siapa yang verify pembayaran (untuk status PENDING → VERIFIED)
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            // ==========================================
            // TIMESTAMPS
            // ==========================================
            $table->timestamp('paid_at')->nullable();
            $table->timestamps(); // created_at, updated_at
            
            // ==========================================
            // FOREIGN KEY CONSTRAINTS
            // ==========================================
            $table->foreign('invoice_id')
                ->references('invoice_id')
                ->on('invoices')
                ->cascadeOnDelete();
            
            $table->foreign('processed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            
            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            
            // ==========================================
            // INDEXES untuk Performance
            // ==========================================
            $table->index('invoice_id');
            $table->index('method');
            $table->index('verify_status');
            $table->index('paid_at');
            $table->index(['paid_at', 'method']); // Laporan harian per metode
            $table->index('processed_by'); // Laporan per kasir
            $table->index('ref_no'); // Search by reference
            
            // Indexes untuk gateway
            $table->index('gateway_transaction_id'); // Webhook query
            $table->index(['gateway_provider', 'gateway_status']); // Gateway reporting
            $table->index('expires_at'); // Auto-cancel expired payments
            
            // Unique constraint untuk prevent duplicate gateway transaction
            $table->unique('gateway_transaction_id', 'uq_gateway_txn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
