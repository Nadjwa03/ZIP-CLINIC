<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Visit;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Tab management
    public $activeTab = 'pending'; // 'pending' or 'invoices'

    // Properties
    public $search = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $paginationTheme = 'tailwind';

    // Reset pagination when search/filter changes
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function viewInvoice($invoiceId)
    {
        return redirect()->route('admin.invoices.show', $invoiceId);
    }

    public function createInvoice($visitId)
    {
        return redirect()->route('admin.invoices.create', ['visit' => $visitId]);
    }

    public function deleteInvoice($invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            // Only allow delete if status is DRAFT
            if ($invoice->status !== Invoice::STATUS_DRAFT) {
                session()->flash('error', 'Hanya invoice dengan status DRAFT yang bisa dihapus!');
                return;
            }

            $invoice->delete();
            session()->flash('success', 'Invoice berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Data untuk tab pending
        if ($this->activeTab === 'pending') {
            $query = Visit::with(['patient', 'doctor', 'appointment.service'])
                ->where('status', Visit::STATUS_DONE)
                ->whereDoesntHave('invoice')
                ->orderBy('visit_at', 'desc');

            // Search filter untuk pending
            if ($this->search) {
                $query->whereHas('patient', function($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('medical_record_number', 'like', '%' . $this->search . '%');
                });
            }

            $pendingVisits = $query->paginate(15);
            $invoices = collect(); // Empty collection for invoices tab
        } else {
            // Data untuk tab invoices
            $query = Invoice::with(['visit.patient'])
                ->orderBy('invoice_date', 'desc')
                ->orderBy('created_at', 'desc');

            // Search filter
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('visit.patient', function($q2) {
                          $q2->where('full_name', 'like', '%' . $this->search . '%')
                             ->orWhere('medical_record_number', 'like', '%' . $this->search . '%');
                      });
                });
            }

            // Status filter
            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            // Date range filter
            if ($this->dateFrom) {
                $query->whereDate('invoice_date', '>=', $this->dateFrom);
            }

            if ($this->dateTo) {
                $query->whereDate('invoice_date', '<=', $this->dateTo);
            }

            $invoices = $query->paginate(15);
            $pendingVisits = collect(); // Empty collection for pending tab
        }

        // Statistics
        $stats = [
            'pending_invoice' => Visit::where('status', Visit::STATUS_DONE)
                ->whereDoesntHave('invoice')
                ->count(),
            'total_invoices' => Invoice::count(),
            'unpaid_invoices' => Invoice::where('status', Invoice::STATUS_UNPAID)->count(),
            'paid_today' => Invoice::where('status', Invoice::STATUS_PAID)
                ->whereDate('paid_at', today())
                ->count(),
        ];

        return view('livewire.admin.invoice.index', [
            'pendingVisits' => $pendingVisits,
            'invoices' => $invoices,
            'stats' => $stats,
        ]);
    }
}