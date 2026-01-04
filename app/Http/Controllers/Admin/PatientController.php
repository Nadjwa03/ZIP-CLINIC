<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('medical_record_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Get patients with pagination
        $patients = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total' => Patient::count(),
            'active' => Patient::where('is_active', true)->count(),
            'today' => Patient::whereDate('created_at', today())->count(),
            'this_month' => Patient::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count(),
        ];

        return view('admin.patients.index', compact('patients', 'stats'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Store a newly created patient
     */
    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'id_type' => 'required|in:KTP,SIM,PASSPORT,KK',
            'id_number' => 'required|string|max:50|unique:patients,id_number',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'email' => 'required|email|unique:patients,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi',
            'id_type.required' => 'Jenis identitas wajib dipilih',
            'id_number.required' => 'Nomor identitas wajib diisi',
            'id_number.unique' => 'Nomor identitas sudah terdaftar',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'date_of_birth.before' => 'Tanggal lahir tidak valid',
            'gender.required' => 'Jenis kelamin wajib dipilih',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
            'address.required' => 'Alamat wajib diisi',
        ]);

        try {
            DB::beginTransaction();

            // Generate Medical Record Number
            $mrn = $this->generateMedicalRecordNumber();

            // Create patient
            $patient = Patient::create([
                'medical_record_number' => $mrn,
                'full_name' => $validated['full_name'],
                'id_type' => $validated['id_type'],
                'id_number' => $validated['id_number'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'blood_type' => $validated['blood_type'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'medical_history' => $validated['medical_history'] ?? null,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.patients.index')
                ->with('success', "Pasien {$patient->full_name} berhasil didaftarkan dengan No. RM: {$mrn}");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mendaftarkan pasien: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        // Validation
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'id_type' => 'required|in:KTP,SIM,PASSPORT,KK',
            'id_number' => 'required|string|max:50|unique:patients,id_number,' . $patient->id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        try {
            $patient->update($validated);

            return redirect()
                ->route('admin.patients.show', $patient)
                ->with('success', 'Data pasien berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle patient status (active/inactive)
     */
    public function toggleStatus(Patient $patient)
    {
        try {
            $patient->update([
                'is_active' => !$patient->is_active
            ]);

            $status = $patient->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()
                ->back()
                ->with('success', "Pasien berhasil {$status}");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        try {
            $name = $patient->full_name;
            $patient->delete();

            return redirect()
                ->route('admin.patients.index')
                ->with('success', "Data pasien {$name} berhasil dihapus");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique medical record number
     */
    private function generateMedicalRecordNumber()
    {
        $prefix = 'RM';
        $date = now()->format('Ymd');
        
        // Get last patient today
        $lastPatient = Patient::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPatient) {
            // Extract sequence from last MRN
            $lastMrn = $lastPatient->medical_record_number;
            $lastSequence = (int) substr($lastMrn, -4);
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        return $prefix . $date . $sequence;
    }
}
