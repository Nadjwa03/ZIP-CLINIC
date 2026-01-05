<?php

namespace App\Http\Livewire\Admin\Speciality;

use App\Models\Speciality;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function toggleStatus($specialityId)
    {
        $speciality = Speciality::find($specialityId);
        
        if ($speciality) {
            $speciality->update([
                'is_active' => !$speciality->is_active
            ]);
            
            $status = $speciality->is_active ? 'diaktifkan' : 'dinonaktifkan';
            session()->flash('success', "Spesialisasi berhasil {$status}!");
        }
    }

    public function deleteSpeciality($specialityId)
    {
        $speciality = Speciality::find($specialityId);
        
        if ($speciality) {
            // Check if any doctors use this speciality
            if ($speciality->doctors()->count() > 0) {
                session()->flash('error', 'Spesialisasi tidak dapat dihapus karena masih digunakan oleh ' . $speciality->doctors()->count() . ' dokter!');
                return;
            }
            
            $speciality->delete();
            session()->flash('success', 'Spesialisasi berhasil dihapus!');
        }
    }

    public function render()
    {
        $query = Speciality::query();

        // Search
        if ($this->search) {
            $query->search($this->search);
        }

        // Filter status
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter);
        }

        $specialities = $query->latest()->paginate(10);

        // Stats
        $stats = [
            'total' => Speciality::count(),
            'active' => Speciality::where('is_active', true)->count(),
            'inactive' => Speciality::where('is_active', false)->count(),
        ];

        return view('livewire.admin.speciality.index', [
            'specialities' => $specialities,
            'stats' => $stats,
        ]);
    }
}
