<?php

namespace App\Http\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentCalendar extends Component
{
    // View mode: 'day' or 'week'
    public $viewMode = 'day';
    
    // Current date being viewed
    public $currentDate;
    
    // Filter
    public $selectedDoctor = 'all';
    public $searchTerm = '';
    
    // Modal states
    public $showAppointmentModal = false;
    public $showDetailModal = false;
    
    // Selected appointment for detail
    public $selectedAppointment;
    
    // Statistics
    public $todayStats = [
        'total' => 0,
        'booked' => 0,
        'checked_in' => 0,
        'in_treatment' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    
    protected $listeners = ['refreshAppointments' => '$refresh'];
    
    public function mount()
    {
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }
    
    public function render()
    {
        // Get doctors for filter
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->get();

        // Get appointments for current date/week
        $appointments = $this->getAppointments();

        // Calculate statistics
        $this->calculateStats($appointments);

        // Get time slots (7 AM - 9 PM)
        $timeSlots = $this->generateTimeSlots();

        // Get week dates if in week view
        $weekDates = $this->viewMode === 'week' ? $this->getWeekDates() : [];

        return view('livewire.admin.appointments.appointment-calendar', [
            'doctors' => $doctors,
            'appointments' => $appointments,
            'timeSlots' => $timeSlots,
            'weekDates' => $weekDates,
        ]);
    }
    
    public function getAppointments()
    {
        $query = Appointment::with(['patient', 'doctor.user', 'service']);

        if ($this->viewMode === 'week') {
            // Get appointments for the whole week
            $startOfWeek = Carbon::parse($this->currentDate)->startOfWeek();
            $endOfWeek = Carbon::parse($this->currentDate)->endOfWeek();
            $query->whereBetween('scheduled_start_at', [$startOfWeek, $endOfWeek]);
        } else {
            // Get appointments for single day
            $query->whereDate('scheduled_start_at', $this->currentDate);
        }

        // Filter by doctor
        if ($this->selectedDoctor !== 'all') {
            $query->where('doctor_user_id', $this->selectedDoctor);
        }

        // Search by patient name
        if ($this->searchTerm) {
            $query->whereHas('patient', function($q) {
                $q->where('full_name', 'like', '%' . $this->searchTerm . '%');
            });
        }

        return $query->orderBy('scheduled_start_at')->get();
    }
    
    public function calculateStats($appointments)
    {
        $this->todayStats = [
            'total' => $appointments->count(),
            'booked' => $appointments->where('status', 'BOOKED')->count(),
            'checked_in' => $appointments->where('status', 'CHECKED_IN')->count(),
            'in_treatment' => $appointments->where('status', 'IN_TREATMENT')->count(),
            'completed' => $appointments->where('status', 'COMPLETED')->count(),
            'cancelled' => $appointments->where('status', 'CANCELLED')->count(),
        ];
    }
    
    public function generateTimeSlots()
    {
        $slots = [];
        $start = Carbon::createFromTime(7, 0);
        $end = Carbon::createFromTime(21, 0);
        
        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addHour();
        }
        
        return $slots;
    }
    
    public function previousDay()
    {
        if ($this->viewMode === 'week') {
            $this->currentDate = Carbon::parse($this->currentDate)
                ->subWeek()
                ->format('Y-m-d');
        } else {
            $this->currentDate = Carbon::parse($this->currentDate)
                ->subDay()
                ->format('Y-m-d');
        }
    }

    public function nextDay()
    {
        if ($this->viewMode === 'week') {
            $this->currentDate = Carbon::parse($this->currentDate)
                ->addWeek()
                ->format('Y-m-d');
        } else {
            $this->currentDate = Carbon::parse($this->currentDate)
                ->addDay()
                ->format('Y-m-d');
        }
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function getWeekDates()
    {
        $startOfWeek = Carbon::parse($this->currentDate)->startOfWeek();
        $dates = [];

        for ($i = 0; $i < 7; $i++) {
            $dates[] = $startOfWeek->copy()->addDays($i);
        }

        return $dates;
    }
    
    public function viewAppointmentDetail($appointmentId)
    {
        $this->selectedAppointment = Appointment::with([
            'patient', 
            'doctor.user', 
            'service'
        ])->find($appointmentId);
        
        $this->showDetailModal = true;
    }
    
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedAppointment = null;
    }
    
    public function updateStatus($appointmentId, $newStatus)
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment) {
            $appointment->update(['status' => $newStatus]);

            session()->flash('success', 'Status appointment berhasil diupdate');
            $this->dispatch('refreshAppointments');
        }
    }
    
    public function getStatusColor($status)
    {
        return match($status) {
            'BOOKED' => 'bg-blue-100 text-blue-800 border-blue-300',
            'CHECKED_IN' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'IN_TREATMENT' => 'bg-purple-100 text-purple-800 border-purple-300',
            'COMPLETED' => 'bg-green-100 text-green-800 border-green-300',
            'CANCELLED' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300'
        };
    }
}