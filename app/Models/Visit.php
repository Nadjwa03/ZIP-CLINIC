<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    protected $primaryKey = 'visit_id';

    protected $fillable = [
        'appointment_id',
        'queue_id',
        'patient_id',
        'doctor_user_id',
        'visit_at',
        'status',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'notes',
        'follow_up_at',
    ];

    protected $casts = [
        'visit_at' => 'datetime',
        'follow_up_at' => 'datetime',
    ];

    // Constants for status
    const STATUS_IN_TREATMENT = 'IN_TREATMENT';
    const STATUS_DONE = 'DONE';
    const STATUS_FOLLOW_UP = 'FOLLOW_UP';
    const STATUS_READY_TO_BILL = 'READY_TO_BILL';

    // Relationships
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_user_id');
    }

    /**
 * Get the user account of the doctor (if needed)
 */
    public function doctorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_user_id', 'id');
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(VisitProcedure::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(VisitDetail::class, 'visit_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PatientMedia::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'visit_id', 'visit_id');
    }

    // Scopes
    public function scopeInTreatment($query)
    {
        return $query->where('status', self::STATUS_IN_TREATMENT);
    }

    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    public function scopeFollowUp($query)
    {
        return $query->where('status', self::STATUS_FOLLOW_UP);
    }

    public function scopeReadyToBill($query)
    {
        return $query->where('status', self::STATUS_READY_TO_BILL);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_user_id', $doctorId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visit_at', now()->toDateString());
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('visit_at', 'desc');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_IN_TREATMENT => '<span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">In Treatment</span>',
            self::STATUS_DONE => '<span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Done</span>',
            self::STATUS_FOLLOW_UP => '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Follow Up</span>',
            self::STATUS_READY_TO_BILL => '<span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">Ready to Bill</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    // SOAP Format Helper
    public function getSOAPSummaryAttribute()
    {
        return [
            'subjective' => $this->subjective,
            'objective' => $this->objective,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
        ];
    }

    // Methods
    public function markAsReadyToBill()
    {
        $this->update(['status' => self::STATUS_READY_TO_BILL]);
    }

    public function complete()
    {
        $this->update(['status' => self::STATUS_DONE]);
    }

    public function scheduleFollowUp($date)
    {
        $this->update([
            'status' => self::STATUS_FOLLOW_UP,
            'follow_up_at' => $date,
        ]);
    }

    /**
     * Add detail perawatan per-gigi ke visit ini
     */
    public function addDetail(array $data)
    {
        return $this->details()->create([
            'tooth_codes' => $data['tooth_codes'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'diagnosis_note' => $data['diagnosis_note'] ?? null,
            'treatment_note' => $data['treatment_note'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'entered_by' => $data['entered_by'] ?? \Auth::id(),
        ]);
    }

    /**
     * Update SOAP data untuk visit ini
     */
    public function updateSOAP(array $soapData)
    {
        return $this->update([
            'subjective' => $soapData['subjective'] ?? $this->subjective,
            'objective' => $soapData['objective'] ?? $this->objective,
            'assessment' => $soapData['assessment'] ?? $this->assessment,
            'plan' => $soapData['plan'] ?? $this->plan,
            'notes' => $soapData['notes'] ?? $this->notes,
            'follow_up_at' => $soapData['follow_up_at'] ?? $this->follow_up_at,
        ]);
    }
}