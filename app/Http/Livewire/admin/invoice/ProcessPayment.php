<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ProcessPayment extends Component
{
    public $invoiceId;
    public $invoice;
    
    public $paymentAmount = 0;
    public $paymentMethod = 'CASH';
    public $referenceNumber = '';
    public $notes = '';
    public $maxAmount = 0;

    protected $rules = [
        'paymentAmount' => 'required|numeric|min:1',
        'paymentMethod' => 'required|in:CASH,QRIS,TRANSFER,DEBIT,CREDIT',
        'referenceNumber' => 'nullable|string|max:80',
        'notes' => 'nullable|string',
    ];

    public function mount($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        $this->invoice = Invoice::findOrFail($invoiceId);
        $this->maxAmount = $this->invoice->outstanding;
        $this->paymentAmount = $this->maxAmount;
    }

    public function updatedPaymentAmount()
    {
        // Validate amount tidak melebihi outstanding
        if ($this->paymentAmount > $this->maxAmount) {
            $this->paymentAmount = $this->maxAmount;
            session()->flash('warning', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan!');
        }
    }

    public function updatedPaymentMethod()
    {
        // Reset reference number jika bukan TRANSFER
        if ($this->paymentMethod !== 'TRANSFER') {
            $this->referenceNumber = '';
        }
    }

    public function savePayment()
    {
        // Custom validation for TRANSFER
        $rules = $this->rules;
        if ($this->paymentMethod === 'TRANSFER') {
            $rules['referenceNumber'] = 'required|string|max:80';
        }

        $this->validate($rules, [
            'paymentAmount.required' => 'Jumlah pembayaran wajib diisi',
            'paymentAmount.min' => 'Jumlah pembayaran minimal Rp 1',
            'referenceNumber.required' => 'Nomor referensi wajib diisi untuk metode Transfer',
        ]);

        // Validate amount
        if ($this->paymentAmount > $this->invoice->outstanding) {
            session()->flash('error', 'Jumlah pembayaran melebihi sisa tagihan!');
            return;
        }

        if ($this->paymentAmount <= 0) {
            session()->flash('error', 'Jumlah pembayaran harus lebih dari 0!');
            return;
        }

        try {
            DB::beginTransaction();

            // Create payment
            $payment = Payment::create([
                'invoice_id' => $this->invoice->invoice_id,
                'amount' => $this->paymentAmount,
                'method' => $this->paymentMethod,
                'ref_no' => $this->referenceNumber,
                'notes' => $this->notes,
                'verify_status' => 'VERIFIED', // Auto verified untuk CASH/DEBIT/CREDIT
                'paid_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            // Update invoice paid_amount
            $this->invoice->paid_amount += $this->paymentAmount;

            // Update invoice status
            if ($this->invoice->paid_amount >= $this->invoice->total) {
                // Fully paid
                $this->invoice->status = Invoice::STATUS_PAID;
                $this->invoice->paid_at = now();
            } elseif ($this->invoice->paid_amount > 0) {
                // Partially paid
                $this->invoice->status = Invoice::STATUS_PARTIALLY_PAID;
            }

            $this->invoice->save();

            DB::commit();

            session()->flash('success', 'Pembayaran berhasil diproses! Invoice ' . 
                ($this->invoice->status === Invoice::STATUS_PAID ? 'sudah lunas' : 'dibayar sebagian'));

            // Emit event to parent component
            $this->dispatch('paymentProcessed');
            
            // Close modal
            $this->dispatch('closePaymentModal');

        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            \Log::error('Payment processing failed', [
                'invoice_id' => $this->invoice->invoice_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function closeModal()
    {
        $this->dispatch('closePaymentModal');
    }

    public function render()
    {
        return view('livewire.admin.invoice.process-payment');
    }
}
