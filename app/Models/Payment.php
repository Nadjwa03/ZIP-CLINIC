<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    // ==========================================
    // TABLE & PRIMARY KEY
    // ==========================================
    protected $table = 'payments';
    protected $primaryKey = 'payment_id'; // ✅ PENTING!

    // ==========================================
    // PAYMENT METHOD CONSTANTS
    // ==========================================
    const METHOD_CASH = 'CASH';
    const METHOD_QRIS = 'QRIS';
    const METHOD_TRANSFER = 'TRANSFER';
    const METHOD_DEBIT = 'DEBIT';
    const METHOD_CREDIT = 'CREDIT';
    const METHOD_MIDTRANS = 'MIDTRANS'; // Digital payments via gateway

    // ==========================================
    // VERIFY STATUS CONSTANTS
    // ==========================================
    const VERIFY_VERIFIED = 'VERIFIED';
    const VERIFY_PENDING = 'PENDING';
    const VERIFY_FAILED = 'FAILED';

    // ==========================================
    // FILLABLE - SESUAI MIGRATION LENGKAP
    // ==========================================
    protected $fillable = [
        'invoice_id',
        'method',
        'amount',
        'verify_status',
        
        // Reference info (manual payments)
        'ref_no',
        'bank_name',
        'account_number',
        'card_last4',
        'card_type',
        
        // Gateway info (Midtrans)
        'gateway_provider',
        'gateway_transaction_id',
        'gateway_payment_type',
        'gateway_status',
        'gateway_response',
        'gateway_notified_at',
        'gateway_fee',
        'expires_at',
        
        // Proof of payment
        'proof_image_path',
        
        // Additional info
        'notes',
        
        // Audit trail
        'processed_by',
        'verified_by',
        'verified_at',
        
        // Timestamps
        'paid_at',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'gateway_response' => 'array', // JSON → Array
        'paid_at' => 'datetime',
        'gateway_notified_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // ==========================================
    // APPENDS - Virtual Attributes
    // ==========================================
    protected $appends = [
        'method_label',
        'method_icon',
        'verify_status_label',
        'verify_status_color',
        'net_amount',
        'is_expired',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Invoice yang dibayar
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * User yang memproses payment (kasir)
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    /**
     * User yang verify payment
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    // ==========================================
    // ACCESSORS - Virtual Attributes
    // ==========================================

    /**
     * Get readable payment method label
     */
    public function getMethodLabelAttribute()
    {
        return match($this->method) {
            self::METHOD_CASH => 'Tunai',
            self::METHOD_QRIS => 'QRIS',
            self::METHOD_TRANSFER => 'Transfer Bank',
            self::METHOD_DEBIT => 'Kartu Debit',
            self::METHOD_CREDIT => 'Kartu Kredit',
            self::METHOD_MIDTRANS => 'Digital Payment',
            default => $this->method,
        };
    }

    /**
     * Get payment method icon for UI
     */
    public function getMethodIconAttribute()
    {
        return match($this->method) {
            self::METHOD_CASH => '💵',
            self::METHOD_QRIS => '📱',
            self::METHOD_TRANSFER => '🏦',
            self::METHOD_DEBIT => '💳',
            self::METHOD_CREDIT => '💳',
            self::METHOD_MIDTRANS => '🌐',
            default => '💰',
        };
    }

    /**
     * Get verify status label
     */
    public function getVerifyStatusLabelAttribute()
    {
        return match($this->verify_status) {
            self::VERIFY_VERIFIED => 'Terverifikasi',
            self::VERIFY_PENDING => 'Menunggu Verifikasi',
            self::VERIFY_FAILED => 'Gagal',
            default => $this->verify_status,
        };
    }

    /**
     * Get verify status color for UI
     */
    public function getVerifyStatusColorAttribute()
    {
        return match($this->verify_status) {
            self::VERIFY_VERIFIED => 'green',
            self::VERIFY_PENDING => 'yellow',
            self::VERIFY_FAILED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get net amount (amount - gateway fee)
     * For manual payments, net_amount = amount
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - ($this->gateway_fee ?? 0);
    }

    /**
     * Check if payment is expired (for PENDING payments)
     */
    public function getIsExpiredAttribute()
    {
        if ($this->verify_status !== self::VERIFY_PENDING || !$this->expires_at) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Check if payment is from Midtrans
     */
    public function getIsMidtransAttribute()
    {
        return $this->method === self::METHOD_MIDTRANS 
            || !is_null($this->gateway_provider);
    }

    /**
     * Get detailed payment type (untuk Midtrans)
     * Contoh: gopay, shopeepay, bca_va, dll
     */
    public function getDetailedTypeAttribute()
    {
        if ($this->is_midtrans && $this->gateway_payment_type) {
            return match($this->gateway_payment_type) {
                'gopay' => 'GoPay',
                'shopeepay' => 'ShopeePay',
                'qris' => 'QRIS',
                'bca_va' => 'BCA Virtual Account',
                'bni_va' => 'BNI Virtual Account',
                'bri_va' => 'BRI Virtual Account',
                'mandiri_va' => 'Mandiri Virtual Account',
                'permata_va' => 'Permata Virtual Account',
                'bank_transfer' => 'Transfer Bank',
                'credit_card' => 'Kartu Kredit',
                default => ucwords(str_replace('_', ' ', $this->gateway_payment_type)),
            };
        }

        return $this->method_label;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope untuk payment yang verified
     */
    public function scopeVerified($query)
    {
        return $query->where('verify_status', self::VERIFY_VERIFIED);
    }

    /**
     * Scope untuk payment yang pending
     */
    public function scopePending($query)
    {
        return $query->where('verify_status', self::VERIFY_PENDING);
    }

    /**
     * Scope untuk payment hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('paid_at', today());
    }

    /**
     * Scope untuk payment pada tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('paid_at', $date);
    }

    /**
     * Scope untuk payment berdasarkan method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Scope untuk payment via Midtrans
     */
    public function scopeMidtrans($query)
    {
        return $query->where('method', self::METHOD_MIDTRANS)
                     ->orWhereNotNull('gateway_provider');
    }

    /**
     * Scope untuk manual payments (cash, transfer, EDC)
     */
    public function scopeManual($query)
    {
        return $query->whereIn('method', [
            self::METHOD_CASH,
            self::METHOD_TRANSFER,
            self::METHOD_QRIS,
            self::METHOD_DEBIT,
            self::METHOD_CREDIT,
        ])->whereNull('gateway_provider');
    }

    /**
     * Scope untuk expired pending payments
     */
    public function scopeExpired($query)
    {
        return $query->where('verify_status', self::VERIFY_PENDING)
                     ->whereNotNull('expires_at')
                     ->where('expires_at', '<', now());
    }

    /**
     * Scope untuk payment by kasir
     */
    public function scopeByProcessor($query, $userId)
    {
        return $query->where('processed_by', $userId);
    }

    // ==========================================
    // METHODS - State Transitions
    // ==========================================

    /**
     * Verify payment (PENDING → VERIFIED)
     */
    public function verify($verifiedBy = null)
    {
        if ($this->verify_status === self::VERIFY_VERIFIED) {
            return $this; // Already verified
        }

        $this->update([
            'verify_status' => self::VERIFY_VERIFIED,
            'verified_by' => $verifiedBy ?? auth()->id(),
            'verified_at' => now(),
        ]);

        // Update invoice paid_amount
        $this->invoice->recalculatePaidAmount();

        return $this;
    }

    /**
     * Mark payment as failed
     */
    public function markFailed($reason = null)
    {
        $this->update([
            'verify_status' => self::VERIFY_FAILED,
            'notes' => $reason ? "Failed: {$reason}" : $this->notes,
        ]);

        return $this;
    }

    /**
     * Auto-expire pending payment
     */
    public function expire()
    {
        if ($this->verify_status !== self::VERIFY_PENDING) {
            throw new \Exception('Only pending payments can be expired');
        }

        $this->markFailed('Payment expired');

        return $this;
    }

    // ==========================================
    // STATIC HELPER METHODS
    // ==========================================

    /**
     * Get all available payment methods
     */
    public static function getMethods()
    {
        return [
            self::METHOD_CASH => 'Tunai',
            self::METHOD_QRIS => 'QRIS',
            self::METHOD_TRANSFER => 'Transfer Bank',
            self::METHOD_DEBIT => 'Kartu Debit',
            self::METHOD_CREDIT => 'Kartu Kredit',
            self::METHOD_MIDTRANS => 'Digital Payment (Midtrans)',
        ];
    }

    /**
     * Create cash payment
     */
    public static function createCashPayment($invoiceId, $amount, $processedBy = null)
    {
        return static::create([
            'invoice_id' => $invoiceId,
            'method' => self::METHOD_CASH,
            'amount' => $amount,
            'verify_status' => self::VERIFY_VERIFIED, // Cash auto-verified
            'processed_by' => $processedBy ?? auth()->id(),
            'verified_by' => $processedBy ?? auth()->id(),
            'verified_at' => now(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Create transfer/QRIS payment (need verification)
     */
    public static function createTransferPayment($invoiceId, $amount, $method, $data = [])
    {
        return static::create([
            'invoice_id' => $invoiceId,
            'method' => $method,
            'amount' => $amount,
            'verify_status' => self::VERIFY_PENDING,
            'ref_no' => $data['ref_no'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'proof_image_path' => $data['proof_image'] ?? null,
            'notes' => $data['notes'] ?? null,
            'processed_by' => auth()->id(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Get daily summary by payment method
     */
    public static function getDailySummary($date = null)
    {
        $date = $date ?? today();
        
        return static::forDate($date)
            ->verified()
            ->selectRaw('method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('method')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->method => [
                    'total' => $item->total,
                    'count' => $item->count,
                ]];
            });
    }

    /**
     * Get total payments for date
     */
    public static function getTotalForDate($date = null)
    {
        $date = $date ?? today();
        
        return static::forDate($date)
            ->verified()
            ->sum('amount');
    }

    /**
     * Auto-expire pending payments (run via scheduler)
     */
    public static function autoExpirePending()
    {
        $expiredPayments = static::expired()->get();

        foreach ($expiredPayments as $payment) {
            $payment->expire();
        }

        return $expiredPayments->count();
    }
}
