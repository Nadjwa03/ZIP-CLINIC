<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-brown-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
            3
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Pilih Dokter</h3>
            <p class="text-sm text-gray-600">Opsional - Bisa dilewati jika belum yakin</p>
        </div>
    </div>

    {{-- Skip Option (Prominent) --}}
    <button 
        wire:click="skipDoctorSelection"
        type="button"
        class="w-full p-5 mb-6 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 hover:border-brown-300 transition text-center group">
        <div class="text-gray-700 font-medium mb-1 group-hover:text-brown-600">
            Tidak pilih dokter
        </div>
        <p class="text-sm text-gray-500">
            Admin akan pilihkan dokter yang tersedia untuk Anda
        </p>
    </button>

    {{-- Divider --}}
    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">atau pilih dokter</span>
        </div>
    </div>

    {{-- Doctor List --}}
    <div class="space-y-3 max-h-[400px] overflow-y-auto">
        @forelse($doctors as $doctor)
            <button 
                wire:click="selectDoctor({{ $doctor->doctor_user_id }})"
                type="button"
                class="w-full flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-brown-50 hover:border-brown-600 transition
                    {{ $doctorId == $doctor->doctor_user_id ? 'border-brown-600 bg-brown-50' : 'border-gray-200' }}">
                
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-full bg-brown-100 flex items-center justify-center font-semibold text-brown-600 text-xl mr-4">
                    {{ strtoupper(substr($doctor->user->name ?? 'D', 0, 1)) }}
                </div>
                
                {{-- Info --}}
                <div class="flex-1 text-left">
                    <p class="font-semibold text-gray-900">{{ $doctor->user->name ?? 'Dokter' }}</p>
                    @if($doctor->speciality)
                        <p class="text-sm text-gray-600">{{ $doctor->speciality->speciality_name }}</p>
                    @endif
                    @if($doctor->license_number)
                        <p class="text-xs text-gray-500">SIP: {{ $doctor->license_number }}</p>
                    @endif
                </div>

                {{-- Selected Indicator --}}
                @if($doctorId == $doctor->doctor_user_id)
                    <div class="text-brown-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif
            </button>
        @empty
            <div class="text-center py-8">
                <p class="text-gray-500">Tidak ada dokter yang tersedia untuk layanan ini</p>
                <button 
                    wire:click="skipDoctorSelection"
                    class="mt-3 text-brown-600 hover:underline">
                    Lewati langkah ini
                </button>
            </div>
        @endforelse
    </div>

    {{-- Help Text --}}
    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            <strong>💡 Tips:</strong> Jika Anda memilih dokter, jadwal appointment akan disesuaikan dengan jadwal praktek dokter tersebut. Jika tidak memilih, admin akan mengatur jadwal yang paling sesuai.
        </p>
    </div>
</div>
