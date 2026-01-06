<?php

namespace App\Http\Livewire\Admin\Doctor;

use App\Models\Doctor;
use App\Models\Speciality;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $specialityFilter = '';
    public $statusFilter = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->specialityFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($doctorId)
    {
        $doctor = Doctor::find($doctorId);
        
        if ($doctor) {
            if ($doctor->is_active) {
                $doctor->delete(); // Soft delete
            } else {
                $doctor->restore();
            }
            
            $status = $doctor->is_active ? 'diaktifkan' : 'dinonaktifkan';
            session()->flash('success', "Dokter berhasil {$status}!");
        }
    }

    public function deleteDoctor($doctorId)
    {
        $doctor = Doctor::withTrashed()->find($doctorId);
        
        if ($doctor) {
            // Check if doctor has appointments
            $appointmentCount = $doctor->appointments()->count();
            if ($appointmentCount > 0) {
                session()->flash('error', "Dokter tidak dapat dihapus karena sudah memiliki {$appointmentCount} appointment!");
                return;
            }
            
            // Permanently delete
            $doctor->user()->delete(); // This will cascade delete doctor
            session()->flash('success', 'Dokter berhasil dihapus permanen!');
        }
    }

    public function render()
    {
        $query = Doctor::with(['user', 'speciality'])->withTrashed();

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('display_name', 'like', '%' . $this->search . '%')
                  ->orWhere('registration_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filter by speciality
        if ($this->specialityFilter) {
            $query->where('speciality_id', $this->specialityFilter);
        }

        // Filter by status (active/inactive based on soft delete)
        if ($this->statusFilter !== '') {
            if ($this->statusFilter == '1') {
                $query->whereNull('deleted_at'); // Active
            } else {
                $query->whereNotNull('deleted_at'); // Inactive
            }
        }

        $doctors = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get specialities for filter
        $specialities = Speciality::where('is_active', true)
            ->orderBy('speciality_name')
            ->get();

        // Statistics
        $stats = [
            'total' => Doctor::withTrashed()->count(),
            'active' => Doctor::count(),
            'inactive' => Doctor::onlyTrashed()->count(),
        ];

        return view('livewire.admin.doctor.index', [
            'doctors' => $doctors,
            'specialities' => $specialities,
            'stats' => $stats,
        ]);
    }
}
