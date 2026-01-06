<?php

namespace App\Http\Livewire\Admin\Doctor;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Support\Facades\DB;

class Schedule extends Component
{
    public $doctorId;
    public $doctor;

    // Schedules grouped by day
    public $schedules = [];

    // Days of week
    public $daysOfWeek = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    // For adding new shift
    public $newShift = [
        'day' => null,
        'start_time' => '09:00',
        'end_time' => '17:00',
    ];

    public function mount($doctorId)
    {
        $this->doctorId = $doctorId;
        $this->doctor = Doctor::with('user')->findOrFail($doctorId);

        $this->loadSchedules();
    }

    public function loadSchedules()
    {
        // Get all schedules grouped by day
        $existingSchedules = DoctorSchedule::where('doctor_user_id', $this->doctorId)
            ->whereNull('effective_from')
            ->whereNull('effective_to')
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        // Initialize schedules array
        $this->schedules = [];
        foreach ($this->daysOfWeek as $day => $dayName) {
            $this->schedules[$day] = [];

            if (isset($existingSchedules[$day])) {
                foreach ($existingSchedules[$day] as $schedule) {
                    $this->schedules[$day][] = [
                        'id' => $schedule->id,
                        'start_time' => date('H:i', strtotime($schedule->start_time)),
                        'end_time' => date('H:i', strtotime($schedule->end_time)),
                    ];
                }
            }
        }
    }

    public function addShift($day)
    {
        $this->schedules[$day][] = [
            'id' => null,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ];
    }

    public function removeShift($day, $index)
    {
        $shift = $this->schedules[$day][$index];

        // If has ID, delete from database
        if ($shift['id']) {
            try {
                DoctorSchedule::findOrFail($shift['id'])->delete();
                session()->flash('success', 'Shift berhasil dihapus!');
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal menghapus shift: ' . $e->getMessage());
                return;
            }
        }

        // Remove from array
        unset($this->schedules[$day][$index]);
        $this->schedules[$day] = array_values($this->schedules[$day]);
    }

    public function save()
    {
        // Validate all shifts
        foreach ($this->schedules as $day => $shifts) {
            foreach ($shifts as $index => $shift) {
                if (empty($shift['start_time']) || empty($shift['end_time'])) {
                    session()->flash('error', "Jam praktek untuk {$this->daysOfWeek[$day]} shift " . ($index + 1) . " harus diisi!");
                    return;
                }

                if ($shift['start_time'] >= $shift['end_time']) {
                    session()->flash('error', "Jam selesai harus lebih besar dari jam mulai untuk {$this->daysOfWeek[$day]} shift " . ($index + 1) . "!");
                    return;
                }

                // Check overlap with other shifts on same day
                foreach ($shifts as $otherIndex => $otherShift) {
                    if ($index !== $otherIndex) {
                        // Check if time ranges overlap
                        if (
                            ($shift['start_time'] >= $otherShift['start_time'] && $shift['start_time'] < $otherShift['end_time']) ||
                            ($shift['end_time'] > $otherShift['start_time'] && $shift['end_time'] <= $otherShift['end_time']) ||
                            ($shift['start_time'] <= $otherShift['start_time'] && $shift['end_time'] >= $otherShift['end_time'])
                        ) {
                            session()->flash('error', "Jadwal shift pada {$this->daysOfWeek[$day]} saling bertumpukan!");
                            return;
                        }
                    }
                }
            }
        }

        try {
            DB::beginTransaction();

            foreach ($this->schedules as $day => $shifts) {
                foreach ($shifts as $shift) {
                    if ($shift['id']) {
                        // Update existing schedule
                        DoctorSchedule::where('id', $shift['id'])->update([
                            'start_time' => $shift['start_time'],
                            'end_time' => $shift['end_time'],
                        ]);
                    } else {
                        // Create new schedule
                        DoctorSchedule::create([
                            'doctor_user_id' => $this->doctorId,
                            'day_of_week' => $day,
                            'start_time' => $shift['start_time'],
                            'end_time' => $shift['end_time'],
                            'is_active' => true,
                        ]);
                    }
                }
            }

            DB::commit();

            session()->flash('success', 'Jadwal dokter berhasil disimpan!');
            $this->loadSchedules();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.doctor.schedule');
    }
}
