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
            $selectedPatient = $patients->firstWhere('patient_id', $selectedPatientId) ?? $patients->first();

            // Set in session if not set
            if (!$selectedPatientId) {
                $request->session()->put('selected_patient_id', $selectedPatient->patient_id);
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
                ->with(['service', 'doctor'])
                ->whereIn('status', ['BOOKED', 'CHECKED_IN'])
                ->where('scheduled_start_at', '>=', now())
                ->orderBy('scheduled_start_at')
                ->take(5)
                ->get()
            : collect();

        return view('pasien.index', [
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'stats' => $stats,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
    
    /**
     * Switch patient
     */
    public function switchPatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id'
        ]);

        $user = Auth::user();
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();

        // Store selected patient in session
        $request->session()->put('selected_patient_id', $patient->patient_id);

        return response()->json([
            'success' => true,
            'message' => 'Patient switched successfully'
        ]);
    }
}