<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verification;
use App\Mail\OtpEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PatientOtpLoginController extends Controller
{
    /**
     * Halaman awal: Form input email
     */
    public function showLoginForm(Request $request)
    {
        // Cek state dari session
        $state = $request->session()->get('patient_auth_state', 'email'); // email, otp, register
        $email = $request->session()->get('patient_login_email');
        $phone = $request->session()->get('patient_register_phone');

        return view('verification.patient-login', [
            'state' => $state,
            'email' => $email,
            'phone' => $phone,
        ]);
    }

    /**
     * Step 1: Cek email - sudah terdaftar atau belum
     */
    public function checkEmail(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // Simpan email ke session
        $request->session()->put('patient_login_email', $data['email']);

        // EMAIL BELUM TERDAFTAR → Arahkan ke form register
        if (!$user) {
            $request->session()->put('patient_auth_state', 'register');
            return redirect()->route('patient.login')
                ->with('info', 'Email belum terdaftar. Silakan lengkapi data untuk registrasi.');
        }

        // Bukan akun pasien
        if ($user->role !== 'patient') {
            $request->session()->forget(['patient_login_email', 'patient_auth_state']);
            return redirect()->route('patient.login')
                ->with('failed', 'Email ini bukan akun pasien. Silakan gunakan halaman login Admin/Dokter.');
        }

        // Akun inactive
        if ($user->status === 'inactive') {
            $request->session()->forget(['patient_login_email', 'patient_auth_state']);
            return redirect()->route('patient.login')
                ->with('failed', 'Akun sudah dinonaktifkan. Hubungi admin.');
        }

        // Akun masih verify (belum aktif) - kirim OTP verifikasi
        if ($user->status === 'verify') {
            return $this->sendOtp($request, $user, 'register');
        }

        // EMAIL SUDAH TERDAFTAR & ACTIVE → Kirim OTP login
        return $this->sendOtp($request, $user, 'login');
    }

    /**
     * Step 2a: Register pasien baru
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        // Buat user baru dengan status 'verify'
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make(Str::random(16)), // Random password (tidak dipakai)
            'role'     => 'patient',
            'status'   => 'verify',
        ]);

        // Simpan ke session
        $request->session()->put('patient_login_email', $user->email);

        // Kirim OTP untuk verifikasi
        return $this->sendOtp($request, $user, 'register');
    }

    /**
     * Kirim OTP (reusable untuk login & register)
     */
    private function sendOtp(Request $request, User $user, string $type)
    {
        // Generate OTP 6 digit
        $otp = (string) random_int(100000, 999999);

        // Invalidasi OTP sebelumnya
        Verification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('status', 'active')
            ->update(['status' => 'invalid']);

        // Simpan OTP baru
        Verification::create([
            'user_id'    => $user->id,
            'unique_id'  => (string) Str::uuid(),
            'otp'        => Hash::make($otp),
            'type'       => $type,
            'send_via'   => 'email',
            'resend'     => 0,
            'attempts'   => 0,
            'status'     => 'active',
            'expires_at' => now()->addMinutes(5),
        ]);

        // Kirim email
        try {
            Mail::to($user->email)->send(new OtpEmail($otp, $type));
            Log::info("OTP {$type} dikirim ke: {$user->email}, OTP: {$otp}");
        } catch (\Exception $e) {
            Log::error("Gagal kirim OTP: " . $e->getMessage());
            return redirect()->route('patient.login')
                ->with('failed', 'Gagal mengirim email OTP. Silakan coba lagi.');
        }

        // Update state ke OTP
        $request->session()->put('patient_auth_state', 'otp');
        $request->session()->put('patient_otp_type', $type);

        $message = $type === 'register' 
            ? 'Kode OTP verifikasi telah dikirim ke email kamu.'
            : 'Kode OTP login telah dikirim ke email kamu.';

        return redirect()->route('patient.login')->with('success', $message);
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $email = $request->session()->get('patient_login_email');
        $otpType = $request->session()->get('patient_otp_type', 'login');

        if (!$email) {
            $this->clearSession($request);
            return redirect()->route('patient.login')
                ->with('failed', 'Session habis. Silakan mulai dari awal.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->clearSession($request);
            return redirect()->route('patient.login')
                ->with('failed', 'Email tidak ditemukan.');
        }

        $record = Verification::where('user_id', $user->id)
            ->where('type', $otpType)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$record) {
            return redirect()->route('patient.login')
                ->with('failed', 'OTP tidak ditemukan atau sudah tidak aktif.');
        }

        // Cek expired
        if ($record->expires_at && now()->greaterThan($record->expires_at)) {
            $record->update(['status' => 'invalid']);
            return redirect()->route('patient.login')
                ->with('failed', 'OTP sudah kadaluarsa. Silakan kirim ulang.');
        }

        // Increment attempts
        $record->increment('attempts');

        // Max 5 attempts
        if ($record->attempts > 5) {
            $record->update(['status' => 'invalid']);
            return redirect()->route('patient.login')
                ->with('failed', 'Terlalu banyak percobaan salah. Silakan kirim ulang OTP.');
        }

        // Verifikasi OTP
        if (!Hash::check($data['otp'], $record->otp)) {
            $remaining = 5 - $record->attempts;
            return redirect()->route('patient.login')
                ->with('failed', "Kode OTP salah. Sisa percobaan: {$remaining}");
        }

        // OTP benar
        $record->update([
            'status'  => 'used',
            'used_at' => now(),
        ]);

        // Jika register, aktifkan akun
        if ($otpType === 'register' && $user->status === 'verify') {
            $user->update(['status' => 'active']);
        }

        // Login user
        Auth::login($user, true);
        $request->session()->regenerate();
        $this->clearSession($request);

        $message = $otpType === 'register'
            ? 'Registrasi berhasil! Selamat datang di Klinik ZIP.'
            : 'Login berhasil!';

        return redirect()->route('patient.dashboard')->with('success', $message);
    }

    /**
     * Kirim ulang OTP
     */
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('patient_login_email');
        $otpType = $request->session()->get('patient_otp_type', 'login');

        if (!$email) {
            $this->clearSession($request);
            return redirect()->route('patient.login')
                ->with('failed', 'Session habis. Silakan mulai dari awal.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->clearSession($request);
            return redirect()->route('patient.login')
                ->with('failed', 'Email tidak ditemukan.');
        }

        return $this->sendOtp($request, $user, $otpType);
    }

    /**
     * Reset/Cancel - kembali ke form email
     */
    public function cancel(Request $request)
    {
        $this->clearSession($request);
        return redirect()->route('patient.login');
    }

    /**
     * Clear all patient auth session
     */
    private function clearSession(Request $request)
    {
        $request->session()->forget([
            'patient_login_email',
            'patient_auth_state',
            'patient_otp_type',
            'patient_register_phone',
        ]);
    }
}