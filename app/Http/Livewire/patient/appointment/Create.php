<?php

namespace App\Http\Livewire\Patient\Appointment;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Create extends Component
{
    // ==========================================
    // STEP MANAGEMENT
    // ==========================================
    public $currentStep = 1;
    public $totalSteps = 6;
    
    // ==========================================
    // FORM DATA
    // ==========================================
    public $patientId;
    public $serviceId;
    public $doctorId; // nullable
    public $selectedDate;
    public $selectedTime;
    public $complaint = '';
    
    // ==========================================
    // DATA COLLECTIONS
    // ==========================================
    public $patients = [];
    public $servicesByCategory = [];
    public $categories = [];
    public $doctors = [];
    public $availableSlots = [];
    public $quickDates = [];
    
    // ==========================================
    // UI STATE
    // ==========================================
    public $selectedCategory = null;
    public $searchTerm = '';
    
    // ==========================================
    // VALIDATION RULES
    // ==========================================
    protected $rules = [
        'patientId' => 'required|exists:patients,patient_id',
        'serviceId' => 'required|exists:services,service_id',
        'doctorId' => 'nullable|exists:doctors,doctor_user_id',
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required',
        'complaint' => 'nullable|string|max:1000',
    ];
    
    protected $messages = [
        'patientId.required' => 'Silakan pilih pasien',
        'serviceId.required' => 'Silakan pilih layanan',
        'selectedDate.required' => 'Silakan pilih tanggal',
        'selectedTime.required' => 'Silakan pilih waktu',
        'selectedDate.after_or_equal' => 'Tanggal harus hari ini atau setelahnya',
    ];

    // ==========================================
    // LIFECYCLE HOOKS
    // ==========================================
    
    public function mount()
    {
        $this->loadPatients();
        $this->loadServices();
        $this->generateQuickDates();
    }

    // ==========================================
    // DATA LOADERS
    // ==========================================
    
    public function loadPatients()
    {
        $this->patients = Patient::where('owner_user_id', Auth::id())
            ->where('is_active', true)
            ->orderByRaw("CASE 
                WHEN relationship = 'SELF' THEN 1
                WHEN relationship = 'SPOUSE' THEN 2
                WHEN relationship = 'CHILD' THEN 3
                WHEN relationship = 'PARENT' THEN 4
                ELSE 5
            END")
            ->get();
    }
    
    public function loadServices()
    {
        $query = Service::forPatientBooking();
        
        // Apply category filter if selected
        if ($this->selectedCategory) {
            $query->byCategory($this->selectedCategory);
        }
        
        // Apply search if provided
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('service_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            });
        }
        
        $this->servicesByCategory = $query->get()->groupBy('category');
        $this->categories = Service::getCategories();
    }
    
    public function loadDoctors()
    {
        if (!$this->serviceId) {
            $this->doctors = [];
            return;
        }
        
        $service = Service::find($this->serviceId);
        
        if ($service && $service->speciality_id) {
            // Load doctors with same speciality
            $this->doctors = Doctor::with(['user', 'speciality'])
                ->where('is_active', true)
                ->where('speciality_id', $service->speciality_id)
                ->get();
        } else {
            // Load all active doctors
            $this->doctors = Doctor::with(['user', 'speciality'])
                ->where('is_active', true)
                ->get();
        }
    }
    
    public function loadAvailableSlots()
    {
        if (!$this->selectedDate) {
            $this->availableSlots = [];
            return;
        }
        
        if ($this->doctorId) {
            // Load doctor's schedule
            $this->loadDoctorSlots();
        } else {
            // Load clinic operational hours
            $this->loadClinicSlots();
        }
        
        // Mark booked slots as unavailable
        $this->markBookedSlots();
    }
    
    private function loadDoctorSlots()
    {
        $date = Carbon::parse($this->selectedDate);
        $dayOfWeek = $date->dayOfWeekIso; // 1=Monday, 7=Sunday
        
        // Get doctor's schedule for this day
        $schedule = DoctorSchedule::where('doctor_user_id', $this->doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();
        
        if (!$schedule) {
            $this->availableSlots = [];
            return;
        }
        
        // Generate slots from schedule
        $slots = [];
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);
        
        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes(30);
            
            if ($slotEnd->lte($end)) {
                $slots[] = [
                    'start' => $start->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'available' => true,
                    'label' => $start->format('H:i'),
                ];
            }
            
            $start->addMinutes(30);
        }
        
        $this->availableSlots = $slots;
    }
    
    private function loadClinicSlots()
    {
        $date = Carbon::parse($this->selectedDate);
        $dayOfWeek = $date->dayOfWeekIso;
        
        // Default clinic hours
        // Monday-Friday: 09:00-17:00
        // Saturday: 09:00-13:00
        // Sunday: Closed
        
        if ($dayOfWeek == 7) {
            $this->availableSlots = [];
            return;
        }
        
        $startTime = '09:00';
        $endTime = ($dayOfWeek == 6) ? '13:00' : '17:00';
        
        $slots = [];
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);
        
        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addMinutes(30);
            
            if ($slotEnd->lte($end)) {
                $slots[] = [
                    'start' => $start->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'available' => true,
                    'label' => $start->format('H:i'),
                ];
            }
            
