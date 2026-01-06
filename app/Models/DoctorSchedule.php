<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'doctor_user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Schedule belongs to Doctor
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_user_id', 'doctor_user_id');
    }

    /**
     * Scope: Active schedules only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Effective schedules (within date range)
     */
    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now();
        
        return $query->where(function ($q) use ($date) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('effective_to')
              ->orWhere('effective_to', '>=', $date);
        });
    }

    /**
     * Scope: By day of week
     */
    public function scopeByDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope: Order by day and time
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('day_of_week')->orderBy('start_time');
    }

    /**
     * Accessor: Day name in Indonesian
     */
    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $days[$this->day_of_week] ?? '-';
    }

    /**
     * Accessor: Day name in English
     */
    public function getDayNameEnAttribute()
    {
        $days = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        return $days[$this->day_of_week] ?? '-';
    }

    /**
     * Accessor: Time range formatted
     */
    public function getTimeRangeAttribute()
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . 
               date('H:i', strtotime($this->end_time));
    }

    /**
     * Accessor: Full schedule display
     */
    public function getFullScheduleAttribute()
    {
        return "{$this->day_name}: {$this->time_range}";
    }

    /**
     * Check if schedule is effective for a given date
     */
    public function isEffectiveOn($date)
    {
        $date = is_string($date) ? \Carbon\Carbon::parse($date) : $date;

        // Check effective_from
        if ($this->effective_from && $date->lt($this->effective_from)) {
            return false;
        }

        // Check effective_to
        if ($this->effective_to && $date->gt($this->effective_to)) {
            return false;
        }

        return true;
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutesAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        return $start->diffInMinutes($end);
    }
}