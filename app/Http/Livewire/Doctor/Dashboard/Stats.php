<?php

namespace App\Http\Livewire\Doctor\Dashboard;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Stats extends Component
{
    public $stats = [];

    protected $listeners = ['appointmentUpdated' => '$refresh'];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            $this->stats = [
                'today_total' => 0,
                'today_waiting' => 0,
                'today_in_treatment' => 0,
                'today_completed' => 0,
                'total_patients' => 0,
            ];
            return;
        }

        $today = Carbon::today();
        $todayAppointments = Appointment::where('doctor_user_id', $doctor->doctor_user_id)
            ->whereDate('scheduled_start_at', $today)
            ->get();

        $this->stats = [
            'today_total' => $todayAppointments->count(),
            'today_waiting' => $todayAppointments->where('status', 'BOOKED')->count() +
                              $todayAppointments->where('status', 'CHECKED_IN')->count(),
            'today_in_treatment' => $todayAppointments->where('status', 'IN_TREATMENT')->count(),
            'today_completed' => $todayAppointments->where('status', 'COMPLETED')->count(),
            'total_patients' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)
                ->distinct('patient_id')
                ->count('patient_id'),
        ];
    }

    public function render()
    {
        $this->loadStats();
        return view('livewire.doctor.dashboard.stats');
    }
}
