<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Klinik ZIP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

@php
    // kalau OTP sudah dikirim (dari controller), otomatis tampilkan section input OTP
    $otpSent = session('otp_sent') ? true : false;

    // default metode: email
    $defaultSendVia = old('send_via', 'email');
@endphp

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white text-center">
                <div class="w-20 h-20 bg-white rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-2">Verifikasi Akun</h2>
                <p class="text-blue-100">Masukkan kode OTP untuk verifikasi</p>
            </div>

            <div class="p-8">

                {{-- Flash message Laravel --}}
                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('failed'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                        {{ session('failed') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                        <ul class="text-sm text-red-700 list-disc pl-5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Info email user --}}
                <div class="mb-6 bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center justify-center space-x-2 text-sm text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>
                            Verifikasi untuk: <span class="font-semibold">{{ auth()->user()->email ?? '-' }}</span>
                        </span>
                    </div>
                </div>

                {{-- =======================
                     FORM KIRIM OTP
                     ======================= --}}
                <div id="send-section" class="{{ $otpSent ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Pilih Metode Verifikasi
                    </label>

                    <div class="space-y-3 mb-6">
                        {{-- Email --}}
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="contact_method" value="email"
                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                   {{ $defaultSendVia === 'email' ? 'checked' : '' }}>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">Email</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1 ml-7">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                            </div>
                        </label>

                        {{-- SMS (opsional) --}}
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors opacity-60">
                            <input type="radio" name="contact_method" value="sms"
                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                   {{ $defaultSendVia === 'sms' ? 'checked' : '' }}
                                   disabled>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900">SMS (Soon)</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1 ml-7">Belum diaktifkan</p>
                            </div>
                        </label>
                    </div>

                    <form method="POST" action="{{ route('verify.send') }}" id="send-otp-form">
                        @csrf
                        {{-- type = register / reset_password (kamu pakai enum ini di DB) --}}
                        <input type="hidden" name="type" value="register">

                        {{-- metode kirim: email/sms/wa --}}
                        <input type="hidden" name="send_via" id="send_via" value="{{ $defaultSendVia }}">

                        <button type="submit" id="send-otp-btn"
                                class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white py-3 px-4 rounded-lg font-medium hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform transition duration-150 hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kirim Kode OTP
                        </button>
                    </form>
                </div>

                {{-- =======================
                     SECTION INPUT OTP
                     ======================= --}}
                <div id="otp-section" class="{{ $otpSent ? '' : 'hidden' }} mt-6">
                    <div class="mb-4 bg-blue-50 rounded-lg p-4 text-sm text-blue-700 text-center">
                        Kode OTP sudah dikirim ke email kamu. Silakan cek Inbox / Spam.
                    </div>

                    <form method="POST" action="{{ route('verify.check') }}" class="space-y-6" onsubmit="return submitOtpToHiddenInput()">
                        @csrf
                        <input type="hidden" name="type" value="register">

                        {{-- OTP gabungan dari 6 box --}}
                        <input type="hidden" name="otp" id="otp-hidden">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Masukkan Kode OTP (6 Digit)
                            </label>

                            <div class="flex justify-center space-x-2 mb-4">
                                <input type="text" maxlength="1" id="otp-1" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                                <input type="text" maxlength="1" id="otp-2" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                                <input type="text" maxlength="1" id="otp-3" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                                <span class="flex items-center text-2xl font-bold text-gray-400">-</span>
                                <input type="text" maxlength="1" id="otp-4" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                                <input type="text" maxlength="1" id="otp-5" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                                <input type="text" maxlength="1" id="otp-6" class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                            </div>

                            <div class="text-center mb-4">
                                <p class="text-sm text-gray-600">
                                    Kode akan kadaluarsa dalam
                                    <span id="countdown-timer" class="font-semibold text-blue-600">05:00</span>
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition duration-150 hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verifikasi Sekarang
                        </button>
                    </form>

                    {{-- RESEND OTP --}}
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 text-center mb-2">Tidak menerima kode?</p>

                        <form method="POST" action="{{ route('verify.send') }}" onsubmit="return handleResendSubmit()">
                            @csrf
                            <input type="hidden" name="type" value="register">
                            <input type="hidden" name="send_via" value="email">

                            <button id="resend-otp-btn" type="submit" disabled
                                    class="w-full text-blue-600 hover:text-blue-700 font-medium py-2 text-sm disabled:text-gray-400 disabled:cursor-not-allowed">
                                <span id="resend-text">Kirim Ulang Kode (<span id="resend-countdown">60</span>s)</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Help -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-gray-600">
                                <p class="font-medium text-gray-800 mb-1">Tips Verifikasi:</p>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li>Cek folder spam jika pakai email</li>
                                    <li>Kode OTP berlaku 5 menit</li>
                                    <li>Gunakan kode terbaru jika kirim ulang</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back to Login -->
                <p class="mt-6 text-center text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition duration-150">
                        ← Kembali ke Login
                    </a>
                </p>

            </div>
        </div>

        <p class="mt-6 text-center text-sm text-gray-600">
            © 2024 Klinik ZIP. All rights reserved.
        </p>
    </div>

    <script>
        // === Sync metode kirim OTP (radio -> hidden input)
        document.querySelectorAll('input[name="contact_method"]').forEach(el => {
            el.addEventListener('change', () => {
                const hidden = document.getElementById('send_via');
                if (hidden) hidden.value = el.value; // email/sms/wa
            });
        });

        // === Gabungkan 6 box otp ke input hidden 'otp'
        function submitOtpToHiddenInput() {
            let otp = '';
            for (let i = 1; i <= 6; i++) {
                otp += (document.getElementById(`otp-${i}`)?.value || '');
            }
            document.getElementById('otp-hidden').value = otp;
            return true;
        }

        // === OTP input auto focus & numeric only
        document.addEventListener('DOMContentLoaded', function() {
            const otpInputs = document.querySelectorAll('.otp-input');

            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowLeft' && index > 0) otpInputs[index - 1].focus();
                    if (e.key === 'ArrowRight' && index < otpInputs.length - 1) otpInputs[index + 1].focus();
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                    for (let i = 0; i < Math.min(pasted.length, 6); i++) {
                        otpInputs[i].value = pasted[i];
                    }
                    const lastIndex = Math.min(pasted.length, 6) - 1;
                    if (lastIndex >= 0) otpInputs[lastIndex].focus();
                });
            });

            // kalau section otp tampil, fokus ke otp-1
            const otpSection = document.getElementById('otp-section');
            if (otpSection && !otpSection.classList.contains('hidden')) {
                document.getElementById('otp-1')?.focus();
                startOTPCountdown();
                startResendCountdown();
            }
        });

        // === countdown OTP (5 menit)
        let otpCountdownInterval;
        let otpExpiryTime = 300; // 5 menit

        function startOTPCountdown() {
            const countdownTimer = document.getElementById('countdown-timer');
            if (!countdownTimer) return;

            if (otpCountdownInterval) clearInterval(otpCountdownInterval);

            otpCountdownInterval = setInterval(() => {
                otpExpiryTime--;
                const minutes = Math.floor(otpExpiryTime / 60);
                const seconds = otpExpiryTime % 60;
                countdownTimer.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

                if (otpExpiryTime <= 0) {
                    clearInterval(otpCountdownInterval);
                    countdownTimer.textContent = 'Kadaluarsa';
                    countdownTimer.classList.remove('text-blue-600');
                    countdownTimer.classList.add('text-red-600');
                }
            }, 1000);
        }

        // === resend countdown (60 detik)
        let resendCountdownInterval;
        let resendWaitTime = 60;

        function startResendCountdown() {
            const resendBtn = document.getElementById('resend-otp-btn');
            const resendCountdown = document.getElementById('resend-countdown');
            const resendText = document.getElementById('resend-text');

            if (!resendBtn || !resendCountdown || !resendText) return;

            let timeLeft = resendWaitTime;
            resendBtn.disabled = true;

            if (resendCountdownInterval) clearInterval(resendCountdownInterval);

            resendCountdownInterval = setInterval(() => {
                timeLeft--;
                resendCountdown.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(resendCountdownInterval);
                    resendBtn.disabled = false;
                    resendText.textContent = 'Kirim Ulang Kode';
                }
            }, 1000);
        }

        // biar kalau klik resend, tombol disable dulu (anti spam)
        function handleResendSubmit() {
            const resendBtn = document.getElementById('resend-otp-btn');
            if (resendBtn) resendBtn.disabled = true;
            return true;
        }
    </script>

</body>
</html>
