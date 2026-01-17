<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;

    protected $table = 'doctors';
    protected $primaryKey = 'doctor_user_id';
    public $incrementing = false; // Primary key is not auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'doctor_user_id',
        'registration_number',
        'display_name',
        'speciality_id',
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
        return $this->belongsTo(User::class, 'doctor_user_id', 'id');
    }

    /**
     * Get the speciality of this doctor
     */
    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id', 'speciality_id');
    }

    /**
     * Get doctor schedules
     */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_user_id', 'doctor_user_id');
    }

    /**
     * Get doctor appointments
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_user_id', 'doctor_user_id');
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
        $specialityId = $speciality instanceof \App\Models\Speciality
            ? $speciality->speciality_id
            : $speciality;

        return $query->where('speciality_id', $specialityId);
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

    /**
     * Check if doctor is available at specific date and time
     */
    public function isAvailableAt($dateTime)
    {
        $date = \Carbon\Carbon::parse($dateTime);
        $dayOfWeek = $date->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        $time = $date->format('H:i:s');

        // Get schedules for this day
        $schedules = $this->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->whereNull('effective_from')
            ->whereNull('effective_to')
            ->get();

        // Check if time falls within any schedule
        foreach ($schedules as $schedule) {
            if ($time >= $schedule->start_time && $time < $schedule->end_time) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots($date, $slotDuration = 30)
    {
        $date = \Carbon\Carbon::parse($date);
        $dayOfWeek = $date->dayOfWeekIso;

        // Get schedules for this day
        $schedules = $this->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->whereNull('effective_from')
            ->whereNull('effective_to')
            ->orderBy('start_time')
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            // Parse start and end time
            $start = \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
            $end = \Carbon\Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

            while ($start->lt($end)) {
                $slotEnd = $start->copy()->addMinutes($slotDuration);

                if ($slotEnd->lte($end)) {
                    $slots[] = [
                        'start' => $start->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'datetime' => $start->format('Y-m-d H:i:s'),
                    ];
                }

                $start->addMinutes($slotDuration);
            }
        }

        return $slots;
    }
}
