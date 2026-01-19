<?php

namespace App\Http\Livewire\Nurse;

use App\Models\Queue;
use App\Models\Doctor;
use App\Models\Visit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueList extends Component
{
    use WithPagination;

    public $queueDate;
    public $filterDoctor = '';
    public $filterStatus = '';
    public $filterPriority = '';

    protected $queryString = [
        'filterDoctor' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterPriority' => ['except' => ''],
    ];

    public function mount()
    {
        $this->queueDate = today()->format('Y-m-d');
    }

    public function render()
    {
        $queues = Queue::query()
            ->with(['patient', 'doctor.user', 'doctor.speciality', 'appointment.service', 'visit'])
            ->whereDate('queue_date', $this->queueDate)
            ->when($this->filterDoctor, fn($q) => $q->where('doctor_user_id', $this->filterDoctor))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->orderByRaw("FIELD(status, 'IN_TREATMENT', 'WAITING', 'CALLED', 'DONE', 'CANCELLED', 'SKIPPED')")
            ->orderByRaw("FIELD(priority, 'URGENT', 'VIP', 'NORMAL')")
            ->orderBy('queue_number', 'asc')
            ->paginate(15);

        $doctors = Doctor::with('user')->where('is_active', true)->get();

        // Stats
        $stats = [
            'total' => Queue::whereDate('queue_date', $this->queueDate)->count(),
            'waiting' => Queue::whereDate('queue_date', $this->queueDate)->where('status', 'WAITING')->count(),
            'in_treatment' => Queue::whereDate('queue_date', $this->queueDate)->where('status', 'IN_TREATMENT')->count(),
            'done' => Queue::whereDate('queue_date', $this->queueDate)->where('status', 'DONE')->count(),
        ];

        return view('livewire.nurse.queue-list', [
            'queues' => $queues,
            'doctors' => $doctors,
            'stats' => $stats,
        ]);
    }

    /**
     * Panggil pasien
     */
    public function callPatient($queueId)
    {
        $queue = Queue::findOrFail($queueId);

        // Check if doctor already has patient in treatment
        $existingInTreatment = Queue::where('doctor_user_id', $queue->doctor_user_id)
            ->whereDate('queue_date', today())
            ->where('status', Queue::STATUS_IN_TREATMENT)
            ->exists();

        if ($existingInTreatment) {
            session()->flash('error', 'Dokter ini masih menangani pasien lain. Selesaikan dulu sebelum memanggil pasien baru.');
            return;
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => Queue::STATUS_IN_TREATMENT,
                'called_at' => now(),
                'called_by' => Auth::id(),
                'started_at' => now(),
            ]);

            // Create Visit
            Visit::create([
                'queue_id' => $queue->queue_id,
                'appointment_id' => $queue->appointment_id,
                'patient_id' => $queue->patient_id,
                'doctor_user_id' => $queue->doctor_user_id,
                'visit_at' => now(),
                'status' => Visit::STATUS_IN_TREATMENT,
            ]);
        });

        // Dispatch TTS event
        $this->dispatch('patientCalled', [
            'queue_number' => $queue->formatted_queue_number,
            'patient_name' => $queue->patient->name,
            'doctor_name' => $queue->doctor->display_name,
        ]);

        session()->flash('message', "Pasien {$queue->patient->name} berhasil dipanggil!");
    }

    /**
     * Skip pasien
     */
    public function skipPatient($queueId)
    {
        $queue = Queue::findOrFail($queueId);

        $queue->update([
            'status' => Queue::STATUS_SKIPPED,
        ]);

        session()->flash('message', "Pasien {$queue->patient->name} dilewati.");
    }

    /**
     * Restore skipped patient to waiting
     */
    public function restorePatient($queueId)
    {
        $queue = Queue::findOrFail($queueId);

        $queue->update([
            'status' => Queue::STATUS_WAITING,
        ]);

        session()->flash('message', "Pasien {$queue->patient->name} dikembalikan ke antrian.");
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->filterDoctor = '';
        $this->filterStatus = '';
        $this->filterPriority = '';
        $this->queueDate = today()->format('Y-m-d');
    }

    public function updatingFilterDoctor()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterPriority()
    {
        $this->resetPage();
    }
}