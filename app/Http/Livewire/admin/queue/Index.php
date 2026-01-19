<?php

namespace App\Http\Livewire\Admin\Queue;

use App\Models\Queue;
use App\Models\Doctor;
use App\Models\Visit;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

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
            ->with(['patient', 'doctor.user', 'appointment.service', 'visit'])
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

    /**
     * Panggil pasien - update queue status dan buat Visit record
     */
    public function callQueue($queueId)
    {
        $queue = Queue::findOrFail($queueId);

        DB::transaction(function () use ($queue) {
            // 1. Update queue status
            $queue->update([
                'status' => Queue::STATUS_IN_TREATMENT,
                'called_at' => now(),
                'started_at' => now(),
            ]);

            // 2. Buat Visit record jika belum ada
            if (!$queue->visit) {
                Visit::create([
                    'queue_id' => $queue->queue_id,
                    'appointment_id' => $queue->appointment_id,
                    'patient_id' => $queue->patient_id,
                    'doctor_user_id' => $queue->doctor_user_id,
                    'visit_at' => now(),
                    'status' => Visit::STATUS_IN_TREATMENT,
                    // SOAP fields akan diisi nanti oleh dokter/perawat
                    'subjective' => null,
                    'objective' => null,
                    'assessment' => null,
                    'plan' => null,
                ]);
            }

            // 3. Update appointment status jika ada
            if ($queue->appointment) {
                $queue->appointment->update(['status' => 'IN_TREATMENT']);
            }
        });

        session()->flash('message', 'Pasien nomor antrian ' . $queue->formatted_queue_number . ' berhasil dipanggil!');

        $this->dispatch('queueUpdated');
    }

    /**
     * Tandai selesai - update queue dan visit status
     */
    public function markAsDone($queueId)
    {
        $queue = Queue::with('visit')->findOrFail($queueId);

        DB::transaction(function () use ($queue) {
            // 1. Update queue status
            $queue->update([
                'status' => Queue::STATUS_DONE,
                'completed_at' => now(),
            ]);

            // 2. Update visit status ke READY_TO_BILL
            if ($queue->visit) {
                $queue->visit->update([
                    'status' => Visit::STATUS_READY_TO_BILL,
                ]);
            }

            // 3. Update appointment status jika ada
            if ($queue->appointment) {
                $queue->appointment->update(['status' => 'COMPLETED']);
            }
        });

        session()->flash('message', 'Antrian nomor ' . $queue->formatted_queue_number . ' selesai!');

        $this->dispatch('queueUpdated');
    }

    /**
     * Batalkan antrian
     */
    public function cancelQueue($queueId)
    {
        $queue = Queue::findOrFail($queueId);

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => Queue::STATUS_CANCELLED,
                'cancel_reason' => 'Dibatalkan oleh admin',
            ]);

            // Update appointment jika ada
            if ($queue->appointment) {
                $queue->appointment->update([
                    'status' => 'CANCELLED',
                    'cancel_reason' => 'Dibatalkan oleh admin',
                    'cancelled_at' => now(),
                ]);
            }
        });

        session()->flash('message', 'Antrian nomor ' . $queue->formatted_queue_number . ' dibatalkan!');

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
