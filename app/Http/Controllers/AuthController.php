<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:50'],
            'password' => ['required', 'min:5', 'max:50'],
        ]);

        $remember = $request->boolean('remember');

        // Cek apakah email adalah pasien (pasien harus login via OTP)
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->role === 'patient') {
            return redirect()
                ->route('patient.login')
                ->withInput(['email' => $credentials['email']])
                ->with('failed', 'Pasien harus login menggunakan OTP. Silakan masukkan email untuk menerima kode OTP.');
        }

        // Attempt login untuk admin/dokter
        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->with('failed', 'Email atau password salah.');
        }

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Redirect berdasarkan role (without intended to avoid wrong redirects)
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.index');
        } elseif ($user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->role === 'nurse') {
            return redirect()->route('nurse.index');
        } elseif ($user->role === 'patient') {
            return redirect()->route('patient.dashboard');
        }

        // Fallback: logout user dengan role tidak valid
        Auth::logout();
        return redirect()->route('login')->with('failed', 'Role user tidak valid.');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:50'],
            'email'    => ['required', 'email', 'max:50', 'unique:users,email'],
            'password' => ['required', 'min:5', 'max:50', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'patient',   // default register = pasien
            'status'   => 'verify',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verify');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}