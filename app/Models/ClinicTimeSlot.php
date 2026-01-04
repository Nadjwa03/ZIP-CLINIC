<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicTimeSlot extends Model
{
    protected $fillable = [
        'slot_date',
        'start_time',
        'end_time',
        'capacity',
        'is_closed',
    ];

    protected $casts = [
        'slot_date' => 'date',
        'is_closed' => 'boolean',
        'capacity' => 'integer',
    ];

    // Relationships
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'slot_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_closed', false)
            ->where('slot_date', '>=', now()->toDateString());
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('slot_date', $date);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    // Methods
    public function getBookedCountAttribute()
    {
        return $this->appointments()
            ->whereIn('status', ['BOOKED', 'APPROVED', 'CHECKED_IN', 'IN_TREATMENT'])
            ->count();
    }

    public function getRemainingCapacityAttribute()
    {
        return $this->capacity - $this->booked_count;
    }

    public function isAvailableForBooking()
    {
        if ($this->is_closed) {
            return false;
        }

        if ($this->slot_date < now()->toDateString()) {
            return false;
        }

        return $this->remaining_capacity > 0;
    }

    // Generate slots for a date range
    public static function generateSlotsForDateRange($startDate, $endDate, $startTime = '08:00', $endTime = '17:00', $capacity = 3)
    {
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            new \DateTime($endDate . ' +1 day')
        );

        foreach ($period as $date) {
            // Skip Sundays (day 0)
            if ($date->format('w') == 0) {
                continue;
            }

            $currentTime = \Carbon\Carbon::parse($startTime);
            $endTimeCarbon = \Carbon\Carbon::parse($endTime);

            while ($currentTime < $endTimeCarbon) {
                $slotStart = $currentTime->copy();
                $slotEnd = $currentTime->copy()->addMinutes(15);

                static::updateOrCreate([
                    'slot_date' => $date->format('Y-m-d'),
                    'start_time' => $slotStart->format('H:i:s'),
                ], [
                    'end_time' => $slotEnd->format('H:i:s'),
                    'capacity' => $capacity,
                    'is_closed' => false,
                ]);

                $currentTime->addMinutes(15);
            }
        }
    }
}