<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_user_id',      // ← TAMBAHKAN INI
        'secret_code',        // ← TAMBAHKAN INI  
        'claimed_at',         // ← TAMBAHKAN INI
        'medical_record_number',
        'full_name',
        'id_type',
        'id_number',
        'date_of_birth',
        'gender',
        'blood_type',
        'email',
        'phone',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'allergies',
        'medical_history',
        'is_active',
        'last_visit_at',
        'last_treatment',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'last_visit_at' => 'datetime',
        'claimed_at' => 'datetime',
    ];

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Get formatted last visit date
     */
    public function getFormattedLastVisitAttribute()
    {
        if (!$this->last_visit_at) {
            return '-';
        }
        
        return $this->last_visit_at->format('d M Y');
    }

    /**
     * Get formatted registered date
     */
    public function getFormattedRegisteredAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    /**
     * Relationships
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get all media for this patient
     */
    public function media()
    {
        return $this->hasMany(PatientMedia::class)->orderBy('taken_at', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get only active media
     */
    public function activeMedia()
    {
        return $this->hasMany(PatientMedia::class)->active()->orderBy('taken_at', 'desc');
    }

    /**
     * Get only photos
     */
    public function photos()
    {
        return $this->hasMany(PatientMedia::class)->photos()->active()->orderBy('taken_at', 'desc');
    }

    /**
     * Get only x-rays
     */
    public function xrays()
    {
        return $this->hasMany(PatientMedia::class)->xrays()->active()->orderBy('taken_at', 'desc');
    }

    /**
     * Get only documents
     */
    public function documents()
    {
        return $this->hasMany(PatientMedia::class)->documents()->active()->orderBy('taken_at', 'desc');
    }

    /**
     * Update last visit information
     */
    public function updateLastVisit($treatment = null)
    {
        $this->update([
            'last_visit_at' => now(),
            'last_treatment' => $treatment,
        ]);
    }
}