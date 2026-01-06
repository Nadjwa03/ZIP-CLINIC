<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-brown-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
            4
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Pilih Tanggal</h3>
            <p class="text-sm text-gray-600">Kapan Anda ingin berobat?</p>
        </div>
    </div>

    {{-- Quick Date Selection --}}
    <div class="mb-6">
        <p class="text-sm font-medium text-gray-700 mb-3">Tanggal Cepat:</p>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-7 gap-2">
            @foreach($quickDates as $qDate)
                <button 
                    wire:click="selectDate('{{ $qDate['date'] }}')"
                    type="button"
                    class="p-3 border-2 rounded-lg text-center transition hover:border-brown-600 hover:bg-brown-50
                        {{ $selectedDate == $qDate['date'] ? 'border-brown-600 bg-brown-50' : 'border-gray-200' }}">
                    <p class="text-xs text-gray-500 uppercase">{{ $qDate['day'] }}</p>
                    <p class="text-xl font-bold text-gray-900">{{ $qDate['dayNum'] }}</p>
                    <p class="text-xs text-gray-500">{{ $qDate['month'] }}</p>
                    @if($qDate['isToday'])
                        <span class="text-xs text-brown-600 font-medium">Hari Ini</span>
                    @elseif($qDate['isTomorrow'])
                        <span class="text-xs text-gray-600">Besok</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Divider --}}
    <div class="relative mb-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">atau</span>
        </div>
    </div>

    {{-- Full Calendar Picker --}}
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Pilih Tanggal Lain:
        </label>
        <input 
            type="date" 
            wire:model="selectedDate"
            wire:change="selectDate($event.target.value)"
            min="{{ now()->format('Y-m-d') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brown-600 focus:border-transparent">
    </div>

    {{-- Selected Date Display --}}
    @if($selectedDate)
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-800">Tanggal dipilih:</p>
                    <p class="text-lg font-semibold text-green-900">
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Help Text --}}
    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            <strong>💡 Info:</strong> Klinik tutup pada hari Minggu. Jam operasional: Senin-Jumat 09:00-17:00, Sabtu 09:00-13:00.
        </p>
    </div>

    {{-- Loading State --}}
    <div wire:loading wire:target="selectDate" class="mt-4 text-center text-gray-600">
        <svg class="animate-spin inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Memuat slot waktu...
    </div>
</div>
