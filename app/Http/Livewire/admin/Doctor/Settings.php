<?php

namespace App\Http\Livewire\Admin\Doctor;

use Livewire\Component;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Settings extends Component
{
    public $doctorId;
    public $doctor;

    // Change password
    public $new_password;
    public $new_password_confirmation;

    protected $rules = [
        'new_password' => 'required|min:8|confirmed',
    ];

    protected $messages = [
        'new_password.required' => 'Password baru harus diisi',
        'new_password.min' => 'Password minimal 8 karakter',
        'new_password.confirmed' => 'Konfirmasi password tidak cocok',
    ];

    public function mount($doctorId)
    {
        $this->doctorId = $doctorId;
        $this->doctor = Doctor::with('user')->findOrFail($doctorId);
    }

    public function toggleStatus()
    {
        try {
            if ($this->doctor->deleted_at) {
                // Restore
                $this->doctor->restore();
                session()->flash('success', 'Dokter berhasil diaktifkan kembali!');
            } else {
                // Soft delete
                $this->doctor->delete();
                session()->flash('success', 'Dokter berhasil dinonaktifkan!');
            }

            $this->doctor->refresh();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function deleteDoctor()
    {
        try {
            // Check if doctor has appointments
            $appointmentCount = $this->doctor->appointments()->count();

            if ($appointmentCount > 0) {
                session()->flash('error', 'Tidak dapat menghapus dokter yang memiliki riwayat appointment!');
                return;
            }

            DB::beginTransaction();

            // Delete user account
            $user = $this->doctor->user;

            // Force delete doctor (this will cascade to schedules)
            $this->doctor->forceDelete();

            // Delete user
            $user->delete();

            DB::commit();

            session()->flash('success', 'Dokter berhasil dihapus!');

            return redirect()->route('admin.doctors.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus dokter: ' . $e->getMessage());
        }
    }

    public function changePassword()
    {
        $this->validate();

        try {
            $user = $this->doctor->user;
            $user->password = Hash::make($this->new_password);
            $user->save();

            $this->reset(['new_password', 'new_password_confirmation']);

            session()->flash('success', 'Password berhasil diubah!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.doctor.settings');
    }
}
