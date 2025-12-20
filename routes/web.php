<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientOtpLoginController;


Route::get('/', fn () => view('welcome'));


//=======GUEST & AUTHENTICATED ROUTES =======//
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    // Login/Register pasien via OTP
    Route::get('/patient/login', [PatientOtpLoginController::class, 'showLoginForm'])->name('patient.login');
    Route::post('/patient/check-email', [PatientOtpLoginController::class, 'checkEmail'])->name('patient.check-email');
    Route::post('/patient/register', [PatientOtpLoginController::class, 'register'])->name('patient.register');
    Route::post('/patient/verify-otp', [PatientOtpLoginController::class, 'verifyOtp'])->name('patient.verify-otp');
    Route::post('/patient/resend-otp', [PatientOtpLoginController::class, 'resendOtp'])->name('patient.resend-otp');
    Route::post('/patient/cancel', [PatientOtpLoginController::class, 'cancel'])->name('patient.cancel');
});



Route::middleware('auth')->group(function () {

     // OTP verify page
    Route::get('/verify', [VerificationController::class, 'index'])->name('verify');

    // send otp
    Route::post('/verify/send', [VerificationController::class, 'send'])->name('verify.send');

    // check otp
    Route::post('/verify/check', [VerificationController::class, 'check'])->name('verify.check');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // pasien area (wajib verified)
    Route::get('/pasien', fn () => view('pasien.index'))
        ->middleware(['check_role:patient','check_status'])
        ->name('pasien');

    // dashboard admin/dokter
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('check_role:admin,doctor')
        ->name('dashboard');

});

Route::middleware(['auth', 'check_role:admin'])->group(function () {
    Route::get('/admin/pasien', fn () => 'halaman pasien (admin)');
});