<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Queue extends Model
{

    // TABLE & PRIMARY KEY
    protected $table = 'queues';
    protected $primaryKey = 'queue_id'; // ✅ PENTING! Sesuai migration


    // STATUS CONSTANTS - SESUAI ERD + IMPROVEMENT
    const STATUS_WAITING = 'WAITING';
    const STATUS_CALLED = 'CALLED';          
    const STATUS_IN_TREATMENT = 'IN_TREATMENT';
    const STATUS_DONE = 'DONE';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_SKIPPED = 'SKIPPED';         


    // FILLABLE - SESUAI MIGRATION
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_user_id',
        'queue_number',
        'queue_date',
        'estimated_time',
        'status',
        'complaint',
        'cancel_reason',
        'checked_in_at',    
        'called_at',
        'started_at',
        'completed_at',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'queue_date' => 'date',
        'checked_in_at' => 'datetime',
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ==========================================
    // APPENDS - Virtual Attributes
    // ==========================================
    protected $appends = [
        'status_label',
        'status_color',
        'formatted_queue_number',
        'waiting_time',
        'total_duration',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Pasien yang antri
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    /**
     * Dokter yang handle antrian
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_user_id', 'doctor_user_id');
    }

    /**
     * Appointment yang jadi sumber antrian (nullable untuk walk-in)
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'appointment_id');
    }

    /**
     * Visit yang terbentuk dari antrian ini
     */
    public function visit()
    {
        return $this->hasOne(Visit::class, 'queue_id', 'queue_id');
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
            self::STATUS_WAITING => 'Menunggu',
            self::STATUS_CALLED => 'Dipanggil',
            self::STATUS_IN_TREATMENT => 'Sedang Ditangani',
            self::STATUS_DONE => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_SKIPPED => 'Dilewati',
            default => $this->status,
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_WAITING => 'yellow',
            self::STATUS_CALLED => 'blue',
            self::STATUS_IN_TREATMENT => 'purple',
            self::STATUS_DONE => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_SKIPPED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get formatted queue number (001, 002, ...)
     */
    public function getFormattedQueueNumberAttribute()
    {
        return str_pad($this->queue_number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get waiting time (dari check-in sampai sekarang)
     */
    public function getWaitingTimeAttribute()
    {
        if (!in_array($this->status, [self::STATUS_WAITING, self::STATUS_CALLED])) {
            return null;
        }

        $startTime = $this->checked_in_at ?? $this->created_at;
        $minutes = now()->diffInMinutes($startTime);
        
        return $this->formatDuration($minutes);
    }

    /**
     * Get total duration (dari check-in sampai selesai)
     */
    public function getTotalDurationAttribute()
    {
        if ($this->status !== self::STATUS_DONE || !$this->completed_at) {
            return null;
        }

        $startTime = $this->checked_in_at ?? $this->created_at;
        $minutes = $this->completed_at->diffInMinutes($startTime);
        
        return $this->formatDuration($minutes);
    }

    /**
     * Format duration in human readable format
     */
    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $mins > 0 ? "{$hours} jam {$mins} menit" : "{$hours} jam";
    }

    /**
     * Check if queue is from appointment (online booking)
     */
    public function getIsFromAppointmentAttribute()
    {
        return !is_null($this->appointment_id);
    }

    /**
     * Check if queue is walk-in
     */
    public function getIsWalkInAttribute()
    {
        return is_null($this->appointment_id);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope untuk queue pada tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('queue_date', $date);
    }

    /**
     * Scope untuk queue hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('queue_date', today());
    }

    /**
     * Scope untuk queue dokter tertentu
     */
    public function scopeForDoctor($query, $doctorUserId)
    {
        return $query->where('doctor_user_id', $doctorUserId);
    }

    /**
     * Scope untuk queue berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk queue yang menunggu
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    /**
     * Scope untuk queue yang sedang dipanggil
     */
    public function scopeCalled($query)
    {
        return $query->where('status', self::STATUS_CALLED);
    }

    /**
     * Scope untuk queue yang aktif (waiting, called, in_treatment)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_WAITING,
            self::STATUS_CALLED,
            self::STATUS_IN_TREATMENT,
        ]);
    }

    /**
     * Scope untuk queue dari appointment (online booking)
     */
    public function scopeFromAppointment($query)
    {
        return $query->whereNotNull('appointment_id');
    }

    /**
     * Scope untuk queue walk-in
     */
    public function scopeWalkIn($query)
    {
        return $query->whereNull('appointment_id');
    }

    /**
     * Order by queue number ascending
     */
    public function scopeInOrder($query)
    {
        return $query->orderBy('queue_number', 'asc');
    }

    // ==========================================
    // METHODS - State Transitions
    // ==========================================

    /**
     * Panggil antrian (status: WAITING → CALLED)
     */
    public function call()
    {
        if ($this->status !== self::STATUS_WAITING) {
            throw new \Exception('Only waiting queue can be called');
        }

        $this->update([
            'status' => self::STATUS_CALLED,
            'called_at' => now(),
        ]);

        // TODO: Send notification to patient (WhatsApp/SMS)
        // TODO: Display on monitor/TV
        
        return $this;
    }

    /**
     * Mulai treatment (status: CALLED → IN_TREATMENT)
     */
    public function startTreatment()
    {
        if (!in_array($this->status, [self::STATUS_CALLED, self::STATUS_WAITING])) {
            throw new \Exception('Invalid status to start treatment: ' . $this->status);
        }

        $this->update([
            'status' => self::STATUS_IN_TREATMENT,
            'started_at' => now(),
        ]);

        // Update related appointment juga
        $this->appointment?->update(['status' => Appointment::STATUS_IN_TREATMENT]);
        
        return $this;
    }

    /**
     * Selesai treatment (status: IN_TREATMENT → DONE)
     */
    public function complete()
    {
        if ($this->status !== self::STATUS_IN_TREATMENT) {
            throw new \Exception('Queue must be in treatment to complete');
        }

        $this->update([
            'status' => self::STATUS_DONE,
            'completed_at' => now(),
        ]);

        // Update related appointment juga
        $this->appointment?->update(['status' => Appointment::STATUS_COMPLETED]);
        
        return $this;
    }

    /**
     * Cancel antrian
     */
    public function cancel($reason = null)
    {
        if (!in_array($this->status, [self::STATUS_WAITING, self::STATUS_CALLED])) {
            throw new \Exception('Cannot cancel queue with status: ' . $this->status);
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancel_reason' => $reason,
        ]);

        // Update related appointment juga
        $this->appointment?->cancel($reason);
        
        return $this;
    }

    /**
     * Skip antrian (pasien tidak hadir saat dipanggil)
     */
    public function skip()
    {
        if ($this->status !== self::STATUS_CALLED) {
            throw new \Exception('Only called queue can be skipped');
        }

        $this->update(['status' => self::STATUS_SKIPPED]);
        
        return $this;
    }

    // ==========================================
    // STATIC HELPER METHODS
    // ==========================================

    /**
     * Generate next queue number untuk tanggal & dokter tertentu
     */
    public static function getNextNumber($date, $doctorUserId = null)
    {
        $query = static::whereDate('queue_date', $date);
        
        // PILIHAN A: Global queue number per hari
        // (Semua dokter share nomor antrian yang sama)
        
        // PILIHAN B: Per-doctor queue number per hari (RECOMMENDED)
        if ($doctorUserId) {
            $query->where('doctor_user_id', $doctorUserId);
        }
        
        $lastQueue = $query->orderByDesc('queue_number')->first();
        
        return ($lastQueue?->queue_number ?? 0) + 1;
    }

    /**
     * Create queue dari appointment (auto-called saat check-in)
     */
    public static function createFromAppointment(Appointment $appointment)
    {
        // Cek apakah sudah punya queue
        if ($appointment->queue) {
            return $appointment->queue;
        }

        return static::create([
            'appointment_id' => $appointment->appointment_id,
            'patient_id' => $appointment->patient_id,
            'doctor_user_id' => $appointment->doctor_user_id,
            'queue_number' => static::getNextNumber(
                $appointment->scheduled_start_at->toDateString(), 
                $appointment->doctor_user_id
            ),
            'queue_date' => $appointment->scheduled_start_at->toDateString(),
            'estimated_time' => $appointment->scheduled_start_at->format('H:i:s'),
            'complaint' => $appointment->complaint,
            'status' => self::STATUS_WAITING,
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Create queue untuk walk-in patient
     */
    public static function createForWalkIn($patientId, $doctorUserId, $complaint = null)
    {
        return static::create([
            'appointment_id' => null, // Walk-in = no appointment
            'patient_id' => $patientId,
            'doctor_user_id' => $doctorUserId,
            'queue_number' => static::getNextNumber(today(), $doctorUserId),
            'queue_date' => today(),
            'complaint' => $complaint,
            'status' => self::STATUS_WAITING,
            'checked_in_at' => now(),
        ]);
    }

    /**
     * Get current queue being served (status IN_TREATMENT)
     */
    public static function getCurrentQueue($doctorUserId, $date = null)
    {
        $date = $date ?? today();
        
        return static::forDoctor($doctorUserId)
            ->forDate($date)
            ->where('status', self::STATUS_IN_TREATMENT)
            ->first();
    }

    /**
     * Get next queue to be called (status WAITING, urutan terkecil)
     */
    public static function getNextQueue($doctorUserId, $date = null)
    {
        $date = $date ?? today();
        
        return static::forDoctor($doctorUserId)
            ->forDate($date)
            ->where('status', self::STATUS_WAITING)
            ->orderBy('queue_number', 'asc')
            ->first();
    }

    /**
     * Get queue statistics for today
     */
    public static function getTodayStats($doctorUserId = null)
    {
        $query = static::today();
        
        if ($doctorUserId) {
            $query->forDoctor($doctorUserId);
        }

        return [
            'total' => $query->count(),
            'waiting' => (clone $query)->where('status', self::STATUS_WAITING)->count(),
            'called' => (clone $query)->where('status', self::STATUS_CALLED)->count(),
            'in_treatment' => (clone $query)->where('status', self::STATUS_IN_TREATMENT)->count(),
            'done' => (clone $query)->where('status', self::STATUS_DONE)->count(),
            'cancelled' => (clone $query)->where('status', self::STATUS_CANCELLED)->count(),
            'skipped' => (clone $query)->where('status', self::STATUS_SKIPPED)->count(),
            'avg_waiting_time' => static::getAverageWaitingTime($doctorUserId),
        ];
    }

    /**
     * Get average waiting time (in minutes)
     */
    public static function getAverageWaitingTime($doctorUserId = null)
    {
        $query = static::today()
            ->where('status', self::STATUS_DONE)
            ->whereNotNull('checked_in_at')
            ->whereNotNull('started_at');
        
        if ($doctorUserId) {
            $query->forDoctor($doctorUserId);
        }

        $queues = $query->get();

        if ($queues->isEmpty()) {
            return 0;
        }

        $totalMinutes = $queues->sum(function ($queue) {
            return $queue->started_at->diffInMinutes($queue->checked_in_at);
        });

        return round($totalMinutes / $queues->count());
    }
}
