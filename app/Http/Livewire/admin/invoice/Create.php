<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Visit;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $visit;
    public $patient;
    
    public $items = [];
    public $discount = 0;
    public $tax = 0;
    public $subtotal = 0;
    public $total = 0;
    public $notes = '';

    protected $rules = [
        'items' => 'required|array|min:1',
        'items.*.name' => 'required|string',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
        'discount' => 'nullable|numeric|min:0',
        'tax' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        // Get visit_id from query string
        $visitId = request()->query('visit');
        
        if (!$visitId) {
            session()->flash('error', 'Visit ID tidak ditemukan!');
            return redirect()->route('admin.invoices.index');
        }

        // Load visit with relationships
        $this->visit = Visit::with(['patient', 'doctor', 'details.service'])
            ->find($visitId);
        
        if (!$this->visit) {
            session()->flash('error', 'Data kunjungan tidak ditemukan!');
            return redirect()->route('admin.invoices.index');
        }

        // Check if invoice already exists for this visit
        if ($this->visit->invoice) {
            session()->flash('error', 'Invoice sudah dibuat untuk visit ini!');
            return redirect()->route('admin.invoices.show', $this->visit->invoice->invoice_id);
        }
        
        $this->patient = $this->visit->patient;
        
        // Auto-populate items from visit details
        if ($this->visit->details && $this->visit->details->count() > 0) {
            foreach ($this->visit->details as $detail) {
                if ($detail->service) {
                    $this->items[] = [
                        'type' => 'SERVICE',
                        'service_id' => $detail->service_id,
                        'name' => $detail->service->service_name,
                        'qty' => 1,
                        'unit_price' => $detail->service->price,
                        'discount' => 0,
                        'tooth_codes' => $detail->tooth_codes,
                    ];
                }
            }
        }
        
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->subtotal = 0;
        
        foreach ($this->items as $item) {
            $itemDiscount = $item['discount'] ?? 0;
            $itemTotal = ($item['qty'] * $item['unit_price']) - $itemDiscount;
            $this->subtotal += $itemTotal;
        }
        
        $this->total = $this->subtotal - $this->discount + $this->tax;
    }

    public function updateItemDiscount($index)
    {
        $this->calculateTotal();
    }

    public function updateItemQty($index)
    {
        $this->calculateTotal();
    }

    public function removeItem($index)
    {
        if (count($this->items) <= 1) {
            session()->flash('error', 'Invoice harus memiliki minimal 1 item!');
            return;
        }

        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function updatedDiscount()
    {
        $this->calculateTotal();
    }

    public function updatedTax()
    {
        $this->calculateTotal();
    }

    public function save()
    {
        // Validate
        $this->validate();

        if (empty($this->items) || count($this->items) === 0) {
            session()->flash('error', 'Invoice harus memiliki minimal 1 item layanan!');
            return;
        }

        if ($this->total <= 0) {
            session()->flash('error', 'Total invoice harus lebih dari 0!');
            return;
        }

        try {
            DB::beginTransaction();

            // Generate unique invoice number
            $today = date('Ymd');
            $prefix = 'INV-' . $today . '-';

            // Get last invoice number for today
            $lastInvoice = Invoice::where('number', 'LIKE', $prefix . '%')
                ->orderBy('number', 'desc')
                ->first();

            if ($lastInvoice) {
                // Extract the sequential number from last invoice
                $lastNumber = intval(substr($lastInvoice->number, -4));
                $nextNumber = $lastNumber + 1;
            } else {
                // First invoice of the day
                $nextNumber = 1;
            }

            $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Create invoice
            $invoice = Invoice::create([
                'visit_id' => $this->visit->visit_id,
                'number' => $invoiceNumber,
                'invoice_date' => today(),
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'total' => $this->total,
                'paid_amount' => 0,
                'status' => Invoice::STATUS_UNPAID,
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            // Create invoice items
            foreach ($this->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->invoice_id,
                    'item_type' => $item['type'] ?? 'SERVICE',
                    'service_id' => $item['service_id'] ?? null,
                    'name' => $item['name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tooth_codes' => $item['tooth_codes'] ?? null,
                ]);
            }

            DB::commit();

            session()->flash('success', 'Invoice berhasil dibuat: ' . $invoiceNumber);

            return redirect()->route('admin.invoices.show', $invoice->invoice_id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            session()->flash('error', 'Gagal membuat invoice: ' . $e->getMessage());
            \Log::error('Invoice creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.invoice.create');
    }
}
