<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-brown-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
            6
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Konfirmasi & Keluhan</h3>
            <p class="text-sm text-gray-600">Periksa kembali detail appointment Anda</p>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="bg-gray-50 rounded-lg p-6 mb-6 space-y-4">
        
        {{-- Patient Info --}}
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-500 uppercase mb-1">Pasien</p>
                <p class="font-semibold text-gray-900">{{ $this->selectedPatient->full_name ?? '-' }}</p>
                <p class="text-sm text-gray-600">
                    {{ $this->selectedPatient->relationship_label ?? '-' }} • 
                    {{ $this->selectedPatient->age ?? '-' }} tahun
                </p>
            </div>
            <button 
                wire:click="goToStep(1)"
                class="text-brown-600 hover:text-brown-700 text-sm">
                Edit
            </button>
        </div>

        <div class="border-t border-gray-200"></div>

        {{-- Service Info --}}
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-500 uppercase mb-1">Layanan</p>
                <p class="font-semibold text-gray-900">{{ $this->selectedService->service_name ?? '-' }}</p>
                <div class="flex items-center gap-3 text-sm text-gray-600 mt-1">
                    <span>{{ $this->selectedService->formatted_price ?? '-' }}</span>
                    <span>•</span>
                    <span>{{ $this->selectedService->formatted_duration ?? '-' }}</span>
                </div>
            </div>
            <button 
                wire:click="goToStep(2)"
                class="text-brown-600 hover:text-brown-700 text-sm">
                Edit
            </button>
        </div>

        <div class="border-t border-gray-200"></div>

        {{-- Doctor Info --}}
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-500 uppercase mb-1">Dokter</p>
                @if($this->selectedDoctor)
                    <p class="font-semibold text-gray-900">{{ $this->selectedDoctor->user->name ?? 'Dokter' }}</p>
                    @if($this->selectedDoctor->speciality)
                        <p class="text-sm text-gray-600">{{ $this->selectedDoctor->speciality->speciality_name }}</p>
                    @endif
                @else
                    <p class="text-gray-600 italic">Akan ditentukan oleh admin</p>
                @endif
            </div>
            <button 
                wire:click="goToStep(3)"
                class="text-brown-600 hover:text-brown-700 text-sm">
                Edit
            </button>
        </div>

        <div class="border-t border-gray-200"></div>

        {{-- Date & Time Info --}}
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-xs text-gray-500 uppercase mb-1">Tanggal & Waktu</p>
                <p class="font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                </p>
                <p class="text-sm text-gray-600">
                    Pukul {{ $selectedTime }}
                    @if($this->selectedService)
                        - {{ \Carbon\Carbon::parse($selectedTime)->addMinutes($this->selectedService->duration_minutes)->format('H:i') }} WIB
                    @endif
                </p>
            </div>
            <button 
                wire:click="goToStep(4)"
                class="text-brown-600 hover:text-brown-700 text-sm">
                Edit
            </button>
        </div>
    </div>

    {{-- Complaint / Notes --}}
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Keluhan / Catatan (Opsional)
        </label>
        <textarea 
            wire:model="complaint"
            rows="4"
            placeholder="Jelaskan keluhan atau catatan khusus Anda di sini..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brown-600 focus:border-transparent"
            maxlength="1000"></textarea>
        <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
    </div>

    {{-- Terms & Conditions --}}
    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <p class="text-sm text-yellow-800 mb-2">
            <strong>⚠️ Perhatian:</strong>
        </p>
        <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
            <li>Harap datang 15 menit sebelum jadwal appointment</li>
            <li>Bawa kartu identitas dan kartu BPJS (jika ada)</li>
            <li>Jika berhalangan hadir, mohon hubungi klinik minimal 4 jam sebelumnya</li>
            <li>Harga yang tertera adalah estimasi, harga final sesuai pemeriksaan dokter</li>
        </ul>
    </div>

    {{-- Submit Button --}}
    <button 
        wire:click="submit"
        wire:loading.attr="disabled"
        class="w-full py-4 bg-brown-600 text-white font-semibold rounded-lg hover:bg-brown-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed">
        
        <span wire:loading.remove wire:target="submit">
            Buat Appointment
        </span>
        
        <span wire:loading wire:target="submit" class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        </span>
    </button>

    {{-- Validation Errors --}}
    @error('patientId')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('serviceId')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('selectedDate')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('selectedTime')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    {{-- Help Text --}}
    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            <strong>💡 Info:</strong> Setelah appointment dibuat, Anda akan menerima notifikasi konfirmasi. Status appointment dapat dilihat di halaman "Daftar Appointment".
        </p>
    </div>
</div>
