<?php

namespace App\Http\Livewire\Doctor\Appointments;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Visit;
use App\Models\VisitDetail;
use App\Models\Service;
use App\Models\PatientMedia;
use App\Models\Queue;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AppointmentDetail extends Component
{
    public $appointmentId;

    // SOAP Fields (untuk visits table)
    public $subjective = '';      // S - Keluhan pasien
    public $objective = '';       // O - Hasil pemeriksaan
    public $assessment = '';      // A - Diagnosis
    public $plan = '';            // P - Rencana perawatan & resep
    public $notes = '';           // Catatan tambahan dokter
    public $follow_up_date = '';

    // Visit Detail Fields (untuk visit_details table)
    public $tooth_codes = '';
    public $service_id = '';
    public $diagnosis_note = '';
    public $treatment_note = '';
    public $detail_remarks = '';
    public $showDetailForm = false;

    public function mount($appointmentId)
    {
        $this->appointmentId = $appointmentId;
        $this->loadVisitData();
    }

    public function loadVisitData()
    {
        $appointment = Appointment::findOrFail($this->appointmentId);

        // Verify ownership
        $user = Auth::user();
        $doctor = Doctor::where('doctor_user_id', $user->id)->first();

        if ($appointment->doctor_user_id !== $doctor->doctor_user_id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        // Load visit data if exists
        $visit = Visit::where('appointment_id', $this->appointmentId)->first();

        if ($visit) {
            $this->subjective = $visit->subjective ?? '';
            $this->objective = $visit->objective ?? '';
            $this->assessment = $visit->assessment ?? '';
            $this->plan = $visit->plan ?? '';
            $this->notes = $visit->notes ?? '';
            $this->follow_up_date = $visit->follow_up_at ? $visit->follow_up_at->format('Y-m-d') : '';
        }
    }

    public function updateStatus($newStatus)
    {
        try {
            \DB::beginTransaction();

            $appointment = Appointment::with('queue')->findOrFail($this->appointmentId);

            // Create queue when checking in (BEFORE updating status)
            if ($newStatus === 'CHECKED_IN' && !$appointment->queue) {
                Queue::createFromAppointment($appointment);
                // Refresh appointment to load the newly created queue
                $appointment->load('queue');
            }

            $updateData = ['status' => $newStatus];

            // Update appointment
            $appointment->update($updateData);

            // Update queue status if exists
            if ($appointment->queue) {
                $queueStatus = match($newStatus) {
                    'CHECKED_IN' => 'WAITING',
                    'IN_TREATMENT' => 'IN_TREATMENT',
                    'COMPLETED' => 'DONE',
                    'CANCELLED' => 'CANCELLED',
                    default => 'WAITING'
                };

                $appointment->queue->update(['status' => $queueStatus]);
            }

            // PENTING: Auto-create Visit saat status jadi IN_TREATMENT
            if ($newStatus === 'IN_TREATMENT') {
                $user = Auth::user();
                $doctor = Doctor::where('doctor_user_id', $user->id)->first();

                // Cek apakah visit sudah ada
                $existingVisit = Visit::where('appointment_id', $appointment->appointment_id)->first();

                if (!$existingVisit) {
                    // Buat visit baru
                    Visit::create([
                        'appointment_id' => $appointment->appointment_id,
                        'queue_id' => $appointment->queue->queue_id ?? null,
                        'patient_id' => $appointment->patient_id,
                        'doctor_user_id' => $doctor->doctor_user_id,
                        'visit_at' => now(),
                        'status' => Visit::STATUS_IN_TREATMENT,
                        'subjective' => $appointment->complaint, // Keluhan dari appointment
                    ]);

                    \Log::info('Visit created automatically', ['appointment_id' => $appointment->appointment_id]);
                }
            }

            // Update Visit status ketika appointment selesai atau dibatalkan
            if ($newStatus === 'COMPLETED' || $newStatus === 'CANCELLED') {
                $visit = Visit::where('appointment_id', $appointment->appointment_id)->first();

                if ($visit) {
                    $visitStatus = match($newStatus) {
                        'COMPLETED' => Visit::STATUS_DONE,
                        'CANCELLED' => Visit::STATUS_DONE, // Tetap mark as DONE
                        default => $visit->status
                    };

                    $visit->update(['status' => $visitStatus]);

                    \Log::info('Visit status updated', [
                        'visit_id' => $visit->visit_id,
                        'new_status' => $visitStatus
                    ]);
                }
            }

            \DB::commit();

            // Dispatch event to update other components
            $this->dispatch('appointmentUpdated');

            session()->flash('success', 'Status berhasil diubah menjadi: ' . $newStatus);

            // Reload data
            $this->loadVisitData();

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Gagal mengubah status: ' . $e->getMessage());
            \Log::error('Error updating appointment status: ' . $e->getMessage());
        }
    }

    public function saveMedicalRecord()
    {
        \Log::info('saveMedicalRecord called - SOAP format');

        try {
            $this->validate([
                'subjective' => 'nullable|string',
                'objective' => 'nullable|string',
                'assessment' => 'nullable|string',
                'plan' => 'nullable|string',
                'notes' => 'nullable|string',
                'follow_up_date' => 'nullable|date',
            ]);

            $appointment = Appointment::findOrFail($this->appointmentId);

            // Find or create visit
            $visit = Visit::where('appointment_id', $this->appointmentId)->first();

            if (!$visit) {
                // Create visit jika belum ada (safety fallback)
                $user = Auth::user();
                $doctor = Doctor::where('doctor_user_id', $user->id)->first();

                $visit = Visit::create([
                    'appointment_id' => $appointment->appointment_id,
                    'queue_id' => $appointment->queue->queue_id ?? null,
                    'patient_id' => $appointment->patient_id,
                    'doctor_user_id' => $doctor->doctor_user_id,
                    'visit_at' => now(),
                    'status' => Visit::STATUS_IN_TREATMENT,
                ]);
            }

            // Update visit dengan SOAP data menggunakan method dari Model
            $visit->updateSOAP([
                'subjective' => $this->subjective,
                'objective' => $this->objective,
                'assessment' => $this->assessment,
                'plan' => $this->plan,
                'notes' => $this->notes,
                'follow_up_at' => $this->follow_up_date ?: null,
            ]);

            \Log::info('Visit updated successfully', ['visit_id' => $visit->visit_id]);

            // Reload data
            $this->loadVisitData();

        // Dispatch event
        $this->dispatch('appointmentUpdated');

        session()->flash('success', 'Data SOAP berhasil disimpan');

        // Dispatch browser event for notification
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Data SOAP berhasil disimpan!'
        ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation exception in saveMedicalRecord', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Exception in saveMedicalRecord: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Gagal menyimpan data SOAP: ' . $e->getMessage());
        }
    }

    public function toggleDetailForm()
    {
        $this->showDetailForm = !$this->showDetailForm;

        // Reset form jika ditutup
        if (!$this->showDetailForm) {
            $this->resetDetailForm();
        }
    }

    public function resetDetailForm()
    {
        $this->tooth_codes = '';
        $this->service_id = '';
        $this->diagnosis_note = '';
        $this->treatment_note = '';
        $this->detail_remarks = '';
    }

    public function saveVisitDetail()
    {
        \Log::info('saveVisitDetail called');

        try {
            $this->validate([
                'tooth_codes' => 'nullable|string|max:50',
                'service_id' => 'nullable|exists:services,service_id',
                'diagnosis_note' => 'nullable|string|max:200',
                'treatment_note' => 'nullable|string',
                'detail_remarks' => 'nullable|string',
            ]);

            // Get visit
            $visit = Visit::where('appointment_id', $this->appointmentId)->first();

            if (!$visit) {
                session()->flash('error', 'Visit tidak ditemukan. Pastikan sudah klik "Mulai Perawatan".');
                return;
            }

            // Add detail menggunakan method dari Model Visit
            $visit->addDetail([
                'tooth_codes' => $this->tooth_codes,
                'service_id' => $this->service_id,
                'diagnosis_note' => $this->diagnosis_note,
                'treatment_note' => $this->treatment_note,
                'remarks' => $this->detail_remarks,
                'entered_by' => Auth::id(),
            ]);

            \Log::info('Visit detail added successfully', ['visit_id' => $visit->visit_id]);

            // Reset form
            $this->resetDetailForm();
            $this->showDetailForm = false;

            // Dispatch event
            $this->dispatch('visitDetailAdded');

            session()->flash('success', 'Detail perawatan berhasil ditambahkan');

            // Dispatch browser event for notification
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Detail perawatan berhasil ditambahkan!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation exception in saveVisitDetail', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Exception in saveVisitDetail: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Gagal menyimpan detail perawatan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Fetch appointment fresh each time for the view
        $appointment = Appointment::with(['patient', 'service', 'doctor.user', 'doctor.speciality', 'queue'])
            ->findOrFail($this->appointmentId);

        // Load visit if exists
        $visit = Visit::where('appointment_id', $this->appointmentId)
            ->with('details.service')
            ->first();

        // Load active services for dropdown
        $services = Service::where('is_active', true)
            ->orderBy('service_name', 'asc')
            ->get();

        // Load patient media (foto gigi, x-ray) - untuk referensi dokter
        $patientMedia = PatientMedia::where('patient_id', $appointment->patient_id)
            ->where('is_active', true)
            ->whereIn('media_type', [
                'PHOTO_INTRAORAL',
                'PHOTO_EXTRAORAL',
                'PHOTO_BEFORE',
                'PHOTO_AFTER',
                'PHOTO_PROGRESS',
                'XRAY_PERIAPICAL',
                'XRAY_PANORAMIC',
                'XRAY_CEPHALOMETRIC',
                'XRAY_BITEWING',
            ])
            ->orderBy('taken_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.doctor.appointments.appointment-detail', [
            'appointment' => $appointment,
            'visit' => $visit,
            'services' => $services,
            'patientMedia' => $patientMedia,
        ]);
    }
}
