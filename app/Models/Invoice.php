<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Invoice extends Model
{
    use SoftDeletes;

    // ==========================================
    // TABLE & PRIMARY KEY
    // ==========================================
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id'; // ✅ PENTING!

    // ==========================================
    // STATUS CONSTANTS
    // ==========================================
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_UNPAID = 'UNPAID';
    const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATUS_PAID = 'PAID';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_REFUNDED = 'REFUNDED';

    // ==========================================
    // FILLABLE - SESUAI MIGRATION LENGKAP
    // ==========================================
    protected $fillable = [
        'visit_id',
        'number',              // Invoice number (bukan invoice_number)
        'invoice_date',        // Tanggal invoice dibuat
        'due_date',
        
        // Amounts
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',         // Denormalized dari payments table
        
        // Status
        'status',
        
        // Additional
        'notes',
        'terms',
        
        // Discount details
        'discount_type',
        'discount_value',
        'discount_reason',
        
        // Refund info
        'refund_amount',
        'refund_reason',
        'refunded_at',
        'refunded_by',
        
        // Audit trail
        'created_by',
        'approved_by',
        'approved_at',
        
        // Timestamps
        'paid_at',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'approved_at' => 'datetime',
        
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    // ==========================================
    // APPENDS - Virtual Attributes
    // ==========================================
    protected $appends = [
        'status_label',
        'status_color',
        'outstanding',
        'is_paid',
        'is_overdue',
        'payment_progress_percentage',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Visit yang menjadi sumber invoice
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id', 'visit_id');
    }

    /**
     * Patient (via visit relationship)
     * Lebih baik akses via visit untuk konsistensi dengan ERD
     */
    public function patient()
    {
        // Access via visit
        return $this->hasOneThrough(
            Patient::class,
            Visit::class,
            'visit_id',      // FK on visits table
            'patient_id',    // FK on patients table
            'visit_id',      // Local key on invoices
            'patient_id'     // Local key on visits
        );
    }

    /**
     * Invoice items (services/products)
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    /**
     * All payments for this invoice
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Only verified payments
     */
    public function verifiedPayments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id')
            ->where('verify_status', Payment::VERIFY_VERIFIED);
    }

    /**
     * User yang create invoice
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * User yang approve invoice (DRAFT → UNPAID)
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    /**
     * User yang refund invoice
     */
    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by', 'user_id');
    }

    // ==========================================
    // ACCESSORS - Virtual Attributes
    // ==========================================

    /**
     * Get readable status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_UNPAID => 'Belum Dibayar',
            self::STATUS_PARTIALLY_PAID => 'Dibayar Sebagian',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_REFUNDED => 'Dikembalikan',
            default => $this->status,
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_UNPAID => 'red',
            self::STATUS_PARTIALLY_PAID => 'yellow',
            self::STATUS_PAID => 'green',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_REFUNDED => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get outstanding amount (sisa yang harus dibayar)
     */
    public function getOutstandingAttribute()
    {
        return max(0, $this->total - $this->paid_amount);
    }

    /**
     * Check if invoice is fully paid
     */
    public function getIsPaidAttribute()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->due_date || $this->is_paid) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Get payment progress percentage
     */
    public function getPaymentProgressPercentageAttribute()
    {
        if ($this->total <= 0) {
            return 0;
        }

        return min(100, round(($this->paid_amount / $this->total) * 100));
    }

    /**
     * Get days until due (negative if overdue)
     */
    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) {
            return null;
        }

        return today()->diffInDays($this->due_date, false);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope untuk unpaid invoices
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [
            self::STATUS_UNPAID,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    /**
     * Scope untuk paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope untuk draft invoices
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope untuk overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [self::STATUS_UNPAID, self::STATUS_PARTIALLY_PAID])
            ->whereNotNull('due_date')
            ->where('due_date', '<', today());
    }

    /**
     * Scope untuk invoices hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('invoice_date', today());
    }

    /**
     * Scope untuk invoices pada tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('invoice_date', $date);
    }

    /**
     * Scope untuk invoices by patient
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->whereHas('visit', function ($q) use ($patientId) {
            $q->where('patient_id', $patientId);
        });
    }

    // ==========================================
    // STATIC METHODS - Generators
    // ==========================================

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        
        $lastInvoice = static::where('number', 'LIKE', $prefix . '%')
            ->orderByDesc('invoice_id')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create invoice from visit
     */
    public static function createFromVisit(Visit $visit, $userId = null)
    {
        // Create invoice
        $invoice = static::create([
            'visit_id' => $visit->visit_id,
            'number' => static::generateInvoiceNumber(),
            'invoice_date' => today(),
            'due_date' => today()->addDays(7), // 7 hari dari sekarang
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'total' => 0,
            'paid_amount' => 0,
            'status' => self::STATUS_DRAFT,
            'created_by' => $userId ?? auth()->id(),
        ]);

        // Create items from visit details
        foreach ($visit->visitDetails as $detail) {
            $item = InvoiceItem::createFromVisitDetail($detail);
            $item->invoice_id = $invoice->invoice_id;
            $item->save();
        }

        // Calculate totals
        $invoice->recalculateTotals();

        // Auto-approve (DRAFT → UNPAID)
        $invoice->approve($userId);

        return $invoice;
    }

    // ==========================================
    // METHODS - Calculations
    // ==========================================

    /**
     * Recalculate totals from items
     */
    public function recalculateTotals()
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total = $this->subtotal - $this->discount + $this->tax;
        $this->save();
        
        return $this;
    }

    /**
     * Recalculate paid amount from verified payments
     */
    public function recalculatePaidAmount()
    {
        $oldPaidAmount = $this->paid_amount;
        
        $this->paid_amount = $this->verifiedPayments()->sum('amount');
        
        // Update status based on new paid_amount
        $this->updatePaymentStatus();
        
        $this->save();
        
        return $this;
    }

    /**
     * Update status based on paid amount
     */
    protected function updatePaymentStatus()
    {
        if ($this->paid_amount >= $this->total) {
            $this->status = self::STATUS_PAID;
            $this->paid_at = $this->paid_at ?? now();
        } elseif ($this->paid_amount > 0) {
            $this->status = self::STATUS_PARTIALLY_PAID;
            $this->paid_at = null;
        } else {
            $this->status = self::STATUS_UNPAID;
            $this->paid_at = null;
        }
    }

    /**
     * Apply discount to invoice
     */
    public function applyDiscount($value, $isPercentage = false, $reason = null)
    {
        $this->discount_type = $isPercentage ? 'PERCENTAGE' : 'FIXED';
        $this->discount_value = $value;
        $this->discount_reason = $reason;
        
        if ($isPercentage) {
            $this->discount = $this->subtotal * ($value / 100);
        } else {
            $this->discount = $value;
        }
        
        $this->total = $this->subtotal - $this->discount + $this->tax;
        $this->save();
        
        return $this;
    }

    // ==========================================
    // METHODS - State Transitions
    // ==========================================

    /**
     * Approve invoice (DRAFT → UNPAID)
     */
    public function approve($userId = null)
    {
        if ($this->status !== self::STATUS_DRAFT) {
            throw new \Exception('Only draft invoices can be approved');
        }

        $this->update([
            'status' => self::STATUS_UNPAID,
            'approved_by' => $userId ?? auth()->id(),
            'approved_at' => now(),
        ]);

        return $this;
    }

    /**
     * Cancel invoice
     */
    public function cancel($reason = null)
    {
        if ($this->status === self::STATUS_PAID) {
            throw new \Exception('Cannot cancel paid invoice. Use refund instead.');
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason ? "Cancelled: {$reason}" : $this->notes,
        ]);

        return $this;
    }

    /**
     * Refund invoice
     */
    public function refund($amount, $reason, $userId = null)
    {
        if ($this->status !== self::STATUS_PAID) {
            throw new \Exception('Only paid invoices can be refunded');
        }

        $this->update([
            'status' => self::STATUS_REFUNDED,
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
            'refunded_by' => $userId ?? auth()->id(),
        ]);

        // Create negative payment record
        Payment::create([
            'invoice_id' => $this->invoice_id,
            'method' => Payment::METHOD_CASH,
            'amount' => -$amount,
            'verify_status' => Payment::VERIFY_VERIFIED,
            'notes' => "Refund: {$reason}",
            'processed_by' => $userId ?? auth()->id(),
            'paid_at' => now(),
        ]);

        return $this;
    }

    // ==========================================
    // METHODS - Payments
    // ==========================================

    /**
     * Add payment to invoice
     */
    public function addPayment($method, $amount, $data = [])
    {
        $isAutoVerified = in_array($method, [
            Payment::METHOD_CASH,
            Payment::METHOD_DEBIT,
            Payment::METHOD_CREDIT,
        ]);

        $payment = $this->payments()->create([
            'method' => $method,
            'amount' => $amount,
            'verify_status' => $isAutoVerified ? Payment::VERIFY_VERIFIED : Payment::VERIFY_PENDING,
            'ref_no' => $data['ref_no'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'proof_image_path' => $data['proof_image'] ?? null,
            'notes' => $data['notes'] ?? null,
            'processed_by' => $data['processed_by'] ?? auth()->id(),
            'paid_at' => now(),
        ]);

        // Recalculate jika auto-verified
        if ($isAutoVerified) {
            $this->recalculatePaidAmount();
        }

        return $payment;
    }

    // ==========================================
    // METHODS - Reporting
    // ==========================================

    /**
     * Get invoice summary
     */
    public function getSummary()
    {
        return [
            'invoice_number' => $this->number,
            'invoice_date' => $this->invoice_date->format('d M Y'),
            'due_date' => $this->due_date?->format('d M Y'),
            'patient_name' => $this->visit->patient->name,
            'items_count' => $this->items->count(),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'total' => $this->total,
            'paid_amount' => $this->paid_amount,
            'outstanding' => $this->outstanding,
            'status' => $this->status_label,
            'is_overdue' => $this->is_overdue,
            'payments' => $this->verifiedPayments->map(fn($p) => [
                'date' => $p->paid_at->format('d M Y H:i'),
                'method' => $p->method_label,
                'amount' => $p->amount,
            ]),
        ];
    }
}
