<?php

namespace App\Http\Livewire\Doctor\Dashboard;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TodayAppointments extends Component
{
    protected $listeners = ['appointmentUpdated' => '$refresh'];

    public function render()
    {
        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            $todayAppointments = collect();
            $upcomingAppointments = collect();
        } else {
            $today = Carbon::today();
            $nextWeek = Carbon::today()->addDays(7);

            $todayAppointments = Appointment::where('doctor_user_id', $doctor->doctor_user_id)
                ->whereDate('scheduled_start_at', $today)
                ->with(['patient', 'service'])
                ->orderBy('scheduled_start_at', 'asc')
                ->get();

            $upcomingAppointments = Appointment::where('doctor_user_id', $doctor->doctor_user_id)
                ->whereDate('scheduled_start_at', '>', $today)
                ->whereDate('scheduled_start_at', '<=', $nextWeek)
                ->where('status', 'BOOKED')
                ->with(['patient', 'service'])
                ->orderBy('scheduled_start_at', 'asc')
                ->limit(5)
                ->get();
        }

        return view('livewire.doctor.dashboard.today-appointments', [
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
