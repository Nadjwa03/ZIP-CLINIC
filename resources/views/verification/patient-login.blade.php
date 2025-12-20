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
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-100 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        
        {{-- ==================== HEADER ==================== --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-8 text-white text-center">
            <div class="w-20 h-20 bg-white rounded-full mx-auto mb-4 flex items-center justify-center">
                @if(($state ?? 'email') === 'register')
                    {{-- Icon Register --}}
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                @elseif(($state ?? 'email') === 'otp')
                    {{-- Icon OTP --}}
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                @else
                    {{-- Icon Default/Email --}}
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                @endif
            </div>
            
            @if(($state ?? 'email') === 'register')
                <h2 class="text-2xl font-bold mb-2">Registrasi Pasien</h2>
                <p class="text-emerald-100">Lengkapi data diri untuk membuat akun</p>
            @elseif(($state ?? 'email') === 'otp')
                <h2 class="text-2xl font-bold mb-2">Verifikasi OTP</h2>
                <p class="text-emerald-100">Masukkan kode yang dikirim ke</p>
                <p class="text-white font-semibold text-sm mt-1">{{ $email ?? '' }}</p>
            @else
                <h2 class="text-2xl font-bold mb-2">Selamat Datang</h2>
                <p class="text-emerald-100">Masukkan email untuk Login atau Registrasi</p>
            @endif
        </div>

        {{-- ==================== BODY ==================== --}}
        <div class="p-8">
            
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-700 text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-blue-700 text-sm">{{ session('info') }}</span>
                </div>
            @endif

            @if (session('failed'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-700 text-sm">{{ session('failed') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-700 list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ==================== STATE: EMAIL (Default) ==================== --}}
            @if(($state ?? 'email') === 'email')
                <form method="POST" action="{{ route('patient.check-email') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   placeholder="nama@email.com">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Jika email belum terdaftar, kamu akan diarahkan untuk registrasi.
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 px-4 rounded-lg font-medium hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform transition hover:scale-[1.02] active:scale-[0.98]">
                        Login / Register
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">Dengan melanjutkan, kamu menyetujui</p>
                    <a href="#" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">
                        Syarat dan Ketentuan serta Kebijakan Privasi
                    </a>
                </div>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Admin/Dokter?
                    <a href="{{ route('login') }}" class="font-medium text-emerald-600 hover:text-emerald-500">Login dengan password</a>
                </p>

            {{-- ==================== STATE: REGISTER ==================== --}}
            @elseif(($state ?? 'email') === 'register')
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">Nomor telepon/email yang kamu masukkan belum terdaftar. Silakan lengkapi data untuk registrasi.</p>
                </div>

                <form method="POST" action="{{ route('patient.register') }}" class="space-y-5">
                    @csrf

                    {{-- Phone Number --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <div class="relative flex">
                            <div class="flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg">
                                <span class="text-lg mr-2">🇮🇩</span>
                                <span class="text-gray-600 text-sm">+62</span>
                            </div>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   placeholder="812-3456-7890">
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   placeholder="Nama lengkap">
                        </div>
                    </div>

                    {{-- Email (readonly from session) --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 px-4 rounded-lg font-medium hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform transition hover:scale-[1.02] active:scale-[0.98]">
                        Konfirmasi & Kirim OTP
                    </button>
                </form>

                {{-- Ganti Email --}}
                <form method="POST" action="{{ route('patient.cancel') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-700">
                        ← Gunakan email lain
                    </button>
                </form>

            {{-- ==================== STATE: OTP ==================== --}}
            @elseif(($state ?? 'email') === 'otp')
                <form method="POST" action="{{ route('patient.verify-otp') }}" class="space-y-5" id="otp-form">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Masukkan Kode OTP (6 digit)</label>
                        
                        {{-- OTP Input Boxes --}}
                        <div class="flex justify-center gap-2 mb-4">
                            @for ($i = 1; $i <= 6; $i++)
                                <input type="text" 
                                       id="otp-{{ $i }}" 
                                       maxlength="1" 
                                       inputmode="numeric"
                                       pattern="[0-9]"
                                       class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                       required>
                            @endfor
                        </div>
                        
                        {{-- Hidden input untuk submit --}}
                        <input type="hidden" name="otp" id="otp-hidden">
                        
                        <p class="text-xs text-gray-500 text-center">Cek inbox atau folder spam di email kamu.</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-3 px-4 rounded-lg font-medium hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform transition hover:scale-[1.02] active:scale-[0.98]">
                        Verifikasi
                    </button>
                </form>

                {{-- Kirim Ulang & Ganti Email --}}
                <div class="mt-6 flex items-center justify-between">
                    <form method="POST" action="{{ route('patient.resend-otp') }}">
                        @csrf
                        <button type="submit" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            Kirim ulang OTP
                        </button>
                    </form>

                    <form method="POST" action="{{ route('patient.cancel') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                            Ganti email
                        </button>
                    </form>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500">OTP berlaku selama <span class="font-semibold text-gray-700">5 menit</span></p>
                </div>
            @endif

        </div>
    </div>

    <p class="mt-6 text-center text-sm text-gray-600">© {{ date('Y') }} Klinik ZIP. All rights reserved.</p>
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
            
            // Auto submit when all filled
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

    // Update hidden input before submit
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
        }
    });
});
</script>
@endif

</body>
</html>