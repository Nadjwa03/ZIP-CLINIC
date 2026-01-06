<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PatientOtpLoginController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ========================================
// ADMIN PANEL CONTROLLERS
// ========================================
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AppointmentController; 

// ========================================
// PATIENT AREA CONTROLLERS (UPDATED!)
// ========================================
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\PatientManagementController;
use App\Http\Controllers\Patient\MedicalRecordController;
use App\Http\Controllers\Patient\TransactionController;
use App\Http\Controllers\Patient\VoucherController;
use App\Http\Controllers\Patient\ProfileController;

// ========================================
// LANDING PAGE (PUBLIC)
// ========================================
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/services', [LandingController::class, 'services'])->name('landing.services');
Route::get('/service/{id}', [LandingController::class, 'serviceDetail'])->name('landing.service.detail');
Route::get('/doctors', [LandingController::class, 'doctors'])->name('landing.doctors');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');
Route::post('/contact', [LandingController::class, 'contactSubmit'])->name('landing.contact.submit');

// ========================================
// GUEST ROUTES (Not Logged In)
// ========================================
Route::middleware('guest')->group(function () {
    // Standard Login/Register
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    // Patient Login via OTP
    Route::get('/patient/login', [PatientOtpLoginController::class, 'showLoginForm'])->name('patient.login');
    Route::post('/patient/check-email', [PatientOtpLoginController::class, 'checkEmail'])->name('patient.check-email');
    Route::post('/patient/register', [PatientOtpLoginController::class, 'register'])->name('patient.register');
    Route::post('/patient/verify-otp', [PatientOtpLoginController::class, 'verifyOtp'])->name('patient.verify-otp');
    Route::post('/patient/resend-otp', [PatientOtpLoginController::class, 'resendOtp'])->name('patient.resend-otp');
    Route::post('/patient/cancel', [PatientOtpLoginController::class, 'cancel'])->name('patient.cancel');
});

