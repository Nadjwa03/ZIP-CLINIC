<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors with search and filter
     */
    public function index(Request $request)
    {
        $doctorsQuery = Doctor::with('user')->withTrashed();

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $doctorsQuery->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $doctorsQuery->whereNotNull('deleted_at');
            }
        }

        // Search functionality
        if (!empty($request->search)) {
            $keyword = $request->search;
            $doctorsQuery->where(function ($query) use ($keyword) {
                $query->where('display_name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('registration_number', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('speciality', 'LIKE', '%' . $keyword . '%');
            });
        }

        $doctors = $doctorsQuery->orderBy('updated_at', 'desc')->paginate(10);

        return view('admin.doctors.index', [
            'doctors' => $doctors,
            'status' => $request->status
        ]);
    }

    /**
     * Show the form for creating a new doctor
     */
   public function view_create()
{
    return view('admin.doctors.create', [
        'mode' => 'create',
        'data' => null,
    ]);
}

    /**
     * Store a newly created doctor
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:6',
            'registration_number' => 'required|unique:doctors',
            'display_name' => 'required|min:2',
            'speciality' => 'required',
            'photo' => 'nullable|file|mimes:jpg,png,webp|max:8192',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 6 karakter.',
            'registration_number.required' => 'Nomor SIP tidak boleh kosong.',
            'registration_number.unique' => 'Nomor SIP sudah terdaftar.',
            'display_name.required' => 'Nama tampilan tidak boleh kosong.',
            'speciality.required' => 'Spesialisasi tidak boleh kosong.',
            'photo.mimes' => 'Foto harus berekstensi JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran file foto maksimum 8MB.'
        ]);

        // Create user account first
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'DOCTOR',
            'status' => 'ACTIVE',
            'password' => Hash::make($request->password),
        ]);

        // Create doctor profile
        $doctorData = [
            'user_id' => $user->id,
            'registration_number' => $request->registration_number,
            'display_name' => $request->display_name,
            'speciality' => $request->speciality,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'is_active' => true,
        ];

        // Handle photo upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $filePath = $request->file('photo')->store('doctors', 'public');
            $doctorData['photo_path'] = $filePath;
        }

        $doctor = Doctor::create($doctorData);

        // Create schedules if provided
        if ($request->has('schedules')) {
            foreach ($request->schedules as $schedule) {
                if (!empty($schedule['day_of_week']) && !empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                    DoctorSchedule::create([
                        'doctor_user_id' => $doctor->user_id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.doctors.index')->with('success', 'Dokter berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified doctor
     */
    public function view_edit(string $id)
    {
        $doctor = Doctor::with(['user', 'schedules'])->withTrashed()->findOrFail($id);

        return view('admin.doctors.form', ['mode' => 'edit', 'data' => $doctor]);
    }

    /**
     * Update the specified doctor
     */
    public function edit(Request $request, string $id)
    {
        $doctor = Doctor::with('user')->withTrashed()->findOrFail($id);

        $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'phone' => 'required',
            'registration_number' => 'required|unique:doctors,registration_number,' . $id . ',user_id',
            'display_name' => 'required|min:2',
            'speciality' => 'required',
            'photo' => 'nullable|file|mimes:jpg,png,webp|max:8192',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon tidak boleh kosong.',
            'registration_number.required' => 'Nomor SIP tidak boleh kosong.',
            'registration_number.unique' => 'Nomor SIP sudah terdaftar.',
            'display_name.required' => 'Nama tampilan tidak boleh kosong.',
            'speciality.required' => 'Spesialisasi tidak boleh kosong.',
            'photo.mimes' => 'Foto harus berekstensi JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran file foto maksimum 8MB.'
        ]);

        // Update user account
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $doctor->user->update($userData);

        // Update doctor profile
        $doctorData = [
            'registration_number' => $request->registration_number,
            'display_name' => $request->display_name,
            'speciality' => $request->speciality,
            'phone' => $request->phone,
            'bio' => $request->bio,
        ];

        // Handle photo upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // Delete old photo if exists
            if ($doctor->photo_path) {
                Storage::disk('public')->delete($doctor->photo_path);
            }

            $doctorData['photo_path'] = $request->file('photo')->store('doctors', 'public');
        }

        $doctor->update($doctorData);

        // Update schedules
        if ($request->has('schedules')) {
            // Delete existing schedules
            DoctorSchedule::where('doctor_user_id', $doctor->user_id)->delete();

            // Create new schedules
            foreach ($request->schedules as $schedule) {
                if (!empty($schedule['day_of_week']) && !empty($schedule['start_time']) && !empty($schedule['end_time'])) {
                    DoctorSchedule::create([
                        'doctor_user_id' => $doctor->user_id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.doctors.index')->with('success', 'Dokter berhasil diupdate!');
    }

    /**
     * Soft delete the specified doctor
     */
    public function deactivate(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'Dokter berhasil dinonaktifkan!');
    }

    /**
     * Restore the specified doctor
     */
    public function activate(string $id)
    {
        $doctor = Doctor::onlyTrashed()->findOrFail($id);
        $doctor->restore();

        return redirect()->route('admin.doctors.index')->with('success', 'Dokter berhasil diaktifkan kembali!');
    }
}