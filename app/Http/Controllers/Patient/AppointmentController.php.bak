<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\ClinicTimeSlot;
use App\Models\Service;
use App\Models\Doctor;
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
            return redirect()->route('pasien.dashboard');
        }
        
        $patient = Patient::where('id', $selectedPatientId)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();
        
        // Get filter
        $filter = $request->get('status', 'all');
        
        // Query appointments
        $query = Appointment::with([
                'preferredDoctor.user',
                'assignedDoctor.user',
                'service',
                'slot',
                'queue'
            ])
            ->where('patient_id', $patient->id);
        
        // Apply filters
        if ($filter == 'upcoming') {
            $query->whereIn('status', ['BOOKED', 'APPROVED', 'CHECKED_IN'])
                ->whereHas('slot', function($q) {
                    $q->where('slot_date', '>=', now()->toDateString());
                });
        } elseif ($filter == 'completed') {
            $query->where('status', 'COMPLETED');
        } elseif ($filter == 'cancelled') {
            $query->where('status', 'CANCELLED');
        }
        
        $appointments = $query->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get counts for tabs
        $counts = [
            'all' => Appointment::where('patient_id', $patient->id)->count(),
            'upcoming' => Appointment::where('patient_id', $patient->id)
                ->whereIn('status', ['BOOKED', 'APPROVED', 'CHECKED_IN'])
                ->whereHas('slot', function($q) {
                    $q->where('slot_date', '>=', now()->toDateString());
                })
                ->count(),
            'completed' => Appointment::where('patient_id', $patient->id)
                ->where('status', 'COMPLETED')
                ->count(),
            'cancelled' => Appointment::where('patient_id', $patient->id)
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
            return redirect()->route('pasien.dashboard');
        }
        
        $patient = Patient::where('id', $selectedPatientId)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();
        
        // Get active services
        $services = Service::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // Get active doctors
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->get();
        
        return view('pasien.appointments.create', compact(
            'services',
            'doctors',
            'patient'
        ));
    }
    
    /**
     * Get available time slots (AJAX)
     */
    public function getSlots(Request $request)
    {
        $date = $request->get('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }
        
        // Get slots for the date with remaining capacity
        $slots = ClinicTimeSlot::where('slot_date', $date)
            ->where('is_available', true)
            ->withCount('appointments')
            ->orderBy('start_time')
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => Carbon::parse($slot->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($slot->end_time)->format('H:i'),
                    'capacity' => $slot->capacity,
                    'booked' => $slot->appointments_count,
                    'remaining_capacity' => $slot->capacity - $slot->appointments_count,
                ];
            });
        
        return response()->json(['slots' => $slots]);
    }
    
    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $selectedPatientId = $request->session()->get('selected_patient_id');
        
        if (!$selectedPatientId) {
            return redirect()->route('pasien.dashboard')
                ->with('error', 'Silakan pilih pasien terlebih dahulu');
        }
        
        $patient = Patient::where('id', $selectedPatientId)
            ->where('owner_user_id', $user->id)
            ->firstOrFail();
        
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'slot_id' => 'required|exists:clinic_time_slots,id',
            'preferred_doctor_user_id' => 'nullable|exists:users,id',
            'complaint' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Check slot availability
            $slot = ClinicTimeSlot::withCount('appointments')
                ->findOrFail($request->slot_id);
            
            if ($slot->appointments_count >= $slot->capacity) {
                return back()->with('error', 'Slot sudah penuh. Silakan pilih waktu lain.');
            }
            
            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'service_id' => $request->service_id,
                'slot_id' => $request->slot_id,
                'preferred_doctor_user_id' => $request->preferred_doctor_user_id,
                'status' => 'BOOKED',
                'booking_source' => 'WEB',
                'complaint' => $request->complaint,
            ]);
            
            DB::commit();
            
            return redirect()->route('pasien.appointments.show', $appointment->id)
                ->with('success', 'Janji temu berhasil dibuat! Menunggu konfirmasi dari klinik.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Gagal membuat janji temu: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Check if appointment belongs to user's patient
        if ($appointment->patient->owner_user_id != $user->id) {
            abort(403, 'Unauthorized');
        }
        
        $appointment->load([
            'patient',
            'service',
            'slot',
            'preferredDoctor.user',
            'assignedDoctor.user',
            'queue'
        ]);
        
        return view('pasien.appointments.show', compact('appointment'));
    }
    
    /**
     * Cancel appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        
        // Check if appointment belongs to user's patient
        if ($appointment->patient->owner_user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if appointment can be cancelled
        if (!in_array($appointment->status, ['BOOKED', 'APPROVED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment cannot be cancelled'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            $appointment->update([
                'status' => 'CANCELLED',
                'cancel_reason' => $request->input('reason', 'Cancelled by patient'),
                'cancelled_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel appointment: ' . $e->getMessage()
            ], 500);
        }
    }
}