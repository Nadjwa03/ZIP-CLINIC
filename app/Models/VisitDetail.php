<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitDetail extends Model
{
    protected $primaryKey = 'visit_details_id';

    protected $fillable = [
        'visit_id',
        'tooth_codes',
        'service_id',
        'diagnosis_note',
        'treatment_note',
        'remarks',
        'entered_by',
    ];

    // Relationships
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function enteredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    // Scopes
    public function scopeByVisit($query, $visitId)
    {
        return $query->where('visit_id', $visitId);
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    // Accessors
    public function getToothCodesArrayAttribute()
    {
        return $this->tooth_codes ? explode(',', $this->tooth_codes) : [];
    }

    // Methods
    /**
     * Static method untuk create detail perawatan baru
     */
    public static function createDetail($visitId, array $data)
    {
        return self::create([
            'visit_id' => $visitId,
            'tooth_codes' => $data['tooth_codes'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'diagnosis_note' => $data['diagnosis_note'] ?? null,
            'treatment_note' => $data['treatment_note'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'entered_by' => $data['entered_by'] ?? \Auth::id(),
        ]);
    }

    /**
     * Update detail perawatan
     */
    public function updateDetail(array $data)
    {
        return $this->update([
            'tooth_codes' => $data['tooth_codes'] ?? $this->tooth_codes,
            'service_id' => $data['service_id'] ?? $this->service_id,
            'diagnosis_note' => $data['diagnosis_note'] ?? $this->diagnosis_note,
            'treatment_note' => $data['treatment_note'] ?? $this->treatment_note,
            'remarks' => $data['remarks'] ?? $this->remarks,
        ]);
    }
}