// ========================================
// AUTHENTICATED ROUTES
// ========================================
Route::middleware('auth')->group(function () {
    // OTP Verification
    Route::get('/verify', [VerificationController::class, 'index'])->name('verify');
    Route::post('/verify/send', [VerificationController::class, 'send'])->name('verify.send');
    Route::post('/verify/check', [VerificationController::class, 'check'])->name('verify.check');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========================================
    // PATIENT AREA (UPDATED WITH SELF-SERVICE!)
    // ========================================
    Route::prefix('pasien')->name('patient.')->middleware(['check_role:patient', 'check_status'])->group(function () {

        // Dashboard
        Route::get('/', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::post('/switch-patient', [PatientDashboardController::class, 'switchPatient'])->name('switch-patient');

        // Claim Patient Record
        Route::get('/claim', [PatientDashboardController::class, 'showClaimForm'])->name('claim.form');
        Route::post('/claim', [PatientDashboardController::class, 'claimPatient'])->name('claim.submit');

        // Patient Management (Self-Service Registration)
        Route::get('patients', [PatientManagementController::class, 'index'])->name('patients.index');
        Route::get('patients/create', [PatientManagementController::class, 'create'])->name('patients.create');
        Route::post('patients', [PatientManagementController::class, 'store'])->name('patients.store');
        Route::get('patients/{patient}/edit', [PatientManagementController::class, 'edit'])->name('patients.edit');
        Route::put('patients/{patient}', [PatientManagementController::class, 'update'])->name('patients.update');
        
        // Appointments
        Route::get('appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('appointments/create', [PatientAppointmentController::class, 'create'])->name('appointments.create');
        Route::post('appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
        Route::get('appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
        Route::get('appointments/slots', [PatientAppointmentController::class, 'getSlots'])->name('appointments.slots');
        Route::post('appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
        // Route::get('/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('available-slots');
        // Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        // Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
        // Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        // Route::patch('/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('update-status');
        // Route::delete('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        // Medical Records
        Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
        Route::get('medical-records/{id}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
        Route::get('medical-records/{id}/download', [MedicalRecordController::class, 'download'])->name('medical-records.download');
        
        // Transactions / Invoices
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('transactions/{id}/download', [TransactionController::class, 'download'])->name('transactions.download');
        
        // claim Patient Profile
        Route::get('patients/claim', [PatientManagementController::class, 'showClaimForm'])->name('patients.claim');
        Route::post('patients/claim', [PatientManagementController::class, 'claim'])->name('patients.claim.store');
        
        // Clinic Location
        Route::get('clinic-location', function() {
            $settings = \App\Models\AppSetting::query()->get()->pluck('value', 'key')->toArray();
            return view('pasien.clinic-location', compact('settings'));
        })->name('clinic-location');
        
        // Profile / Settings
        Route::get('settings', [ProfileController::class, 'index'])->name('settings');
        Route::put('settings', [ProfileController::class, 'update'])->name('settings.update');
        Route::put('settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password');
        Route::post('settings/photo', [ProfileController::class, 'updatePhoto'])->name('settings.photo');
    });

    // Dashboard (Admin/Doctor) - Your existing dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('check_role:admin,doctor')
        ->name('dashboard');
});

// ========================================
// ADMIN PANEL ROUTES
// ========================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check_role:admin'])->group(function () {
    
    // Admin Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
    
    // Logout from admin panel
    Route::get('/logout', function() {
        Auth::logout();
        return redirect('/login');
    })->name('logout');
    
    // ========================================
    // ADMIN APPOINTMENT ROUTES
    // ========================================
    // Tambahkan di dalam Route::prefix('admin')->group()
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AppointmentController::class, 'create'])->name('create');

        // AJAX Routes - Must be before {appointment} routes
        Route::get('/available-slots', [App\Http\Controllers\Admin\AppointmentController::class, 'getAvailableSlots'])->name('available-slots');

        Route::post('/', [App\Http\Controllers\Admin\AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [App\Http\Controllers\Admin\AppointmentController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [App\Http\Controllers\Admin\AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [App\Http\Controllers\Admin\AppointmentController::class, 'update'])->name('update');
        Route::patch('/{appointment}/status', [App\Http\Controllers\Admin\AppointmentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{appointment}/cancel', [App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('cancel');
    });
        // Check-in
        Route::get('/checkin', function () {
            return view('admin.checkin.index');
        })->name('checkin.index');

        // Queue Management
        Route::get('/queue', function () {
            return view('admin.queue.index');
        })->name('queue.index');

        // Inventory/Stocks
        Route::get('/inventory', function () {
            return view('admin.inventory.index');
        })->name('inventory.index');

    // ========================================
    // SERVICES MANAGEMENT (Master Data)
    // ========================================
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', function () {
            return view('admin.service.index');
        })->name('index');
        
        Route::get('/create', function () {
            return view('admin.service.form', ['serviceId' => null]);
        })->name('create');
        
        Route::get('/{id}/edit', function ($id) {
            return view('admin.service.form', ['serviceId' => $id]);
        })->name('edit');
    });


    // ========================================
    // DOCTORS MANAGEMENT (Master Data)
    // ========================================
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', function () {
            return view('admin.doctor.index');
        })->name('index');

        Route::get('/create', function () {
            return view('admin.doctor.form', ['doctorId' => null]);
        })->name('create');

        Route::get('/{id}', function ($id) {
            $doctor = \App\Models\Doctor::with(['user', 'speciality', 'schedules', 'appointments'])->findOrFail($id);
            return view('admin.doctor.show', ['doctor' => $doctor]);
        })->name('show');

        Route::get('/{id}/edit', function ($id) {
            return view('admin.doctor.form', ['doctorId' => $id]);
        })->name('edit');

        Route::get('/{id}/schedule', function ($id) {
            $doctor = \App\Models\Doctor::findOrFail($id);
            return view('admin.doctor.schedule', ['doctor' => $doctor]);
        })->name('schedule');
    });
    
    
    // ========================================
    // SETTINGS MANAGEMENT
    // ========================================
     // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
    });

    // ========================================
    // MASTER DATA: SPECIALITY
    // ========================================
    Route::prefix('speciality')->name('speciality.')->group(function () {
        Route::get('/', function () {
            return view('admin.speciality.index');
        })->name('index');

        Route::get('/create', function () {
            return view('admin.speciality.form', [
                'specialityId' => null,
                'title' => 'Tambah Spesialisasi'
            ]);
        })->name('create');

        Route::get('/{id}/edit', function ($id) {
            return view('admin.speciality.form', [
                'specialityId' => $id,
                'title' => 'Edit Spesialisasi'
            ]);
        })->name('edit');
    });
    // ========================================
    // ADMIN PATIENT MANAGEMENT
    // ========================================

    // ========================================
    // PATIENT MANAGEMENT (ADMIN) ← ADD THIS SECTION
    // ========================================

    Route::prefix('patients')->name('patients.')->group(function () {
        // ========================================
        // CHECK-IN (Livewire)
        // ========================================
        Route::get('/check-in', function () {
            return view('admin.checkin.index');
        })->name('checkin.index');

                // ========================================
        // PATIENT APPOINTMENT ROUTES
        // ========================================
        // Tambahkan di dalam Route::prefix('pasien')->group()
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [App\Http\Controllers\Patient\AppointmentController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Patient\AppointmentController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Patient\AppointmentController::class, 'store'])->name('store');
            Route::get('/{appointment}', [App\Http\Controllers\Patient\AppointmentController::class, 'show'])->name('show');
            Route::delete('/{appointment}/cancel', [App\Http\Controllers\Patient\AppointmentController::class, 'cancel'])->name('cancel');
            
            // AJAX Routes
            Route::get('/available-slots', [App\Http\Controllers\Patient\AppointmentController::class, 'getSlots'])->name('get-slots');
        });
        // ========================================
        // QUEUE MANAGEMENT (Livewire)
        // ========================================
        Route::get('/queue', function () {
            return view('admin.queue.index');
        })->name('queue.index');
        Route::get('/', [App\Http\Controllers\Admin\PatientController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\PatientController::class, 'create'])->name('create');  // ← NEW
        Route::post('/', [App\Http\Controllers\Admin\PatientController::class, 'store'])->name('store');  // ← NEW
        Route::get('/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [App\Http\Controllers\Admin\PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'update'])->name('update');
        Route::patch('/{patient}/toggle-status', [App\Http\Controllers\Admin\PatientController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{patient}/regenerate-code', [App\Http\Controllers\Admin\PatientController::class, 'regenerateSecretCode'])->name('regenerate-code');
        Route::delete('/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'destroy'])->name('destroy');
});
    Route::get('/pasien', fn () => 'halaman pasien (admin)')->name('pasien');
});