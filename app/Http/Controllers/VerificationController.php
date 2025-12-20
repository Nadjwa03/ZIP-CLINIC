<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Mail\OtpEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Tampilkan halaman verifikasi (untuk register/reset password)
     */
    public function index(Request $request)
    {
        // Kalau sudah active, redirect ke area pasien
        if ($request->user()->status === 'active') {
            return redirect()->route('pasien');
        }

        return view('verification.index');
    }

    /**
     * Kirim OTP untuk register/reset password
     */
    public function send(Request $request)
    {
        $request->validate([
            'type'     => ['required', 'in:register,reset_password'],
            'send_via' => ['required', 'in:email,sms,wa'],
        ]);

        $user = $request->user();

        // Generate OTP 6 digit
        $otp = (string) random_int(100000, 999999);

        // Invalidasi OTP lama yang masih active
        Verification::where('user_id', $user->id)
            ->where('type', $request->type)
            ->where('status', 'active')
            ->update(['status' => 'invalid']);

        // Simpan OTP baru (di-hash)
        Verification::create([
            'user_id'    => $user->id,
            'unique_id'  => (string) Str::uuid(),
            'otp'        => Hash::make($otp),
            'type'       => $request->type,
            'send_via'   => $request->send_via,
            'resend'     => 0,
            'attempts'   => 0,
            'status'     => 'active',
            'expires_at' => now()->addMinutes(5),
        ]);

        // Kirim via email (sementara hanya email)
        try {
            Mail::to($user->email)->send(new OtpEmail($otp, $request->type));
            Log::info("OTP {$request->type} dikirim ke: {$user->email}, OTP: {$otp}");
        } catch (\Exception $e) {
            Log::error("Gagal kirim OTP: " . $e->getMessage());
            return back()->with('failed', 'Gagal mengirim email. Coba lagi nanti.');
        }

        return back()
            ->with('success', 'Kode OTP berhasil dikirim. Cek email kamu (termasuk folder spam).')
            ->with('otp_sent', true);
    }

    /**
     * Verifikasi OTP
     */
    public function check(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:register,reset_password'],
            'otp'  => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        $record = Verification::where('user_id', $user->id)
            ->where('type', $request->type)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$record) {
            return back()
                ->withErrors(['otp' => 'OTP belum dikirim atau sudah tidak aktif.'])
                ->with('otp_sent', true);
        }

        // Cek expired
        if (now()->greaterThan($record->expires_at)) {
            $record->update(['status' => 'invalid']);
            return back()
                ->withErrors(['otp' => 'OTP sudah kadaluarsa. Silakan kirim ulang.'])
                ->with('otp_sent', true);
        }

        // Increment attempts
        $record->increment('attempts');

        // Cek max attempts
        if ($record->attempts > 5) {
            $record->update(['status' => 'invalid']);
            return back()
                ->withErrors(['otp' => 'Terlalu banyak percobaan salah. Kirim ulang OTP.'])
                ->with('otp_sent', false);
        }

        // Verifikasi OTP
        if (!Hash::check($request->otp, $record->otp)) {
            return back()
                ->withErrors(['otp' => 'Kode OTP salah. Sisa percobaan: ' . (5 - $record->attempts)])
                ->with('otp_sent', true);
        }

        // OTP benar - update status
        $record->update([
            'status'  => 'used',
            'used_at' => now(),
        ]);

        // Update user status jadi active
        $user->update(['status' => 'active']);

        return redirect()->route('pasien')->with('success', 'Email berhasil diverifikasi! Selamat datang di Klinik ZIP.');
    }
}