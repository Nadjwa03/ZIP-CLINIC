<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Visit;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $patient = auth()->guard('patient')->user();
        
        $records = Visit::where('patient_id', $patient->patient_id)
            ->with(['doctor.user', 'visitDetails.service'])
            ->orderBy('visit_at', 'desc')
            ->paginate(10);

        return view('pasien.medical-records.index', compact('records'));
    }
    
    public function show($id)
    {
        $patient = auth()->guard('patient')->user();
        
        $record = Visit::where('patient_id', $patient->patient_id)
            ->where('visit_id', $id)
            ->with(['doctor.user', 'visitDetails.service', 'media'])
            ->firstOrFail();

        return view('pasien.medical-records.show', compact('record'));
    }

    
    public function download($id)
    {
        $record = Visit::where('patient_id', Auth::id())
            ->findOrFail($id);
        
        // TODO: Implement PDF generation
        return redirect()->back()->with('info', 'Download PDF coming soon!');
    }
}