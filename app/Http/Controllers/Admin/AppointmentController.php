<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        // Get filter
        $filter = $request->get('status', 'all');
        $dateFilter = $request->get('date', 'all'); // today, week, month, all
        $doctorFilter = $request->get('doctor');
        
        // Query appointments
        $query = Appointment::with(['patient', 'doctor.user', 'service']);
        
        // Apply status filter
        if ($filter !== 'all') {
            $query->where('status', strtoupper($filter));
        }
        
        // Apply date filter
        if ($dateFilter === 'today') {
            $query->whereDate('scheduled_start_at', today());
        } elseif ($dateFilter === 'week') {
            $query->whereBetween('scheduled_start_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
        } elseif ($dateFilter === 'month') {
            $query->whereMonth('scheduled_start_at', now()->month)
                  ->whereYear('scheduled_start_at', now()->year);
        }
        
        // Apply doctor filter
        if ($doctorFilter) {
            $query->where('doctor_user_id', $doctorFilter);
        }
        
        $appointments = $query->orderBy('scheduled_start_at', 'desc')
            ->paginate(15);
        
        // Get doctors for filter
        $doctors = Doctor::where('is_active', true)->get();
        
        // Get counts for tabs
        $counts = [
            'all' => Appointment::count(),
            'booked' => Appointment::where('status', 'BOOKED')->count(),
            'checked_in' => Appointment::where('status', 'CHECKED_IN')->count(),
            'in_treatment' => Appointment::where('status', 'IN_TREATMENT')->count(),
            'completed' => Appointment::where('status', 'COMPLETED')->count(),
            'cancelled' => Appointment::where('status', 'CANCELLED')->count(),
        ];
        
        return view('admin.appointments.index', compact(
            'appointments',
            'doctors',
            'counts',
            'filter',
            'dateFilter',
            'doctorFilter'
        ));
    }
    
    /**
     * Show the form for creating a new appointment (Manual Booking by Admin)
     */
    public function create(Request $request)
    {
        // Get active patients
        $patients = Patient::where('is_active', true)
            ->orderBy('full_name')
            ->get();
        
        // Get active services
        $services = Service::where('is_active', true)
            ->orderBy('service_name')
            ->get();
        
        // Get active doctors
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->join('users', 'doctors.doctor_user_id', '=', 'users.id')
            ->select('doctors.*')
            ->orderBy('users.name')
            ->get();
        
        // Pre-select patient if provided
        $selectedPatientId = $request->get('patient_id');
        
        return view('admin.appointments.create', compact(
            'patients',
            'services',
            'doctors',
            'selectedPatientId'
        ));
    }
    
    /**
     * Store a newly created appointment (Manual Booking by Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'service_id' => 'required|exists:services,service_id',
            'doctor_user_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'complaint' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Combine date and time
            $scheduledStart = Carbon::parse($validated['scheduled_date'] . ' ' . $validated['scheduled_time']);

            // Validate doctor availability
            $doctor = Doctor::findOrFail($validated['doctor_user_id']);

            if (!$doctor->isAvailableAt($scheduledStart)) {
                DB::rollBack();
                return back()->withErrors([
                    'scheduled_time' => 'Dokter tidak praktek di waktu tersebut. Silakan pilih waktu yang sesuai dengan jadwal dokter.'
                ])->withInput();
            }

            // Check if slot is already booked
            $existingAppointment = Appointment::where('doctor_user_id', $validated['doctor_user_id'])
                ->where('scheduled_start_at', $scheduledStart)
                ->whereNotIn('status', ['CANCELLED'])
                ->exists();

            if ($existingAppointment) {
                DB::rollBack();
                return back()->withErrors([
                    'scheduled_time' => 'Slot waktu ini sudah dibooking oleh pasien lain. Silakan pilih waktu lain.'
                ])->withInput();
            }

            // Get service to calculate end time
            $service = Service::findOrFail($validated['service_id']);
            $scheduledEnd = $scheduledStart->copy()->addMinutes($service->duration_minutes ?? 30);

            // Generate queue number otomatis berdasarkan urutan booking
            $queueNumber = Appointment::generateQueueNumber($validated['doctor_user_id'], $validated['scheduled_date']);
            $queueDate = Carbon::parse($validated['scheduled_date'])->format('Y-m-d');

            // Create appointment with queue number
            $appointment = Appointment::create([
                'patient_id' => $validated['patient_id'],
                'service_id' => $validated['service_id'],
                'doctor_user_id' => $validated['doctor_user_id'],
                'scheduled_start_at' => $scheduledStart,
                'scheduled_end_at' => $scheduledEnd,
                'complaint' => $validated['complaint'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'BOOKED', // Auto-approved by admin
                'booking_source' => 'WALK_IN', // Admin booking = WALK_IN
                'queue_number' => $queueNumber,
                'queue_date' => $queueDate,
            ]);

            DB::commit();

            return redirect()->route('admin.appointments.index')
                ->with('success', 'Appointment berhasil dibuat untuk pasien: ' . $appointment->patient->full_name);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal membuat appointment: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.user', 'service', 'queue']);
        
        return view('admin.appointments.show', compact('appointment'));
    }
    
    /**
     * Show the form for editing the specified appointment
     */
    public function edit(Appointment $appointment)
    {
        // Only allow edit if not completed/cancelled
        if (in_array($appointment->status, ['COMPLETED', 'CANCELLED'])) {
            return redirect()->route('admin.appointments.show', $appointment)
                ->with('error', 'Appointment yang sudah completed/cancelled tidak bisa diedit');
        }
        
        $patients = Patient::where('is_active', true)
            ->orderBy('full_name')
            ->get();
        
        $services = Service::where('is_active', true)
            ->orderBy('service_name')
            ->get();
        
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->join('users', 'doctors.doctor_user_id', '=', 'users.id')
            ->select('doctors.*')
            ->orderBy('users.name')
            ->get();
        
        return view('admin.appointments.edit', compact(
            'appointment',
            'patients',
            'services',
            'doctors'
        ));
    }
    
    /**
     * Update the specified appointment
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Only allow edit if not completed/cancelled
        if (in_array($appointment->status, ['COMPLETED', 'CANCELLED'])) {
            return redirect()->route('admin.appointments.show', $appointment)
                ->with('error', 'Appointment yang sudah completed/cancelled tidak bisa diedit');
        }
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'service_id' => 'required|exists:services,service_id',
            'doctor_user_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required|date_format:H:i',
            'complaint' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:BOOKED,CHECKED_IN,IN_TREATMENT,COMPLETED,CANCELLED',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Combine date and time
            $scheduledStart = Carbon::parse($validated['scheduled_date'] . ' ' . $validated['scheduled_time']);
            
            // Get service to calculate end time
            $service = Service::findOrFail($validated['service_id']);
            $scheduledEnd = $scheduledStart->copy()->addMinutes($service->duration_minutes ?? 30);
            
            // Update appointment
            $appointment->update([
                'patient_id' => $validated['patient_id'],
                'service_id' => $validated['service_id'],
                'doctor_user_id' => $validated['doctor_user_id'],
                'scheduled_start_at' => $scheduledStart,
                'scheduled_end_at' => $scheduledEnd,
                'complaint' => $validated['complaint'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.appointments.show', $appointment)
                ->with('success', 'Appointment berhasil diupdate');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal update appointment: ' . $e->getMessage());
        }
    }
    
    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:BOOKED,CHECKED_IN,IN_TREATMENT,COMPLETED,CANCELLED',
            'cancel_reason' => 'required_if:status,CANCELLED|nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $appointment->update([
                'status' => $validated['status'],
                'cancel_reason' => $validated['status'] === 'CANCELLED' ? $validated['cancel_reason'] : null,
                'cancelled_at' => $validated['status'] === 'CANCELLED' ? now() : null,
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Status appointment berhasil diubah menjadi: ' . $validated['status']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }
    
    /**
     * Cancel appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $appointment->update([
                'status' => 'CANCELLED',
                'cancel_reason' => $validated['cancel_reason'],
                'cancelled_at' => now(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Appointment berhasil dibatalkan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan appointment: ' . $e->getMessage());
        }
    }
    
    /**
     * Get available time slots for a date (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date');
        $doctorId = $request->get('doctor_id');

        if (!$date || !$doctorId) {
            return response()->json(['error' => 'Date and doctor ID required'], 400);
        }

        // Get doctor
        $doctor = Doctor::find($doctorId);

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Get available slots from doctor schedule (30 minutes per slot)
        $availableSlots = $doctor->getAvailableSlots($date, 30);

        if (empty($availableSlots)) {
            return response()->json([
                'slots' => [],
                'message' => 'Dokter tidak praktek di hari ini'
            ]);
        }

        // Get existing appointments for the doctor on that date
        $existingAppointments = Appointment::where('doctor_user_id', $doctorId)
            ->whereDate('scheduled_start_at', $date)
            ->whereNotIn('status', ['CANCELLED'])
            ->pluck('scheduled_start_at')
            ->map(function($datetime) {
                return Carbon::parse($datetime)->format('H:i');
            })
            ->toArray();

        // Map slots with availability
        $slots = [];
        foreach ($availableSlots as $slot) {
            $slots[] = [
                'time' => $slot['start'],
                'display' => $slot['start'] . ' - ' . $slot['end'],
                'available' => !in_array($slot['start'], $existingAppointments)
            ];
        }

        return response()->json(['slots' => $slots]);
    }
}
