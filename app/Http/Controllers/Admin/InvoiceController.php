<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display list of visits ready for invoicing
     * (visits yang sudah DONE tapi belum ada invoice)
     */
    public function index(Request $request)
    {
        // Get visits yang sudah selesai (DONE) dan belum punya invoice
        $query = Visit::with(['patient', 'doctor', 'appointment.service'])
            ->where('status', Visit::STATUS_DONE)
            ->whereDoesntHave('invoice') // Belum ada invoice
            ->orderBy('visit_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('medical_record_number', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('visit_at', $request->date);
        }

        $readyForInvoice = $query->paginate(15);

        // Stats
        $stats = [
            'pending_invoice' => Visit::where('status', Visit::STATUS_DONE)
                ->whereDoesntHave('invoice')
                ->count(),
            'invoiced_today' => Invoice::whereDate('created_at', today())->count(),
            'total_amount_today' => Invoice::whereDate('created_at', today())->sum('total_amount'),
        ];

        return view('admin.invoices.index', compact('readyForInvoice', 'stats'));
    }

    /**
     * Show form to create invoice for a visit
     */
    public function create(Request $request)
    {
        $visitId = $request->get('visit');

        if (!$visitId) {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Visit ID tidak ditemukan');
        }

        $visit = Visit::with([
            'patient',
            'doctor',
            'appointment.service',
            'details.service'
        ])->findOrFail($visitId);

        // Check if already has invoice
        if ($visit->invoice) {
            return redirect()->route('admin.invoices.show', $visit->invoice->invoice_id)
                ->with('info', 'Invoice sudah dibuat untuk visit ini');
        }

        // Check if visit is completed
        if ($visit->status !== Visit::STATUS_DONE) {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Visit belum selesai. Tidak bisa membuat invoice.');
        }

        return view('admin.invoices.create', compact('visit'));
    }

    /**
     * Store invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,visit_id',
            'payment_method' => 'required|in:CASH,DEBIT,CREDIT,TRANSFER,INSURANCE',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $visit = Visit::with(['details.service'])->findOrFail($validated['visit_id']);

            // Check if already has invoice
            if ($visit->invoice) {
                DB::rollBack();
                return redirect()->route('admin.invoices.show', $visit->invoice->invoice_id)
                    ->with('info', 'Invoice sudah dibuat sebelumnya');
            }

            // Calculate total from visit details
            $subtotal = $visit->details->sum(function($detail) {
                return $detail->service->price ?? 0;
            });

            $discountAmount = $validated['discount_amount'] ?? 0;
            $totalAmount = $subtotal - $discountAmount;

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'visit_id' => $visit->visit_id,
                'patient_id' => $visit->patient_id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'PENDING',
                'notes' => $validated['notes'] ?? null,
                'issued_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice->invoice_id)
                ->with('success', 'Invoice berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display invoice
     */
    public function show($invoiceId)
    {
        $invoice = Invoice::with([
            'visit.patient',
            'visit.doctor',
            'visit.details.service',
            'issuedBy'
        ])->findOrFail($invoiceId);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Update payment status
     */
    public function updatePayment(Request $request, $invoiceId)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:PENDING,PAID,CANCELLED',
            'paid_at' => 'required_if:payment_status,PAID|nullable|date',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        try {
            $invoice = Invoice::findOrFail($invoiceId);

            $invoice->update([
                'payment_status' => $validated['payment_status'],
                'paid_at' => $validated['payment_status'] === 'PAID' ? $validated['paid_at'] : null,
                'payment_notes' => $validated['payment_notes'] ?? null,
            ]);

            return back()->with('success', 'Status pembayaran berhasil diupdate');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');

        $lastInvoice = Invoice::whereDate('created_at', today())
            ->orderBy('invoice_id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        return $prefix . $date . $sequence;
    }
}
