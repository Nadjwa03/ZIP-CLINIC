<?php

namespace App\Http\Livewire\Nurse;

use App\Models\Doctor;
use App\Models\Queue;
use App\Models\Visit;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TreatmentRoom extends Component
{
    public $selectedDoctorId = null;
    public $showQueueModal = false;
    public $showSoapPanel = false;
    public $currentVisitId = null;

    protected $listeners = [
        'refreshTreatmentRoom' => '$refresh',
        'closeSoapPanel' => 'closeSoapPanel',
    ];

    public function render()
    {
        // Get active doctors for today (yang punya jadwal atau sudah ada antrian)
        $doctors = Doctor::with(['user', 'speciality'])
            ->where('is_active', true)
            ->get()
            ->map(function ($doctor) {
                // Get current patient (IN_TREATMENT)
                $currentQueue = Queue::with(['patient', 'visit'])
                    ->where('doctor_user_id', $doctor->doctor_user_id)
                    ->whereDate('queue_date', today())
                    ->where('status', Queue::STATUS_IN_TREATMENT)
                    ->first();

                // Get waiting count
                $waitingCount = Queue::where('doctor_user_id', $doctor->doctor_user_id)
                    ->whereDate('queue_date', today())
                    ->where('status', Queue::STATUS_WAITING)
                    ->count();

                // Get done count
                $doneCount = Queue::where('doctor_user_id', $doctor->doctor_user_id)
                    ->whereDate('queue_date', today())
                    ->where('status', Queue::STATUS_DONE)
                    ->count();

                return [
                    'doctor' => $doctor,
                    'current_queue' => $currentQueue,
                    'waiting_count' => $waitingCount,
                    'done_count' => $doneCount,
                    'has_patient' => $currentQueue !== null,
                ];
            });

        return view('livewire.nurse.treatment-room', [
            'doctors' => $doctors,
        ]);
    }

    /**
     * Panggil pasien berikutnya
     */
    public function callNextPatient($doctorUserId)
    {
        // Get next waiting queue (by priority first, then queue_number)
        $nextQueue = Queue::where('doctor_user_id', $doctorUserId)
            ->whereDate('queue_date', today())
            ->where('status', Queue::STATUS_WAITING)
            ->orderByRaw("FIELD(priority, 'URGENT', 'VIP', 'NORMAL')")
            ->orderBy('queue_number', 'asc')
            ->first();

        if (!$nextQueue) {
            session()->flash('error', 'Tidak ada pasien yang menunggu.');
            return;
        }

        DB::transaction(function () use ($nextQueue) {
            // Update queue status
            $nextQueue->update([
                'status' => Queue::STATUS_IN_TREATMENT,
                'called_at' => now(),
                'called_by' => Auth::id(),
                'started_at' => now(),
            ]);

            // Create Visit record
            Visit::create([
                'queue_id' => $nextQueue->queue_id,
                'appointment_id' => $nextQueue->appointment_id,
                'patient_id' => $nextQueue->patient_id,
                'doctor_user_id' => $nextQueue->doctor_user_id,
                'visit_at' => now(),
                'status' => Visit::STATUS_IN_TREATMENT,
            ]);
        });

        // Dispatch event untuk TTS (Text-to-Speech)
        $this->dispatch('patientCalled', [
            'queue_number' => $nextQueue->formatted_queue_number,
            'patient_name' => $nextQueue->patient->name,
            'doctor_name' => $nextQueue->doctor->display_name,
        ]);

        session()->flash('message', "Pasien {$nextQueue->patient->name} (Q-{$nextQueue->formatted_queue_number}) dipanggil!");
    }

    /**
     * Buka modal daftar antrian untuk dokter tertentu
     */
    public function openQueueModal($doctorUserId)
    {
        $this->selectedDoctorId = $doctorUserId;
        $this->showQueueModal = true;
    }

    /**
     * Tutup modal antrian
     */
    public function closeQueueModal()
    {
        $this->showQueueModal = false;
        $this->selectedDoctorId = null;
    }

    /**
     * Buka panel SOAP untuk visit tertentu
     */
    public function openSoapPanel($visitId)
    {
        $this->currentVisitId = $visitId;
        $this->showSoapPanel = true;
    }

    /**
     * Tutup panel SOAP
     */
    public function closeSoapPanel()
    {
        $this->showSoapPanel = false;
        $this->currentVisitId = null;
    }

    /**
     * Selesaikan treatment
     */
    public function completeVisit($queueId)
    {
        $queue = Queue::with('visit')->findOrFail($queueId);

        DB::transaction(function () use ($queue) {
            // Update queue
            $queue->update([
                'status' => Queue::STATUS_DONE,
                'completed_at' => now(),
            ]);

            // Update visit
            if ($queue->visit) {
                $queue->visit->update([
                    'status' => Visit::STATUS_READY_TO_BILL,
                ]);
            }

            // Update appointment if exists
            if ($queue->appointment) {
                $queue->appointment->update(['status' => 'COMPLETED']);
            }
        });

        session()->flash('message', "Treatment untuk {$queue->patient->name} selesai!");
        $this->closeSoapPanel();
    }

    /**
     * Get waiting queues for modal
     */
    public function getWaitingQueuesProperty()
    {
        if (!$this->selectedDoctorId) {
            return collect();
        }

        return Queue::with(['patient', 'appointment.service'])
            ->where('doctor_user_id', $this->selectedDoctorId)
            ->whereDate('queue_date', today())
            ->where('status', Queue::STATUS_WAITING)
            ->orderByRaw("FIELD(priority, 'URGENT', 'VIP', 'NORMAL')")
            ->orderBy('queue_number', 'asc')
            ->get();
    }

    /**
     * Get selected doctor
     */
    public function getSelectedDoctorProperty()
    {
        if (!$this->selectedDoctorId) {
            return null;
        }

        return Doctor::with(['user', 'speciality'])->find($this->selectedDoctorId);
    }
}