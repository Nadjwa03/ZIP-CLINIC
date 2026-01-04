<?php

namespace App\Http\Livewire\Admin\Queue;

use Livewire\Component;
use App\Models\Queue;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;

class QueueManagement extends Component
{
    public $currentDate;
    public $selectedDoctor = 'all';
    public $searchTerm = '';
    
    // Modal states
    public $showAddQueueModal = false;
    public $showDetailModal = false;
    public $selectedQueue;
    
    // Form untuk add walk-in queue
    public $patientId;
    public $doctorUserId;
    public $complaint;
    
    // Statistics
    public $queueStats = [
        'total' => 0,
        'waiting' => 0,
        'in_treatment' => 0,
        'done' => 0,
        'cancelled' => 0
    ];
    
    protected $listeners = ['refreshQueue' => '$refresh'];
    
    public function mount()
    {
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }
    
    public function render()
    {
        // Get doctors
        $doctors = Doctor::with('user')->where('is_active', true)->get();
        
        // Get patients for dropdown
        $patients = Patient::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get queues for today
        $queues = $this->getQueues();
        
        // Calculate statistics
        $this->calculateStats($queues);
        
        return view('livewire.admin.queue.queue-management', [
            'doctors' => $doctors,
            'patients' => $patients,
            'queues' => $queues,
        ]);
    }
    
    public function getQueues()
    {
        $query = Queue::with(['patient', 'doctor.user', 'appointment'])
            ->where('queue_date', $this->currentDate);
        
        // Filter by doctor
        if ($this->selectedDoctor !== 'all') {
            $query->where('doctor_user_id', $this->selectedDoctor);
        }
        
        // Search by patient name or queue number
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('queue_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('patient', function($pq) {
                      $pq->where('name', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }
        
        return $query->orderBy('queue_number')->get();
    }
    
    public function calculateStats($queues)
    {
        $this->queueStats = [
            'total' => $queues->count(),
            'waiting' => $queues->where('status', 'WAITING')->count(),
            'in_treatment' => $queues->where('status', 'IN_TREATMENT')->count(),
            'done' => $queues->where('status', 'DONE')->count(),
            'cancelled' => $queues->where('status', 'CANCELLED')->count(),
        ];
    }
    
    public function previousDay()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->subDay()->format('Y-m-d');
    }
    
    public function nextDay()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->addDay()->format('Y-m-d');
    }
    
    public function goToToday()
    {
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }
    
    public function openAddQueueModal()
    {
        $this->reset(['patientId', 'doctorUserId', 'complaint']);
        $this->showAddQueueModal = true;
    }
    
    public function addWalkInQueue()
    {
        $this->validate([
            'patientId' => 'required|exists:patient,patient_id',
            'doctorUserId' => 'required|exists:doctors,doctor_user_id',
            'complaint' => 'nullable|string',
        ]);
        
        // Generate queue number
        $lastQueue = Queue::where('queue_date', $this->currentDate)->max('queue_number');
        $queueNumber = ($lastQueue ?? 0) + 1;
        
        // Create queue
        Queue::create([
            'patient_id' => $this->patientId,
            'doctor_user_id' => $this->doctorUserId,
            'queue_number' => $queueNumber,
            'queue_date' => $this->currentDate,
            'complaint' => $this->complaint,
            'status' => 'WAITING',
        ]);
        
        session()->flash('success', 'Pasien walk-in berhasil ditambahkan ke antrian');
        $this->showAddQueueModal = false;
        $this->reset(['patientId', 'doctorUserId', 'complaint']);
    }
    
    public function callQueue($queueId)
    {
        $queue = Queue::find($queueId);
        if ($queue && $queue->status === 'WAITING') {
            $queue->update([
                'called_at' => now(),
            ]);
            
            session()->flash('success', 'Pasien ' . $queue->patient->name . ' telah dipanggil');
            $this->emit('refreshQueue');
        }
    }
    
    public function startTreatment($queueId)
    {
        $queue = Queue::find($queueId);
        if ($queue && $queue->status === 'WAITING') {
            $queue->update(['status' => 'IN_TREATMENT']);
            
            // Update appointment status if exists
            if ($queue->appointment_id) {
                $queue->appointment->update(['status' => 'IN_TREATMENT']);
            }
            
            session()->flash('success', 'Treatment dimulai untuk ' . $queue->patient->name);
            $this->emit('refreshQueue');
        }
    }
    
    public function viewQueueDetail($queueId)
    {
        $this->selectedQueue = Queue::with([
            'patient', 
            'doctor.user', 
            'appointment.service'
        ])->find($queueId);
        
        $this->showDetailModal = true;
    }
    
    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedQueue = null;
    }
    
    public function getStatusColor($status)
    {
        return match($status) {
            'WAITING' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'IN_TREATMENT' => 'bg-purple-100 text-purple-800 border-purple-300',
            'DONE' => 'bg-green-100 text-green-800 border-green-300',
            'CANCELLED' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300'
        };
    }
}