            $start->addMinutes(30);
        }
        
        $this->availableSlots = $slots;
    }
    
    private function markBookedSlots()
    {
        if (empty($this->availableSlots)) {
            return;
        }
        
        // Get booked appointments for this date
        $query = Appointment::whereDate('scheduled_start_at', $this->selectedDate)
            ->whereNotIn('status', ['CANCELLED', 'NO_SHOW']);
        
        // If doctor selected, filter by doctor
        if ($this->doctorId) {
            $query->where('doctor_user_id', $this->doctorId);
        }
        
        $bookedTimes = $query->pluck('scheduled_start_at')
            ->map(fn($dt) => Carbon::parse($dt)->format('H:i'))
            ->toArray();
        
        // Mark slots as unavailable
        foreach ($this->availableSlots as &$slot) {
            if (in_array($slot['start'], $bookedTimes)) {
                $slot['available'] = false;
            }
        }
    }
    
    private function generateQuickDates()
    {
        $this->quickDates = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            
            $this->quickDates[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->translatedFormat('D'),
                'dayNum' => $date->format('d'),
                'month' => $date->translatedFormat('M'),
                'isToday' => $i === 0,
                'isTomorrow' => $i === 1,
            ];
        }
    }

    // ==========================================
    // STEP ACTIONS
    // ==========================================
    
    public function selectPatient($patientId)
    {
        $this->patientId = $patientId;
        $this->nextStep();
    }
    
    public function selectService($serviceId)
    {
        $this->serviceId = $serviceId;
        $this->loadDoctors();
        $this->nextStep();
    }
    
    public function selectDoctor($doctorId)
    {
        $this->doctorId = $doctorId;
        $this->nextStep();
    }
    
    public function skipDoctorSelection()
    {
        $this->doctorId = null;
        $this->nextStep();
    }
    
    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableSlots();
        $this->nextStep();
    }
    
    public function selectTime($time)
    {
        $this->selectedTime = $time;
        $this->nextStep();
    }
    
    // ==========================================
    // NAVIGATION
    // ==========================================
    
    public function nextStep()
    {
        // Skip doctor step if already at step 3 and going next
        if ($this->currentStep == 3 && empty($this->doctors)) {
            $this->currentStep = 4;
        } else {
            $this->currentStep++;
        }
    }
    
    public function previousStep()
    {
        $this->currentStep--;
    }
    
    public function goToStep($step)
    {
        if ($step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }
    
    // ==========================================
    // FILTERS
    // ==========================================
    
    public function filterByCategory($category)
    {
        $this->selectedCategory = $category;
        $this->loadServices();
    }
    
    public function clearCategoryFilter()
    {
        $this->selectedCategory = null;
        $this->loadServices();
    }
    
    public function updatedSearchTerm()
    {
        $this->loadServices();
    }

    // ==========================================
    // FORM SUBMISSION
    // ==========================================
    
    public function submit()
    {
        $this->validate();
        
        try {
            $service = Service::find($this->serviceId);
            $duration = $service->duration_minutes ?? 30;
            
            $scheduledStart = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
            $scheduledEnd = $scheduledStart->copy()->addMinutes($duration);
            
            // Check if slot is still available
            $conflictExists = Appointment::whereDate('scheduled_start_at', $this->selectedDate)
                ->where('scheduled_start_at', $scheduledStart)
                ->whereNotIn('status', ['CANCELLED', 'NO_SHOW'])
                ->when($this->doctorId, function($q) {
                    $q->where('doctor_user_id', $this->doctorId);
                })
                ->exists();
            
            if ($conflictExists) {
                session()->flash('error', 'Maaf, slot waktu ini sudah dibooking. Silakan pilih waktu lain.');
                $this->currentStep = 5;
                $this->loadAvailableSlots();
                return;
            }
            
            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $this->patientId,
                'service_id' => $this->serviceId,
                'doctor_user_id' => $this->doctorId,
                'booking_source' => 'ONLINE',
                'scheduled_start_at' => $scheduledStart,
                'scheduled_end_at' => $scheduledEnd,
                'complaint' => $this->complaint,
                'status' => 'BOOKED',
            ]);
            
            session()->flash('success', 'Appointment berhasil dibuat! Nomor booking: ' . $appointment->id);
            
            return redirect()->route('patient.appointments.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // ==========================================
    // COMPUTED PROPERTIES
    // ==========================================
    
    public function getSelectedPatientProperty()
    {
        if (!$this->patientId) {
            return null;
        }
        
        return Patient::find($this->patientId);
    }
    
    public function getSelectedServiceProperty()
    {
        if (!$this->serviceId) {
            return null;
        }
        
        return Service::with('speciality')->find($this->serviceId);
    }
    
    public function getSelectedDoctorProperty()
    {
        if (!$this->doctorId) {
            return null;
        }
        
        return Doctor::with(['user', 'speciality'])->find($this->doctorId);
    }
    
    public function getProgressPercentageProperty()
    {
        return ($this->currentStep / $this->totalSteps) * 100;
    }

    // ==========================================
    // RENDER
    // ==========================================
    
    public function render()
    {
        return view('livewire.pasien.appointments.create')
            ->layout('layouts.patient');
    }
}