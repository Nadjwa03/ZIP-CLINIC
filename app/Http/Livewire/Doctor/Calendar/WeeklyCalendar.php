<?php

namespace App\Http\Livewire\Doctor\Calendar;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class WeeklyCalendar extends Component
{
    public $startDate;
    public $weekDays = [];
    public $timeSlots = [];

    protected $listeners = ['appointmentUpdated' => 'loadWeek'];

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->loadWeek();
    }

    public function loadWeek()
    {
        $start = Carbon::parse($this->startDate);
        $end = $start->copy()->addDays(6);

        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            $this->weekDays = [];
            return;
        }

        // Get appointments for the week
        $appointments = Appointment::where('doctor_user_id', $doctor->doctor_user_id)
            ->whereBetween('scheduled_start_at', [$start, $end->endOfDay()])
            ->with(['patient', 'service'])
            ->orderBy('scheduled_start_at')
            ->get();

        // Group appointments by date
        $appointmentsByDate = $appointments->groupBy(function($appointment) {
            return Carbon::parse($appointment->scheduled_start_at)->format('Y-m-d');
        });

        // Generate week days array
        $this->weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $this->weekDays[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->translatedFormat('D'),
                'day_number' => $date->format('d'),
                'month' => $date->translatedFormat('M'),
                'is_today' => $date->isToday(),
                'appointments' => $appointmentsByDate->get($date->format('Y-m-d'), collect()),
            ];
        }

        // Generate time slots (16:00 - 21:00 WITA, 30-minute intervals)
        $this->timeSlots = [];
        $timeStart = Carbon::createFromTime(16, 0);
        $timeEnd = Carbon::createFromTime(21, 0);

        while ($timeStart->lt($timeEnd)) {
            $this->timeSlots[] = [
                'time' => $timeStart->format('H:i'),
                'label' => $timeStart->format('H:i'),
            ];
            $timeStart->addMinutes(30);
        }
    }

    public function previousWeek()
    {
        $this->startDate = Carbon::parse($this->startDate)->subWeek()->format('Y-m-d');
        $this->loadWeek();
    }

    public function nextWeek()
    {
        $this->startDate = Carbon::parse($this->startDate)->addWeek()->format('Y-m-d');
        $this->loadWeek();
    }

    public function goToToday()
    {
        $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $this->loadWeek();
    }

    public function render()
    {
        return view('livewire.doctor.calendar.weekly-calendar');
    }
}
