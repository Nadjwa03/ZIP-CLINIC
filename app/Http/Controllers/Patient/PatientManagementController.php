<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientManagementController extends Controller
{
    /**
     * Display a listing of user's patients
     */
    public function index()
    {
        $user = Auth::user();
        
        $patients = Patient::where('owner_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pasien.patients.index', compact('patients'));
    }
    
    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('pasien.patients.create');
    }
    
    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validate input
        $validated = $request->validate([
            'id_type' => 'required|in:KTP,SIM,PASSPORT,KK',
            'id_number' => 'required|string|max:50',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'nullable|email|max:255',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);
        
        // Auto-fill email if not provided
        if (empty($validated['email'])) {
            $validated['email'] = $user->email;
        }
        
        DB::beginTransaction();
        
        try {
            // Generate Medical Record Number
            $lastPatient = Patient::withTrashed()
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($lastPatient && $lastPatient->medical_record_number) {
                $lastNumber = (int) substr($lastPatient->medical_record_number, 2);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $medicalRecordNumber = 'MR' . str_pad($newNumber, 10, '0', STR_PAD_LEFT);
            
            // Create patient
            $patient = Patient::create([
                'owner_user_id' => $user->id,
                'medical_record_number' => $medicalRecordNumber,
                'id_type' => $validated['id_type'],
                'id_number' => $validated['id_number'],
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'blood_type' => $validated['blood_type'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'medical_history' => $validated['medical_history'] ?? null,
                'is_active' => true,
            ]);
            
            // Set as selected patient in session
            $request->session()->put('selected_patient_id', $patient->id);
            
            DB::commit();
            
            // Redirect to patients list with success message
            return redirect()->route('pasien.patients.index')
                ->with('success', 'Pasien berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Gagal menambahkan pasien: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show success page
     */
    public function success()
    {
        return view('pasien.patients.success');
    }
    
    /**
     * Show the form for editing patient
     */
    public function edit(Patient $patient)
    {
        $user = Auth::user();
        
        // Check if patient belongs to user
        if ($patient->owner_user_id != $user->id) {
            abort(403, 'Unauthorized');
        }
        
        return view('pasien.patients.edit', compact('patient'));
    }
    
    /**
     * Update patient
     */
    public function update(Request $request, Patient $patient)
    {
        $user = Auth::user();
        
        // Check if patient belongs to user
        if ($patient->owner_user_id != $user->id) {
            abort(403, 'Unauthorized');
        }
        
        // Validate input
        $validated = $request->validate([
            'id_type' => 'required|in:KTP,SIM,PASSPORT,KK',
            'id_number' => 'required|string|max:50',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'nullable|email|max:255',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);
        
        // Auto-fill email if not provided
        if (empty($validated['email'])) {
            $validated['email'] = $user->email;
        }
        
        DB::beginTransaction();
        
        try {
            // Update patient with all fields
            $patient->update([
                'id_type' => $validated['id_type'],
                'id_number' => $validated['id_number'],
                'full_name' => $validated['full_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'blood_type' => $validated['blood_type'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'medical_history' => $validated['medical_history'] ?? null,
            ]);
            
            DB::commit();
            
            return redirect()->route('pasien.patients.index')
                ->with('success', 'Data pasien berhasil diupdate');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Gagal mengupdate pasien: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show claim patient form
     */
    public function showClaimForm()
    {
        return view('pasien.patients.claim');
    }
    
    /**
     * Claim existing patient with code
     */
    public function claim(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'patient_code' => 'required|string',
            'secret_code' => 'required|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Find patient by medical_record_number (patient_code)
            $patient = Patient::where('medical_record_number', $request->patient_code)
                ->whereNull('owner_user_id') // Patient belum di-claim
                ->first();
            
            if (!$patient) {
                return back()->with('error', 'Patient Code tidak ditemukan atau sudah diklaim oleh user lain.')
                    ->withInput();
            }
            
            // Verify secret code
            if ($patient->secret_code !== $request->secret_code) {
                return back()->with('error', 'Secret Code salah. Silakan coba lagi.')
                    ->withInput(['patient_code' => $request->patient_code]);
            }
            
            // Claim patient - assign to current user
            $patient->update([
                'owner_user_id' => $user->id,
                'claimed_at' => now(),
            ]);
            
            // Set as selected patient in session
            $request->session()->put('selected_patient_id', $patient->id);
            
            DB::commit();
            
            return redirect()->route('pasien.patients.index')
                ->with('success', 'Profil pasien berhasil diklaim! Selamat datang, ' . $patient->full_name);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Gagal mengklaim profil pasien: ' . $e->getMessage())
                ->withInput();
        }
    }
}