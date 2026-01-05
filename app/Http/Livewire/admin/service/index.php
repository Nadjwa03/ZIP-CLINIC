<?php

namespace App\Http\Livewire\Admin\Service;

use App\Models\Service;
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

    public function toggleStatus($serviceId)
    {
        $service = Service::find($serviceId);
        
        if ($service) {
            $service->update([
                'is_active' => !$service->is_active
            ]);
            
            $status = $service->is_active ? 'diaktifkan' : 'dinonaktifkan';
            session()->flash('success', "Layanan berhasil {$status}!");
        }
    }

    public function deleteService($serviceId)
    {
        $service = Service::find($serviceId);
        
        if ($service) {
            // Check if service is used in appointments
            $appointmentCount = $service->appointments()->count();
            if ($appointmentCount > 0) {
                session()->flash('error', "Layanan tidak dapat dihapus karena sudah digunakan di {$appointmentCount} appointment!");
                return;
            }
            
            // Check if service is used in invoices
            $invoiceCount = $service->invoiceItems()->count();
            if ($invoiceCount > 0) {
                session()->flash('error', "Layanan tidak dapat dihapus karena sudah digunakan di {$invoiceCount} invoice!");
                return;
            }
            
            $service->delete();
            session()->flash('success', 'Layanan berhasil dihapus!');
        }
    }

    public function render()
    {
        $query = Service::with('speciality');

        // Search
        if ($this->search) {
            $query->search($this->search);
        }

        // Filter by speciality
        if ($this->specialityFilter) {
            $query->where('speciality_id', $this->specialityFilter);
        }

        // Filter by status
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter);
        }

        $services = $query->orderBy('service_name')->paginate(15);

        // Get specialities for filter
        $specialities = Speciality::where('is_active', true)
            ->orderBy('speciality_name')
            ->get();

        // Statistics
        $stats = [
            'total' => Service::count(),
            'active' => Service::where('is_active', true)->count(),
            'inactive' => Service::where('is_active', false)->count(),
        ];

        return view('livewire.admin.service.index', [
            'services' => $services,
            'specialities' => $specialities,
            'stats' => $stats,
        ]);
    }
}