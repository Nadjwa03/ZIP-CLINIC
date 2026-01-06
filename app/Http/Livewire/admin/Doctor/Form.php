<?php

namespace App\Http\Livewire\Admin\Doctor;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Speciality;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Form extends Component
{
    use WithFileUploads;

    public $doctorId;
    
    // User fields
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    
    // Doctor fields
    public $registration_number;
    public $display_name;
    public $speciality_id;
    public $bio;
    public $photo;
    public $existing_photo_path;
    
    public $specialities = [];
    public $isEdit = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->doctorId ?? 'NULL') . ',id',
            'phone' => 'required|string|max:30',
            'registration_number' => 'required|string|max:50|unique:doctors,registration_number,' . ($this->doctorId ?? 'NULL') . ',doctor_user_id',
            'display_name' => 'required|string|min:2|max:120',
            'speciality_id' => 'required|exists:specialities,speciality_id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:8192',
        ];

        if (!$this->isEdit) {
            $rules['password'] = 'required|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama dokter wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.unique' => 'Email sudah terdaftar',
        'phone.required' => 'Nomor telepon wajib diisi',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 6 karakter',
        'password.confirmed' => 'Konfirmasi password tidak cocok',
        'registration_number.required' => 'Nomor registrasi (SIP/STR) wajib diisi',
        'registration_number.unique' => 'Nomor registrasi sudah terdaftar',
        'display_name.required' => 'Nama tampilan wajib diisi',
        'speciality_id.required' => 'Spesialisasi wajib dipilih',
        'photo.image' => 'File harus berupa gambar',
        'photo.max' => 'Ukuran foto maksimal 8MB',
    ];

    public function mount($doctorId = null)
    {
        $this->doctorId = $doctorId;
        $this->isEdit = !is_null($doctorId);

        // Load specialities
        $this->specialities = Speciality::where('is_active', true)
            ->orderBy('speciality_name')
            ->get();

        // If edit mode, load doctor data
        if ($this->isEdit) {
            $doctor = Doctor::with('user')->findOrFail($doctorId);
            
            // User data
            $this->name = $doctor->user->name;
            $this->email = $doctor->user->email;
            $this->phone = $doctor->user->phone ?? $doctor->phone;
            
            // Doctor data
            $this->registration_number = $doctor->registration_number;
            $this->display_name = $doctor->display_name;
            $this->speciality_id = $doctor->speciality_id;
            $this->bio = $doctor->bio;
            $this->existing_photo_path = $doctor->photo_path;
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            if ($this->isEdit) {
                // Update existing doctor
                $doctor = Doctor::with('user')->findOrFail($this->doctorId);
                
                // Update user
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ];
                
                if ($this->password) {
                    $userData['password'] = Hash::make($this->password);
                }
                
                $doctor->user->update($userData);
                
                // Update doctor
                $doctorData = [
                    'registration_number' => $this->registration_number,
                    'display_name' => $this->display_name,
                    'speciality_id' => $this->speciality_id,
                    'bio' => $this->bio,
                    'phone' => $this->phone,
                ];
                
                // Handle photo upload
                if ($this->photo) {
                    // Delete old photo
                    if ($this->existing_photo_path) {
                        Storage::disk('public')->delete($this->existing_photo_path);
                    }
                    
                    $doctorData['photo_path'] = $this->photo->store('doctors', 'public');
                }
                
                $doctor->update($doctorData);
                
                session()->flash('success', 'Data dokter berhasil diupdate!');
            } else {
                // Create new user
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'role' => 'doctor',
                    'status' => 'active',
                    'password' => Hash::make($this->password),
                ]);
                
                // Create doctor profile
                $doctorData = [
                    'doctor_user_id' => $user->id,
                    'registration_number' => $this->registration_number,
                    'display_name' => $this->display_name,
                    'speciality_id' => $this->speciality_id,
                    'bio' => $this->bio,
                    'phone' => $this->phone,
                    'is_active' => true,
                ];
                
                // Handle photo upload
                if ($this->photo) {
                    $doctorData['photo_path'] = $this->photo->store('doctors', 'public');
                }
                
                Doctor::create($doctorData);
                
                session()->flash('success', 'Dokter baru berhasil ditambahkan!');
            }

            DB::commit();
            return redirect()->route('admin.doctors.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.doctor.form');
    }
}
