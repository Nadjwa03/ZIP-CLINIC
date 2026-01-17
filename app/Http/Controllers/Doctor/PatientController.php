<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display list of doctor's patients
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get doctor record
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            abort(403, 'Anda tidak terdaftar sebagai dokter');
        }

        // Get search term
        $search = $request->get('search');
        $status = $request->get('status'); // 'active' or 'inactive'

        // Get patients that have appointments with this doctor
        $patients = Patient::whereHas('appointments', function($q) use ($doctor) {
                $q->where('doctor_user_id', $doctor->doctor_user_id);
            })
            // Add appointments count
            ->withCount(['appointments' => function($q) use ($doctor) {
                $q->where('doctor_user_id', $doctor->doctor_user_id);
            }])
            // Get last appointment date
            ->with(['appointments' => function($q) use ($doctor) {
                $q->where('doctor_user_id', $doctor->doctor_user_id)
                  ->latest('scheduled_start_at')
                  ->limit(1)
                  ->select('patient_id', 'scheduled_start_at');
            }])
            // Search filter
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('full_name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%')
                          ->orWhere('medical_record_number', 'like', '%' . $search . '%');
                });
            })
            // Status filter (active/inactive based on last visit)
            ->when($status === 'active', function($q) {
                // Has appointment in last 3 months
                $q->whereHas('appointments', function($query) {
                    $query->where('scheduled_start_at', '>=', Carbon::now()->subMonths(3));
                });
            })
            ->when($status === 'inactive', function($q) {
                // No appointment in last 3 months
                $q->whereDoesntHave('appointments', function($query) {
                    $query->where('scheduled_start_at', '>=', Carbon::now()->subMonths(3));
                });
            })
            ->orderBy('full_name')
            ->paginate(20);

        // Add status to each patient
        $patients->each(function($patient) {
            $lastAppointment = $patient->appointments->first();
            $patient->last_visit = $lastAppointment ? $lastAppointment->scheduled_start_at : null;
            $patient->is_active = $lastAppointment && 
                $lastAppointment->scheduled_start_at->gte(Carbon::now()->subMonths(3));
        });

        return view('doctor.patients.index', compact('doctor', 'patients', 'search', 'status'));
    }

    /**
     * Display patient detail
     */
    public function show($patientId)
    {
        $user = Auth::user();

        // Get doctor record
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            abort(403, 'Anda tidak terdaftar sebagai dokter');
        }

        // Get patient
        $patient = Patient::with(['appointments' => function($q) use ($doctor) {
                $q->where('doctor_user_id', $doctor->doctor_user_id);
            }])
            ->findOrFail($patientId);

        // Verify doctor has access to this patient
        $hasAccess = $patient->appointments()
            ->where('doctor_user_id', $doctor->doctor_user_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke pasien ini');
        }

        // Get upcoming appointments
        $upcomingAppointments = $patient->appointments()
            ->where('doctor_user_id', $doctor->doctor_user_id)
            ->where('scheduled_start_at', '>', Carbon::now())
            ->whereIn('status', ['BOOKED', 'CHECKED_IN'])
            ->with(['service', 'doctor.user'])
            ->orderBy('scheduled_start_at')
            ->get();

        // Get past appointments (COMPLETED, CANCELLED, NO_SHOW)
        $pastAppointments = $patient->appointments()
            ->where('doctor_user_id', $doctor->doctor_user_id)
            ->whereIn('status', ['COMPLETED', 'CANCELLED', 'NO_SHOW'])
            ->with(['service', 'doctor.user'])
            ->orderBy('scheduled_start_at', 'desc')
            ->get();

        // Calculate stats
        $stats = [
            'past_count' => $pastAppointments->count(),
            'upcoming_count' => $upcomingAppointments->count(),
            'total_count' => $pastAppointments->count() + $upcomingAppointments->count(),
        ];

        // Get last visit
        $lastVisit = $pastAppointments->first();

        // Patient is active if visited in last 3 months
        $isActive = $lastVisit && $lastVisit->scheduled_start_at->gte(Carbon::now()->subMonths(3));

        return view('doctor.patients.show', compact(
            'doctor',
            'patient',
            'upcomingAppointments',
            'pastAppointments',
            'stats',
            'lastVisit',
            'isActive'
        ));
    }
}
