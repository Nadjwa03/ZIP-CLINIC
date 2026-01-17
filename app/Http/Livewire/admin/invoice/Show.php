<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Component;

class Show extends Component
{
    public $invoice;
    public $payments;
    public $showPaymentModal = false;

    protected $listeners = ['paymentProcessed' => 'refreshInvoice'];

    public function mount($invoiceId)
    {
        $this->loadInvoice($invoiceId);
    }

    public function loadInvoice($invoiceId)
    {
        $this->invoice = Invoice::with([
            'visit.patient',
            'visit.doctor',
            'items.service',
            'payments' => function($query) {
                $query->orderBy('paid_at', 'desc');
            }
        ])->findOrFail($invoiceId);

        $this->payments = $this->invoice->payments;
    }

    public function refreshInvoice()
    {
        $this->loadInvoice($this->invoice->invoice_id);
        $this->showPaymentModal = false;
    }

    public function openPaymentModal()
    {
        if ($this->invoice->outstanding <= 0) {
            session()->flash('error', 'Invoice sudah lunas!');
            return;
        }

        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    public function printInvoice()
    {
        // Redirect to print view
        return redirect()->route('admin.invoices.print', $this->invoice->invoice_id);
    }

    public function deleteInvoice()
    {
        try {
            // Only allow delete if status is DRAFT
            if ($this->invoice->status !== Invoice::STATUS_DRAFT) {
                session()->flash('error', 'Hanya invoice dengan status DRAFT yang bisa dihapus!');
                return;
            }

            $this->invoice->delete();
            session()->flash('success', 'Invoice berhasil dihapus!');
            
            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus invoice: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.invoice.show');
    }
}
