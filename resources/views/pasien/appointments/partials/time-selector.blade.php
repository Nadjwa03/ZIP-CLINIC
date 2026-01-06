<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-brown-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
            5
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Pilih Waktu Perawatan</h3>
            <p class="text-sm text-gray-600">
                Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Time Slots --}}
    @if(count($availableSlots) > 0)
        <div class="mb-6">
            <p class="text-sm font-medium text-gray-700 mb-3">Pilih Jam:</p>
            
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                @foreach($availableSlots as $slot)
                    <button 
                        wire:click="selectTime('{{ $slot['start'] }}')"
                        type="button"
                        @if(!$slot['available']) disabled @endif
                        class="p-3 border-2 rounded-lg text-center transition
                            @if($slot['available'])
                                {{ $selectedTime == $slot['start'] ? 'border-brown-600 bg-brown-600 text-white' : 'border-gray-200 hover:border-brown-300 hover:bg-brown-50' }}
                            @else
                                border-gray-100 bg-gray-100 text-gray-400 cursor-not-allowed
                            @endif">
                        
                        <p class="font-semibold">{{ $slot['label'] }}</p>
                        
                        @if(!$slot['available'])
                            <p class="text-xs mt-1">Penuh</p>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center justify-center gap-6 mb-6 text-sm">
            <div class="flex items-center">
                <div class="w-8 h-8 border-2 border-gray-200 rounded mr-2"></div>
                <span class="text-gray-600">Tersedia</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 border-2 border-brown-600 bg-brown-600 rounded mr-2"></div>
                <span class="text-gray-600">Dipilih</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 border-2 border-gray-100 bg-gray-100 rounded mr-2"></div>
                <span class="text-gray-600">Tidak Tersedia</span>
            </div>
        </div>

        {{-- Selected Time Display --}}
        @if($selectedTime)
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">Waktu dipilih:</p>
                        <p class="text-lg font-semibold text-green-900">
                            {{ $selectedTime }}
                            @if($this->selectedService)
                                - {{ \Carbon\Carbon::parse($selectedTime)->addMinutes($this->selectedService->duration_minutes)->format('H:i') }}
                                <span class="text-sm text-green-700">({{ $this->selectedService->duration_minutes }} menit)</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

    @else
        {{-- No Slots Available --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-600 mb-2">Tidak ada slot tersedia untuk tanggal ini</p>
            <p class="text-sm text-gray-500 mb-4">Silakan pilih tanggal lain</p>
            <button 
                wire:click="$set('currentStep', 4)"
                class="px-6 py-2 bg-brown-600 text-white rounded-lg hover:bg-brown-700">
                Pilih Tanggal Lain
            </button>
        </div>
    @endif

    {{-- Help Text --}}
    @if(count($availableSlots) > 0)
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">
                <strong>💡 Tips:</strong> Slot yang ditampilkan adalah waktu yang tersedia 
                @if($doctorId)
                    berdasarkan jadwal dokter yang Anda pilih.
                @else
                    berdasarkan jam operasional klinik. Admin akan mengatur dokter untuk Anda.
                @endif
            </p>
        </div>
    @endif

    {{-- Loading State --}}
    <div wire:loading wire:target="selectTime" class="mt-4 text-center text-gray-600">
        <svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Memproses...
    </div>
</div>
