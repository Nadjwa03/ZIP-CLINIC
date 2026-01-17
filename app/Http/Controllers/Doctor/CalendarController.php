<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display doctor's calendar view
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get doctor record
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            abort(403, 'Anda tidak terdaftar sebagai dokter');
        }

        // Get start date (default: current week's Monday)
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->startOfWeek();
        
        $endDate = $startDate->copy()->addDays(6); // 7 days (Mon-Sun)

        // Get appointments for the week
        $appointments = Appointment::where('doctor_user_id', $doctor->doctor_user_id)
            ->whereBetween('scheduled_start_at', [$startDate, $endDate->endOfDay()])
            ->with(['patient', 'service'])
            ->orderBy('scheduled_start_at')
            ->get();

        // Group appointments by date
        $appointmentsByDate = $appointments->groupBy(function($appointment) {
            return $appointment->scheduled_start_at->format('Y-m-d');
        });

        // Generate week days array
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $weekDays[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->translatedFormat('D'),
                'day_number' => $date->format('d'),
                'month' => $date->translatedFormat('M'),
                'is_today' => $date->isToday(),
                'appointments' => $appointmentsByDate->get($date->format('Y-m-d'), collect()),
            ];
        }

        // Generate time slots (16:00 - 21:00 WITA, 30-minute intervals)
        $timeSlots = [];
        $start = Carbon::createFromTime(16, 0);
        $end = Carbon::createFromTime(21, 0);

        while ($start->lt($end)) {
            $timeSlots[] = [
                'time' => $start->format('H:i'),
                'label' => $start->format('H:i'),
            ];
            $start->addMinutes(30);
        }

        // Current time indicator (for today)
        $currentTime = Carbon::now();
        $showCurrentTime = $currentTime->between($startDate, $endDate);
        
        return view('doctor.calendar.index', compact(
            'doctor',
            'weekDays',
            'timeSlots',
            'startDate',
            'endDate',
            'currentTime',
            'showCurrentTime'
        ));
    }
}
