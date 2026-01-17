<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Data dokter tidak ditemukan.');
        }

        // Get filter status from query string
        $status = $request->get('status');

        // Build query
        $query = Appointment::where('doctor_user_id', $doctor->doctor_user_id)
            ->with(['patient', 'service', 'doctor.user', 'doctor.speciality'])
            ->orderBy('scheduled_start_at', 'desc');

        // Apply status filter if provided
        if ($status && $status !== 'all') {
            $query->where('status', strtoupper($status));
        }

        $appointments = $query->paginate(15);

        // Get counts for filter tabs
        $counts = [
            'all' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)->count(),
            'booked' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)->where('status', 'BOOKED')->count(),
            'checked_in' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)->where('status', 'CHECKED_IN')->count(),
            'in_treatment' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)->where('status', 'IN_TREATMENT')->count(),
            'completed' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)->where('status', 'COMPLETED')->count(),
            'cancelled' => Appointment::where('doctor_user_id', $doctor->doctor_user_id)->where('status', 'CANCELLED')->count(),
        ];

        return view('doctor.appointments.index', compact('appointments', 'counts', 'status'));
    }

    
    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        // Ensure the appointment belongs to this doctor
        if ($appointment->doctor_user_id !== $doctor->doctor_user_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        $appointment->load(['patient', 'service', 'doctor.user', 'doctor.speciality']);

        return view('doctor.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        // Ensure the appointment belongs to this doctor
        if ($appointment->doctor_user_id !== $doctor->doctor_user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:BOOKED,CHECKED_IN,IN_TREATMENT,COMPLETED,CANCELLED'
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'appointment' => $appointment->load(['patient', 'service'])
        ]);
    }
}
