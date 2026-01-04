<?php

namespace App\Http\Livewire\Admin\CheckIn;

use App\Models\Appointment;
use App\Models\Queue;
use App\Models\Doctor;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $doctorFilter = '';

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $query = Appointment::with(['patient', 'doctor.user', 'service'])
            ->where('status', 'BOOKED')
            ->whereDate('scheduled_start_at', today())
            ->orderBy('scheduled_start_at', 'asc');

        // Search
        if ($this->search) {
            $query->whereHas('patient', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('medical_record_number', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by doctor
        if ($this->doctorFilter) {
            $query->where('doctor_user_id', $this->doctorFilter);
        }

        $appointments = $query->paginate(15);

        // Get doctors for filter
        $doctors = Doctor::where('is_active', true)->get();

        // Statistics
        $stats = [
            'waiting_checkin' => Appointment::where('status', 'BOOKED')
                ->whereDate('scheduled_start_at', today())
                ->count(),
            'checked_in' => Appointment::where('status', 'CHECKED_IN')
                ->whereDate('scheduled_start_at', today())
                ->count(),
            'in_queue' => Queue::whereDate('queue_date', today())
                ->where('status', 'WAITING')
                ->count(),
        ];

        return view('livewire.admin.check-in.index', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'stats' => $stats,
        ]);
    }

    public function checkIn($appointmentId)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($appointmentId);

        // Validasi: Hanya BOOKED yang bisa check-in
        if ($appointment->status !== 'BOOKED') {
            session()->flash('error', 'Appointment tidak bisa di-check-in! Status: ' . $appointment->status);
            return;
        }

        try {
            \DB::beginTransaction();

            // Update appointment status
            $appointment->update([
                'status' => 'CHECKED_IN',
            ]);

            // Cek apakah sudah ada queue (anti double submit)
            $existingQueue = Queue::where('appointment_id', $appointment->appointment_id)->first();
            if ($existingQueue) {
                \DB::commit();
                session()->flash('success', 'Pasien sudah di-check-in. Nomor antrian: ' . $existingQueue->queue_number);
                return;
            }

            // Pastikan queue_date hanya date (tanpa jam)
            $queueDate = today()->toDateString();

            // Get next queue number for today and this doctor
            $lastQueue = Queue::whereDate('queue_date', $queueDate)
                ->where('doctor_user_id', $appointment->doctor_user_id)
                ->orderBy('queue_number', 'desc')
                ->first();

            $nextQueueNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

            // Create queue
            Queue::create([
                'appointment_id' => $appointment->appointment_id,
                'patient_id' => $appointment->patient_id,
                'doctor_user_id' => $appointment->doctor_user_id,
                'queue_number' => $nextQueueNumber,
                'queue_date' => $queueDate,
                'estimated_time' => $appointment->scheduled_start_at->format('H:i:s'),
                'complaint' => $appointment->complaint,
                'status' => 'WAITING',
            ]);

            \DB::commit();

            session()->flash('success', 'Pasien ' . $appointment->patient->name . ' berhasil di-check-in! Nomor antrian: ' . $nextQueueNumber);
            
            $this->dispatch('patientCheckedIn');

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'doctorFilter']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDoctorFilter()
    {
        $this->resetPage();
    }
}