<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedPatientId = $request->session()->get('selected_patient_id');

        if (!$selectedPatientId) {
            return redirect()->route('patient.dashboard');
        }

        $patient = Patient::where('patient_id', $selectedPatientId)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();

        // Get filter
        $filter = $request->get('status', 'all');

        // Query appointments
        $query = Appointment::with(['doctor.user', 'service'])
            ->where('patient_id', $patient->patient_id);

        // Apply filters
        if ($filter == 'upcoming') {
            $query->whereIn('status', ['BOOKED', 'CHECKED_IN'])
                ->where('scheduled_start_at', '>=', now());
        } elseif ($filter == 'completed') {
            $query->where('status', 'COMPLETED');
        } elseif ($filter == 'cancelled') {
            $query->where('status', 'CANCELLED');
        }

        $appointments = $query->orderBy('scheduled_start_at', 'desc')
            ->paginate(10);

        // Get counts for tabs
        $counts = [
            'all' => Appointment::where('patient_id', $patient->patient_id)->count(),
            'upcoming' => Appointment::where('patient_id', $patient->patient_id)
                ->whereIn('status', ['BOOKED', 'CHECKED_IN'])
                ->where('scheduled_start_at', '>=', now())
                ->count(),
            'completed' => Appointment::where('patient_id', $patient->patient_id)
                ->where('status', 'COMPLETED')
                ->count(),
            'cancelled' => Appointment::where('patient_id', $patient->patient_id)
                ->where('status', 'CANCELLED')
                ->count(),
        ];

        return view('pasien.appointments.index', compact(
            'appointments',
            'filter',
            'counts',
            'patient'
        ));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $selectedPatientId = $request->session()->get('selected_patient_id');

        if (!$selectedPatientId) {
            return redirect()->route('patient.dashboard');
        }

        $patient = Patient::where('patient_id', $selectedPatientId)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();

        // Get all patients belonging to this user
        $patients = Patient::where('owner_user_id', $user->id)
            ->orderBy('full_name')
            ->get();

        // Get active services
        $services = Service::where('is_active', true)
            ->orderBy('service_name')
            ->get();

        // Get active doctors
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->get();

        return view('pasien.appointments.create', compact(
            'patients',
            'services',
            'doctors',
            'patient'
        ));
    }

    /**
     * Get available time slots for a doctor on a specific date (AJAX)
     */
    public function getSlots(Request $request)
    {
        $date = $request->get('date');
        $doctorId = $request->get('doctor_id');
        $serviceId = $request->get('service_id');

        if (!$date || !$doctorId || !$serviceId) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        try {
            $carbonDate = Carbon::parse($date);
            $dayOfWeek = $carbonDate->dayOfWeek; // 0=Sunday, 6=Saturday

            // Get service duration
            $service = Service::findOrFail($serviceId);
            $durationMinutes = $service->duration_minutes ?? 30;

            // Get doctor's schedule for this day
            $schedule = DoctorSchedule::where('doctor_user_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->where(function($q) use ($carbonDate) {
                    $q->whereNull('effective_from')
                      ->orWhere('effective_from', '<=', $carbonDate);
                })
                ->where(function($q) use ($carbonDate) {
                    $q->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $carbonDate);
                })
                ->first();

            if (!$schedule) {
                return response()->json(['slots' => []]);
            }

            // Get existing appointments for this doctor on this date
            $existingAppointments = Appointment::where('doctor_user_id', $doctorId)
                ->whereDate('scheduled_start_at', $date)
                ->whereIn('status', ['BOOKED', 'CHECKED_IN', 'IN_TREATMENT'])
                ->get();

            // Generate time slots
            $slots = [];
            $startTime = Carbon::parse($date . ' ' . $schedule->start_time);
            $endTime = Carbon::parse($date . ' ' . $schedule->end_time);
            $now = Carbon::now();

            while ($startTime->copy()->addMinutes($durationMinutes)->lte($endTime)) {
                $slotEnd = $startTime->copy()->addMinutes($durationMinutes);

                // Skip past time slots
                if ($startTime->lte($now)) {
                    $startTime->addMinutes(30); // Move to next slot
                    continue;
                }

                // Check if slot conflicts with existing appointments
                $isAvailable = true;
                foreach ($existingAppointments as $apt) {
                    $aptStart = Carbon::parse($apt->scheduled_start_at);
                    $aptEnd = Carbon::parse($apt->scheduled_end_at);

                    // Check for overlap
                    if ($startTime->lt($aptEnd) && $slotEnd->gt($aptStart)) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $slots[] = [
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'display' => $startTime->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                    ];
                }

                $startTime->addMinutes(30); // Next slot (30-minute intervals)
            }

            return response()->json(['slots' => $slots]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $selectedPatientId = $request->session()->get('selected_patient_id');

        if (!$selectedPatientId) {
            return redirect()->route('patient.dashboard')
                ->with('error', 'Silakan pilih pasien terlebih dahulu');
        }

        $patient = Patient::where('patient_id', $selectedPatientId)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();

        $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'doctor_id' => 'required|exists:doctors,doctor_user_id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'complaint' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Get service to calculate duration
            $service = Service::findOrFail($request->service_id);
            $durationMinutes = $service->duration_minutes ?? 30;

            // Parse datetime
            $scheduledStart = Carbon::parse($request->date . ' ' . $request->start_time);
            $scheduledEnd = $scheduledStart->copy()->addMinutes($durationMinutes);

            // Generate queue number otomatis berdasarkan urutan booking
            $queueNumber = Appointment::generateQueueNumber($request->doctor_id, $request->date);
            $queueDate = Carbon::parse($request->date)->format('Y-m-d');

            // Check for conflicts
            $conflict = Appointment::where('doctor_user_id', $request->doctor_id)
                ->whereIn('status', ['BOOKED', 'CHECKED_IN', 'IN_TREATMENT'])
                ->where(function($q) use ($scheduledStart, $scheduledEnd) {
                    $q->where(function($q2) use ($scheduledStart, $scheduledEnd) {
                        $q2->where('scheduled_start_at', '<', $scheduledEnd)
                           ->where('scheduled_end_at', '>', $scheduledStart);
                    });
                })
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return back()->with('error', 'Waktu yang dipilih sudah terisi. Silakan pilih waktu lain.')
                    ->withInput();
            }

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->patient_id,
                'service_id' => $request->service_id,
                'doctor_user_id' => $request->doctor_id,
                'scheduled_start_at' => $scheduledStart,
                'scheduled_end_at' => $scheduledEnd,
                'status' => 'BOOKED',
                'booking_source' => 'WEB',
                'complaint' => $request->complaint,
            ]);

            DB::commit();

            return redirect()->route('patient.appointments.show', $appointment->appointment_id)
                ->with('success', 'Janji temu berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat janji temu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified appointment
     */
    public function show($appointmentId)
    {
        $user = Auth::user();

        $appointment = Appointment::with(['patient', 'service', 'doctor.user'])
            ->findOrFail($appointmentId);

        // Check if appointment belongs to user's patient
        if ($appointment->patient->owner_user_id != $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('pasien.appointments.show', compact('appointment'));
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $appointmentId)
    {
        $user = Auth::user();

        $appointment = Appointment::findOrFail($appointmentId);

        // Check if appointment belongs to user's patient
        if ($appointment->patient->owner_user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if appointment can be cancelled
        if (!in_array($appointment->status, ['BOOKED', 'CHECKED_IN'])) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment tidak dapat dibatalkan'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $appointment->update([
                'status' => 'CANCELLED',
                'cancel_reason' => $request->input('reason', 'Dibatalkan oleh pasien'),
                'cancelled_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Appointment berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan appointment: ' . $e->getMessage()
            ], 500);
        }
    }
}
