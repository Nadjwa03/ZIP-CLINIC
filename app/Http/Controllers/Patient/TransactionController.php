<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

class TransactionController extends Controller
{
    public function index()
    {
        $patient = Auth::user();
        
        $transactions = Invoice::with(['appointment', 'appointment.service'])
            ->where('patient_id', $patient->id)
            ->orderBy('invoice_date', 'desc')
            ->paginate(10);
        
        return view('pasien.transactions.index', compact('transactions'));
    }
    
    public function show($id)
    {
        $transaction = Invoice::with(['items', 'appointment'])
            ->where('patient_id', Auth::id())
            ->findOrFail($id);
        
        return view('pasien.transactions.show', compact('transaction'));
    }
    
    public function download($id)
    {
        $transaction = Invoice::where('patient_id', Auth::id())
            ->findOrFail($id);
        
        // TODO: Implement PDF generation
        return redirect()->back()->with('info', 'Download invoice PDF coming soon!');
    }
}