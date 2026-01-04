<?php

namespace App\Http\Livewire\Admin\Queue;

use App\Models\Queue;
use App\Models\Doctor;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
    public $selectedDoctor = '';
    public $selectedStatus = '';
    public $queueDate;

    protected $listeners = ['refreshQueue'];

    public function mount()
    {
        $this->queueDate = today()->format('Y-m-d');
    }

    public function render()
    {
        $queues = Queue::query()
            ->with(['patient', 'doctor.user', 'appointment.service'])
            ->whereDate('queue_date', $this->queueDate)
            ->when($this->selectedDoctor, function($query) {
                $query->where('doctor_user_id', $this->selectedDoctor);
            })
            ->when($this->selectedStatus, function($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->orderBy('queue_number', 'asc')
            ->get();

        $doctors = Doctor::where('is_active', true)->get();

        // Statistics
        $stats = [
            'total' => Queue::whereDate('queue_date', $this->queueDate)->count(),
            'waiting' => Queue::whereDate('queue_date', $this->queueDate)->where('status', 'WAITING')->count(),
            'in_treatment' => Queue::whereDate('queue_date', $this->queueDate)->where('status', 'IN_TREATMENT')->count(),
            'done' => Queue::whereDate('queue_date', $this->queueDate)->where('status', 'DONE')->count(),
        ];

        return view('livewire.admin.queue.index', [
            'queues' => $queues,
            'doctors' => $doctors,
            'stats' => $stats,
        ]);
    }

    public function callQueue($queueId)
    {
        $queue = Queue::findOrFail($queueId);
        
        // Update status ke IN_TREATMENT
        $queue->update([
            'status' => 'IN_TREATMENT',
            'called_at' => now(),
        ]);

        session()->flash('message', 'Pasien nomor antrian ' . $queue->queue_number . ' berhasil dipanggil!');
        
        $this->dispatch('queueUpdated');
    }

    public function markAsDone($queueId)
    {
        $queue = Queue::findOrFail($queueId);
        
        $queue->update([
            'status' => 'DONE',
        ]);

        session()->flash('message', 'Antrian nomor ' . $queue->queue_number . ' selesai!');
        
        $this->dispatch('queueUpdated');
    }

    public function cancelQueue($queueId)
    {
        $queue = Queue::findOrFail($queueId);
        
        $queue->update([
            'status' => 'CANCELLED',
            'cancel_reason' => 'Dibatalkan oleh admin',
        ]);

        session()->flash('message', 'Antrian nomor ' . $queue->queue_number . ' dibatalkan!');
        
        $this->dispatch('queueUpdated');
    }

    public function refreshQueue()
    {
        // Method untuk refresh otomatis (bisa dipanggil via polling)
        $this->dispatch('queueRefreshed');
    }

    public function resetFilters()
    {
        $this->selectedDoctor = '';
        $this->selectedStatus = '';
        $this->queueDate = today()->format('Y-m-d');
    }
}