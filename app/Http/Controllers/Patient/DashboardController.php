<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display patient dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all patients belonging to this user
        $patients = Patient::where('owner_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $selectedPatient = null;
if ($patients->isNotEmpty()) {
    $selectedPatientId = $request->session()->get('selected_patient_id');
    $selectedPatient = $patients->firstWhere('id', $selectedPatientId) ?? $patients->first();
    
    // Set in session if not set
    if (!$selectedPatientId) {
        $request->session()->put('selected_patient_id', $selectedPatient->id);
    }
}

// Get stats (0 if no patient)
$stats = [
    'appointments' => $selectedPatient ? $selectedPatient->appointments()->count() : 0,
    'visits' => $selectedPatient ? $selectedPatient->visits()->count() : 0,
    'completed_visits' => $selectedPatient ? $selectedPatient->visits()->where('status', 'COMPLETED')->count() : 0,
];

// Get upcoming appointments (empty if no patient)
$upcomingAppointments = $selectedPatient 
    ? $selectedPatient->appointments()
        ->with(['service'])
        ->whereIn('status', ['BOOKED', 'APPROVED'])
        ->where('appointment_date', '>=', now()->toDateString())
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->take(5)
        ->get()
    : collect();

return view('pasien.index', [
    'patients' => $patients,
    'selectedPatient' => $selectedPatient,
    'stats' => $stats,
    'upcomingAppointments' => $upcomingAppointments,
]);
        
        // Get selected patient (from session or first patient)
        $selectedPatientId = $request->session()->get('selected_patient_id');
        $patient = null;
        
        if ($selectedPatientId) {
            $patient = $patients->firstWhere('id', $selectedPatientId);
        }
        
        if (!$patient) {
            $patient = $patients->first();
            $request->session()->put('selected_patient_id', $patient->id);
        }
        
        // Get stats
        $stats = [
            'appointments' => Appointment::where('patient_id', $patient->id)
                ->whereIn('status', ['BOOKED', 'APPROVED', 'CHECKED_IN'])
                ->count(),
            
            'visits' => Visit::where('patient_id', $patient->id)
                ->count(),
            
            'completed_visits' => Visit::where('patient_id', $patient->id)
                ->whereHas('appointment', function($query) {
                    $query->where('status', 'COMPLETED');
                })
                ->count(),
        ];
        
        // Upcoming appointments (next 5)
        $upcomingAppointments = Appointment::with([
                'preferredDoctor.user',
                'assignedDoctor.user', 
                'service',
                'slot'
            ])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['BOOKED', 'APPROVED', 'CHECKED_IN'])
            ->whereHas('slot', function($query) {
                $query->where('slot_date', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('pasien.index', compact(
            'patient',
            'patients',
            'stats',
            'upcomingAppointments'
        ));
    }
    
    /**
     * Switch patient
     */
    public function switchPatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id'
        ]);
        
        $user = Auth::user();
        $patient = Patient::where('id', $request->patient_id)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();
        
        // Store selected patient in session
        $request->session()->put('selected_patient_id', $patient->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Patient switched successfully'
        ]);
    }
}