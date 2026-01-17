<div wire:poll.5s>
    <!-- Today's Appointments -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Jadwal Hari Ini</h3>
            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::today()->format('d M Y') }}</span>
        </div>

        @if($todayAppointments->count() > 0)
        <div class="space-y-3">
            @foreach($todayAppointments as $appointment)
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4
                        {{ $appointment->status == 'BOOKED' ? 'border-blue-500' : '' }}
                        {{ $appointment->status == 'CHECKED_IN' ? 'border-yellow-500' : '' }}
                        {{ $appointment->status == 'IN_TREATMENT' ? 'border-purple-500' : '' }}
                        {{ $appointment->status == 'COMPLETED' ? 'border-green-500' : '' }}">

                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $appointment->patient->full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $appointment->service->service_name }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-bold rounded-full
                                {{ $appointment->status == 'BOOKED' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $appointment->status == 'CHECKED_IN' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $appointment->status == 'IN_TREATMENT' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $appointment->status == 'COMPLETED' ? 'bg-green-100 text-green-700' : '' }}">
                        @switch($appointment->status)
                            @case('BOOKED') Terjadwal @break
                            @case('CHECKED_IN') Check-in @break
                            @case('IN_TREATMENT') Perawatan @break
                            @case('COMPLETED') Selesai @break
                        @endswitch
                    </span>
                </div>

                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}</span>
                    </div>
                    <a href="{{ route('doctor.appointments.show', $appointment->appointment_id) }}" class="text-[#6B4423] font-semibold hover:underline">
                        Lihat Detail →
                    </a>
                </div>

                @if($appointment->complaint)
                <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                    <p class="text-xs font-semibold text-yellow-800">Keluhan:</p>
                    <p class="text-sm text-yellow-900">{{ $appointment->complaint }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500">Tidak ada jadwal untuk hari ini</p>
        </div>
        @endif
    </div>

    <!-- Upcoming Appointments -->
    @if($upcomingAppointments->count() > 0)
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Jadwal Mendatang (7 Hari)</h3>

        <div class="space-y-3">
            @foreach($upcomingAppointments as $appointment)
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-gray-300">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $appointment->patient->full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $appointment->service->service_name }}</p>
                        <div class="flex items-center space-x-3 text-sm text-gray-600 mt-2">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
