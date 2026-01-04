<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pasien - Klinik ZIP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .otp-input[type=number] {
            -moz-appearance: textfield;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #6B4423;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #5A3A1E;
        }
    </style>
</head>
<body class="bg-white min-h-screen">

<div class="flex min-h-screen">
    
    {{-- ==================== LEFT SIDE: ILLUSTRATION (Hidden on mobile) ==================== --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] relative overflow-hidden">
        {{-- Pattern Background --}}
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="smallGrid" width="20" height="20" patternUnits="userSpaceOnUse">
                        <path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#smallGrid)" />
            </svg>
        </div>
        
        {{-- Content --}}
        <div class="relative z-10 flex flex-col items-center justify-center w-full p-12 text-white">
            
            {{-- Logo --}}
            <a href="/" class="absolute top-8 left-8 flex items-center space-x-3 hover:opacity-80 transition-opacity">
                <div class="text-4xl">🦷</div>
                <span class="font-bold text-2xl">Klinik ZIP</span>
            </a>
            
            {{-- Illustration --}}
            <div class="w-full max-w-md mb-8">
                <img src="{{ asset('images/login-register_pasien.svg') }}" 
                     alt="Patient Login Illustration" 
                     class="w-full h-auto drop-shadow-2xl"
                     onerror="this.style.display='none'">
            </div>
            
            {{-- Text Content --}}
            <div class="text-center max-w-md">
                <h1 class="text-3xl font-bold mb-4">Selamat Datang di Klinik ZIP</h1>
                <p class="text-lg text-gray-200 leading-relaxed">
                    Sistem booking appointment dan medical records yang mudah dan aman untuk pasien.
                </p>
                
                {{-- Features List --}}
                <div class="mt-8 space-y-3 text-left">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 mr-3 flex-shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-200">Booking appointment online 24/7</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-6 h-6 mr-3 flex-shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-200">Akses medical records Anda</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-6 h-6 mr-3 flex-shrink-0 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-200">Riwayat treatment lengkap</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== RIGHT SIDE: FORM ==================== --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 bg-gray-50">
        <div class="w-full max-w-md">
            
            {{-- Mobile Logo --}}
            <a href="/" class="flex lg:hidden items-center justify-center space-x-3 mb-8 hover:opacity-80 transition-opacity">
                <div class="text-4xl">🦷</div>
                <span class="font-bold text-2xl text-[#6B4423]">Klinik ZIP</span>
            </a>
            
            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                
                {{-- Header --}}
                <div class="text-center mb-8">
                    @if(($state ?? 'email') === 'register')
                        <div class="w-16 h-16 bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] rounded-2xl mx-auto mb-4 flex items-center justify-center transform rotate-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Registrasi Akun</h2>
                        <p class="text-gray-600">Lengkapi data diri untuk membuat akun baru</p>
                    @elseif(($state ?? 'email') === 'otp')
                        <div class="w-16 h-16 bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] rounded-2xl mx-auto mb-4 flex items-center justify-center transform -rotate-3">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Verifikasi OTP</h2>
                        <p class="text-gray-600">Kode verifikasi telah dikirim ke</p>
                        <p class="text-[#6B4423] font-semibold mt-1">{{ $email ?? '' }}</p>
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] rounded-2xl mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Login Pasien</h2>
                        <p class="text-gray-600">Masukkan email untuk login atau registrasi</p>
                    @endif
                </div>

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-green-700 text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- @if (session('info'))
                    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-blue-700 text-sm">{{ session('info') }}</span>
                        </div>
                    </div>
                @endif -->

                @if (session('failed'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-red-700 text-sm">{{ session('failed') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $err)
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>{{ $err }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ==================== STATE: EMAIL (Default) ==================== --}}
                @if(($state ?? 'email') === 'email')
                    <form method="POST" action="{{ route('patient.check-email') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6B4423] focus:border-[#6B4423] transition-all duration-200"
                                       placeholder="nama@email.com">
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Email belum terdaftar? Kami akan arahkan Anda untuk registrasi.
                            </p>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] text-white py-3.5 px-4 rounded-xl font-semibold hover:from-[#5A3A1E] hover:to-[#4A2F18] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6B4423] transform transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                            Lanjutkan
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-500 mb-2">Dengan melanjutkan, Anda menyetujui</p>
                        <a href="#" class="text-sm font-medium text-[#6B4423] hover:text-[#5A3A1E] transition-colors">
                            Syarat & Ketentuan
                        </a>
                        <span class="text-gray-400 mx-2">•</span>
                        <a href="#" class="text-sm font-medium text-[#6B4423] hover:text-[#5A3A1E] transition-colors">
                            Kebijakan Privasi
                        </a>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-600">
                            Admin atau Dokter?
                            <a href="{{ route('login') }}" class="font-semibold text-[#6B4423] hover:text-[#5A3A1E] transition-colors ml-1">
                                Login dengan password →
                            </a>
                        </p>
                    </div>

                {{-- ==================== STATE: REGISTER ==================== --}}
                @elseif(($state ?? 'email') === 'register')
                    <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
                        <p class="text-sm text-amber-800">
                            <span class="font-semibold">Email belum terdaftar.</span> Silakan lengkapi data untuk membuat akun baru.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('patient.register') }}" class="space-y-5">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                       class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6B4423] focus:border-[#6B4423] transition-all"
                                       placeholder="Masukkan nama lengkap">
                            </div>
                        </div>

                        {{-- Phone Number --}}
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                            <div class="flex">
                                <div class="flex items-center px-4 bg-gray-100 border-2 border-r-0 border-gray-200 rounded-l-xl">
                                    <span class="text-xl mr-2">🇮🇩</span>
                                    <span class="text-gray-600 text-sm font-medium">+62</span>
                                </div>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                       class="block w-full px-4 py-3.5 border-2 border-gray-200 rounded-r-xl focus:ring-2 focus:ring-[#6B4423] focus:border-[#6B4423] transition-all"
                                       placeholder="812-3456-7890">
                            </div>
                        </div>

                        {{-- Email (readonly) --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                                       class="block w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600">
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] text-white py-3.5 px-4 rounded-xl font-semibold hover:from-[#5A3A1E] hover:to-[#4A2F18] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6B4423] transform transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                            Daftar & Kirim OTP
                        </button>
                    </form>

                    {{-- Cancel Button --}}
                    <form method="POST" action="{{ route('patient.cancel') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full text-center text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors py-2">
                            ← Gunakan email lain
                        </button>
                    </form>

                {{-- ==================== STATE: OTP ==================== --}}
                @elseif(($state ?? 'email') === 'otp')
                    <form method="POST" action="{{ route('patient.verify-otp') }}" class="space-y-6" id="otp-form">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-4 text-center">Masukkan Kode OTP (6 digit)</label>
                            
                            {{-- OTP Input Boxes --}}
                            <div class="flex justify-center gap-3 mb-4">
                                @for ($i = 1; $i <= 6; $i++)
                                    <input type="text" 
                                           id="otp-{{ $i }}" 
                                           maxlength="1" 
                                           inputmode="numeric"
                                           pattern="[0-9]"
                                           class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-[#6B4423] focus:border-[#6B4423] transition-all"
                                           required>
                                @endfor
                            </div>
                            
                            {{-- Hidden input for submit --}}
                            <input type="hidden" name="otp" id="otp-hidden">
                            
                            <p class="text-xs text-center text-gray-500">
                                Periksa inbox atau folder spam email Anda
                            </p>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] text-white py-3.5 px-4 rounded-xl font-semibold hover:from-[#5A3A1E] hover:to-[#4A2F18] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6B4423] transform transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl">
                            Verifikasi Sekarang
                        </button>
                    </form>

                    {{-- Resend & Change Email --}}
                    <div class="mt-6 flex items-center justify-between">
                        <form method="POST" action="{{ route('patient.resend-otp') }}">
                            @csrf
                            <button type="submit" class="text-sm text-[#6B4423] hover:text-[#5A3A1E] font-semibold transition-colors">
                                Kirim ulang OTP
                            </button>
                        </form>

                        <form method="POST" action="{{ route('patient.cancel') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-gray-800 font-medium transition-colors">
                                Ganti email
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 p-3 bg-amber-50 rounded-lg text-center">
                        <p class="text-xs text-amber-800">
                            Kode OTP berlaku selama <span class="font-bold">5 menit</span>
                        </p>
                    </div>
                @endif

            </div>
            
            {{-- Footer --}}
            <p class="mt-6 text-center text-sm text-gray-500">
                © {{ date('Y') }} Klinik ZIP. All rights reserved.
            </p>
        </div>
    </div>

</div>

{{-- OTP Input JavaScript --}}
@if(($state ?? 'email') === 'otp')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-input');
    const form = document.getElementById('otp-form');
    const hiddenInput = document.getElementById('otp-hidden');

    // Focus first input
    inputs[0].focus();

    inputs.forEach((input, index) => {
        // Only allow numbers
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            
            // Update hidden input
            updateHiddenInput();
        });

        // Handle backspace
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            
            pastedData.split('').forEach((char, i) => {
                if (inputs[i]) {
                    inputs[i].value = char;
                }
            });
            
            // Focus last filled or next empty
            const lastIndex = Math.min(pastedData.length, inputs.length) - 1;
            if (lastIndex >= 0) {
                inputs[lastIndex].focus();
            }
            
            updateHiddenInput();
        });
    });

    // Update hidden input
    function updateHiddenInput() {
        let otp = '';
        inputs.forEach(input => otp += input.value);
        hiddenInput.value = otp;
    }

    // Validate before submit
    form.addEventListener('submit', function(e) {
        updateHiddenInput();
        if (hiddenInput.value.length !== 6) {
            e.preventDefault();
            alert('Masukkan 6 digit kode OTP');
            inputs[0].focus();
        }
    });
});
</script>
@endif

</body>
</html>