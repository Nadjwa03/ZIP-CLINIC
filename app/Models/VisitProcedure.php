<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitProcedure extends Model
{
    protected $fillable = [
        'visit_id',
        'service_id',
        'tooth_codes',
        'diagnosis_note',
        'treatment_note',
        'remarks',
        'performed_by_user_id',
    ];

    // Relationships
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    // Accessors
    public function getToothCodesArrayAttribute()
    {
        return $this->tooth_codes ? explode(',', $this->tooth_codes) : [];
    }

    // Methods
    public function setToothCodes(array $codes)
    {
        $this->tooth_codes = implode(',', $codes);
        $this->save();
    }
}