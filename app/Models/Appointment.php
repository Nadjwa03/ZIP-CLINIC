<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    // TABLE & PRIMARY KEY
    protected $table = 'appointments';
    protected $primaryKey = 'appointment_id';


    // STATUS CONSTANTS - SESUAI ERD
    const STATUS_BOOKED = 'BOOKED';
    const STATUS_CHECKED_IN = 'CHECKED_IN';
    const STATUS_IN_TREATMENT = 'IN_TREATMENT';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED = 'CANCELLED';


    // BOOKING SOURCE CONSTANTS - SESUAI ERD
    const SOURCE_WEB = 'WEB';
    const SOURCE_WALK_IN = 'WALK_IN';


    // FILLABLE - SESUAI MIGRATION
    protected $fillable = [
        'patient_id',
        'service_id',
        'doctor_user_id',
        'slot_id',
        'scheduled_start_at',
        'scheduled_end_at',
        'queue_number',
        'queue_date',
        'complaint',
        'status',
        'booking_source',
        'cancel_reason',
        'cancelled_at',
        'notes',
        // Medical Record Fields
        'examination_notes',
        'diagnosis',
        'treatment_plan',
        'prescription',
        'follow_up_date',
        'doctor_notes',
        // Vital Signs
        'blood_pressure',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'weight',
        'height',
        // Treatment Timestamps
        'treatment_started_at',
        'treatment_completed_at',
    ];


    // CASTS
    protected $casts = [
        'scheduled_start_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

  
    // APPENDS - Virtual Attributes
    protected $appends = [
        'status_label',
        'status_color',
        'date',
        'time_range',
        'duration_minutes',
    ];


    // RELATIONSHIPS
    
    /**
     * Pasien yang booking appointment ini
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    /**
     * Dokter yang ditugaskan untuk appointment ini
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_user_id', 'doctor_user_id');
    }

    /**
     * Layanan/service yang di-booking
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    /**
     * Time slot yang dipilih (jika pakai slot system)
     */
    public function slot()
    {
        return $this->belongsTo(ClinicTimeSlot::class, 'slot_id', 'id');
    }

    /**
     * Queue yang terbentuk dari appointment ini
     */
    public function queue()
    {
        return $this->hasOne(Queue::class, 'appointment_id', 'appointment_id');
    }

    /**
     * Visit yang terbentuk dari appointment ini (setelah treatment)
     */
    public function visit()
    {
        return $this->hasOne(Visit::class, 'appointment_id', 'appointment_id');
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
            self::STATUS_BOOKED => 'Terjadwal',
            self::STATUS_CHECKED_IN => 'Sudah Check-in',
            self::STATUS_IN_TREATMENT => 'Sedang Ditangani',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Get status color for UI (Tailwind/Bootstrap)
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_BOOKED => 'blue',
            self::STATUS_CHECKED_IN => 'indigo',
            self::STATUS_IN_TREATMENT => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get appointment date only (YYYY-MM-DD)
     */
    public function getDateAttribute()
    {
        return $this->scheduled_start_at?->format('Y-m-d');
    }

    /**
     * Get time range (HH:MM - HH:MM)
     */
    public function getTimeRangeAttribute()
    {
        if (!$this->scheduled_start_at) {
            return null;
        }

        $start = $this->scheduled_start_at->format('H:i');
        $end = $this->scheduled_end_at?->format('H:i');
        
        return $end ? "{$start} - {$end}" : $start;
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutesAttribute()
    {
        if (!$this->scheduled_start_at || !$this->scheduled_end_at) {
            return null;
        }

        return $this->scheduled_start_at->diffInMinutes($this->scheduled_end_at);
    }

    /**
     * Check if appointment is today
     */
    public function getIsTodayAttribute()
    {
        return $this->scheduled_start_at?->isToday() ?? false;
    }

    /**
     * Check if appointment is in the past
     */
    public function getIsPastAttribute()
    {
        return $this->scheduled_start_at?->isPast() ?? false;
    }

    /**
     * Check if appointment is upcoming (future)
     */
    public function getIsUpcomingAttribute()
    {
        return $this->scheduled_start_at?->isFuture() ?? false;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope untuk appointment hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_start_at', today());
    }

    /**
     * Scope untuk appointment pada tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('scheduled_start_at', $date);
    }

    /**
     * Scope untuk appointment dokter tertentu
     */
    public function scopeForDoctor($query, $doctorUserId)
    {
        return $query->where('doctor_user_id', $doctorUserId);
    }

    /**
     * Scope untuk appointment berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk appointment yang upcoming (belum lewat & belum selesai)
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_start_at', '>', now())
                     ->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope untuk appointment aktif (booked, checked-in, in-treatment)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_BOOKED,
            self::STATUS_CHECKED_IN,
            self::STATUS_IN_TREATMENT,
        ]);
    }

    /**
     * Scope untuk appointment pending (belum check-in)
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_BOOKED);
    }

    /**
     * Scope untuk appointment yang bisa di-check-in (hari ini & status booked)
     */
    public function scopeCanCheckIn($query)
    {
        return $query->whereDate('scheduled_start_at', today())
                     ->where('status', self::STATUS_BOOKED);
    }

    /**
     * Scope untuk filter by booking source
     */
    public function scopeFromWeb($query)
    {
        return $query->where('booking_source', self::SOURCE_WEB);
    }

    public function scopeWalkIn($query)
    {
        return $query->where('booking_source', self::SOURCE_WALK_IN);
    }

    // ==========================================
    // METHODS - State Transitions
    // ==========================================

    /**
     * Check-in appointment
     */
    public function checkIn()
    {
        if (!$this->canCheckIn()) {
            throw new \Exception('Appointment cannot be checked in with current status: ' . $this->status);
        }

        $this->update(['status' => self::STATUS_CHECKED_IN]);
        
        // Auto-create queue when check-in
        $this->createQueue();
        
        return $this;
    }

    /**
     * Start treatment (pindah ke IN_TREATMENT)
     */
    public function startTreatment()
    {
        if ($this->status !== self::STATUS_CHECKED_IN) {
            throw new \Exception('Appointment must be checked-in before starting treatment');
        }

        $this->update(['status' => self::STATUS_IN_TREATMENT]);
        
        // Update queue status juga
        $this->queue?->update(['status' => 'IN_TREATMENT']);
        
        return $this;
    }

    /**
     * Complete appointment
     */
    public function complete()
    {
        if ($this->status !== self::STATUS_IN_TREATMENT) {
            throw new \Exception('Appointment must be in treatment before completing');
        }

        $this->update(['status' => self::STATUS_COMPLETED]);
        
        // Update queue status juga
        $this->queue?->update([
            'status' => 'DONE',
            'completed_at' => now(),
        ]);
        
        return $this;
    }

    /**
     * Cancel appointment
     */
    public function cancel($reason = null)
    {
        if (!$this->canCancel()) {
            throw new \Exception('Appointment cannot be cancelled with current status: ' . $this->status);
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancel_reason' => $reason,
            'cancelled_at' => now(),
        ]);
        
        // Cancel queue juga jika ada
        $this->queue?->update([
            'status' => 'CANCELLED',
            'cancel_reason' => $reason,
        ]);
        
        return $this;
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Check if appointment can be checked in
     */
    public function canCheckIn(): bool
    {
        return $this->status === self::STATUS_BOOKED 
            && $this->scheduled_start_at->isToday();
    }

    /**
     * Check if appointment can be cancelled
     */
    public function canCancel(): bool
    {
        return in_array($this->status, [
            self::STATUS_BOOKED,
            self::STATUS_CHECKED_IN,
        ]);
    }

    /**
     * Check if appointment can be rescheduled
     */
    public function canReschedule(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    /**
     * Create queue from appointment (auto-called on check-in)
     */
    public function createQueue()
    {
        if ($this->queue) {
            return $this->queue; // Already has queue
        }

        // Get next queue number for this doctor today
        $lastQueue = Queue::where('doctor_user_id', $this->doctor_user_id)
            ->where('queue_date', today())
            ->orderByDesc('queue_number')
            ->first();

        $nextNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

        return Queue::create([
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
            'doctor_user_id' => $this->doctor_user_id,
            'queue_date' => today(),
            'queue_number' => $nextNumber,
            'complaint' => $this->complaint,
            'status' => 'WAITING',
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Calculate end time based on service duration
     */
    public function calculateEndTime()
    {
        $duration = $this->service?->duration_minutes ?? 30;
        return $this->scheduled_start_at->copy()->addMinutes($duration);
    }

    /**
     * Check for schedule conflict with other appointments
     */
    public static function hasConflict($doctorUserId, $startAt, $endAt, $excludeAppointmentId = null)
    {
        $query = self::where('doctor_user_id', $doctorUserId)
            ->where(function ($q) use ($startAt, $endAt) {
                // Check overlap
                $q->whereBetween('scheduled_start_at', [$startAt, $endAt])
                  ->orWhereBetween('scheduled_end_at', [$startAt, $endAt])
                  ->orWhere(function ($q2) use ($startAt, $endAt) {
                      $q2->where('scheduled_start_at', '<=', $startAt)
                         ->where('scheduled_end_at', '>=', $endAt);
                  });
            })
            ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_COMPLETED]);

        if ($excludeAppointmentId) {
            $query->where('appointment_id', '!=', $excludeAppointmentId);
        }

        return $query->exists();
    }

    /**
     * Generate queue number untuk tanggal dan dokter tertentu
     * Nomor antrian berdasarkan URUTAN BOOKING, bukan urutan check-in
     *
     * @param int $doctorUserId
     * @param string $date Format: Y-m-d
     * @return int
     */
    public static function generateQueueNumber($doctorUserId, $date)
    {
        // Get last queue number untuk dokter dan tanggal tersebut
        $lastAppointment = self::where('doctor_user_id', $doctorUserId)
            ->where('queue_date', $date)
            ->orderByDesc('queue_number')
            ->first();

        return ($lastAppointment?->queue_number ?? 0) + 1;
    }
}
