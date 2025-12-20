<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Pasien - OTP</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white text-center">
      <h2 class="text-2xl font-bold">Masukkan OTP</h2>
      <p class="text-blue-100 text-sm mt-1">Kode dikirim ke email kamu</p>
    </div>

    <div class="p-8">

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

      <div class="mb-6 bg-blue-50 rounded-lg p-4 text-sm text-blue-700 text-center">
        Email: <span class="font-semibold">{{ $email ?? request('email') }}</span>
      </div>

      <form method="POST" action="{{ route('patient.login.check') }}" onsubmit="return joinOtpToHidden()">
        @csrf

        <input type="hidden" name="email" value="{{ $email ?? request('email') }}">
        <input type="hidden" name="otp" id="otp-hidden">

        <label class="block text-sm font-medium text-gray-700 mb-2">Kode OTP (6 digit)</label>

        <div class="flex justify-center space-x-2 mb-6">
          @for ($i=1; $i<=6; $i++)
            <input type="text" inputmode="numeric" maxlength="1" id="otp-{{ $i }}"
              class="otp-input w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg
                     focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
            />
          @endfor
        </div>

        <button type="submit"
          class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-medium
                 hover:from-blue-700 hover:to-indigo-700">
          Verifikasi & Masuk
        </button>
      </form>

      <form method="POST" action="{{ route('patient.login.send') }}" class="mt-4">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? request('email') }}">
        <button type="submit" class="w-full text-blue-600 hover:text-blue-700 font-medium py-2 text-sm">
          Kirim ulang OTP
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('patient.login') }}" class="text-blue-600 font-medium hover:text-blue-500">
          ← Ganti email
        </a>
      </p>

    </div>
  </div>

<script>
  function joinOtpToHidden() {
    let otp = '';
    for (let i = 1; i <= 6; i++) otp += (document.getElementById('otp-'+i).value || '');
    document.getElementById('otp-hidden').value = otp;
    return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, idx) => {
      input.addEventListener('input', () => {
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value && idx < inputs.length - 1) inputs[idx + 1].focus();
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && idx > 0) inputs[idx - 1].focus();
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        pasted.split('').forEach((ch, i) => { if (inputs[i]) inputs[i].value = ch; });
        if (inputs[pasted.length - 1]) inputs[pasted.length - 1].focus();
      });
    });

    document.getElementById('otp-1')?.focus();
  });
</script>

</body>
</html>