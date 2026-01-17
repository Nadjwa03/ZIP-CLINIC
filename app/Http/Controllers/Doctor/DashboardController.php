<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display doctor dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get doctor record with relationships
        $doctor = Doctor::with(['user', 'speciality'])
            ->where('doctor_user_id', $user->id)
            ->first();

        if (!$doctor) {
            abort(403, 'Anda tidak terdaftar sebagai dokter');
        }

        // Data will be loaded by Livewire components
        return view('doctor.dashboard', compact('doctor'));
    }
}
