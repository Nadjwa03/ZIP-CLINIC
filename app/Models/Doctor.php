<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'speciality',
        'license_number',
        'phone',
        'bio',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the doctor
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get doctor schedules
     */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get doctor appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get doctor name from user relationship
     */
    public function getNameAttribute()
    {
        return $this->user->name ?? '';
    }

    /**
     * Get doctor email from user relationship
     */
    public function getEmailAttribute()
    {
        return $this->user->email ?? '';
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        return null;
    }

    /**
     * Scope to get only active doctors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by speciality
     */
    public function scopeOfSpeciality($query, $speciality)
    {
        return $query->where('speciality', $speciality);
    }

    /**
     * Scope to get doctors for landing page
     * Returns active doctors with their user relationship
     */
    public function scopeForLanding($query, $limit = 4)
    {
        return $query->with('user')
                     ->where('is_active', true)
                     ->limit($limit);
    }
}
