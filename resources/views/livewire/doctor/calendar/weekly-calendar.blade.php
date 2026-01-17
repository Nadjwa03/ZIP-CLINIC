<div wire:poll.10s>
    <!-- Calendar Controls -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <button wire:click="previousWeek" 
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                
                <button wire:click="nextWeek"
                        class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <h3 class="text-lg font-semibold text-gray-800 ml-4">
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - 
                    {{ \Carbon\Carbon::parse($startDate)->addDays(6)->translatedFormat('d M Y') }}
                </h3>
            </div>

            <button wire:click="goToToday"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                Hari Ini
            </button>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <div class="min-w-[800px]">
            <!-- Week Header -->
            <div class="grid grid-cols-8 border-b border-gray-200">
                <div class="p-4 bg-gray-50 font-semibold text-gray-700">Waktu</div>
                @foreach($weekDays as $day)
                <div class="p-4 text-center border-l border-gray-200
                            {{ $day['is_today'] ? 'bg-blue-50' : 'bg-gray-50' }}">
                    <div class="text-sm text-gray-600">{{ $day['day_name'] }}</div>
                    <div class="text-2xl font-bold {{ $day['is_today'] ? 'text-blue-600' : 'text-gray-800' }}">
                        {{ $day['day_number'] }}
                    </div>
                    <div class="text-xs text-gray-500">{{ $day['month'] }}</div>
                </div>
                @endforeach
            </div>

            <!-- Time Slots -->
            <div class="divide-y divide-gray-100">
                @foreach($timeSlots as $slot)
                <div class="grid grid-cols-8 min-h-[60px]">
                    <!-- Time Label -->
                    <div class="p-3 bg-gray-50 text-sm text-gray-600 font-medium">
                        {{ $slot['label'] }}
                    </div>

                    <!-- Day Columns -->
                    @foreach($weekDays as $day)
                    <div class="border-l border-gray-200 p-2 relative {{ $day['is_today'] ? 'bg-blue-50/30' : '' }}">
                        @php
                            $slotTime = $slot['time'];
                            $dayAppointments = $day['appointments']->filter(function($apt) use ($slotTime) {
                                $startTime = \Carbon\Carbon::parse($apt->scheduled_start_at)->format('H:i');
                                return $startTime == $slotTime;
                            });
                        @endphp

                        @foreach($dayAppointments as $appointment)
                        <a href="{{ route('doctor.appointments.show', $appointment->appointment_id) }}"
                           class="block mb-1 p-2 rounded text-xs border-l-2 hover:shadow-md transition
                                  {{ $appointment->status == 'BOOKED' ? 'bg-blue-100 border-blue-500 text-blue-800' : '' }}
                                  {{ $appointment->status == 'CHECKED_IN' ? 'bg-yellow-100 border-yellow-500 text-yellow-800' : '' }}
                                  {{ $appointment->status == 'IN_TREATMENT' ? 'bg-purple-100 border-purple-500 text-purple-800' : '' }}
                                  {{ $appointment->status == 'COMPLETED' ? 'bg-green-100 border-green-500 text-green-800' : '' }}">
                            <div class="font-semibold truncate">{{ $appointment->patient->full_name }}</div>
                            <div class="truncate opacity-90">{{ $appointment->service->service_name }}</div>
                            <div class="text-xs opacity-75">
                                {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 bg-white rounded-lg shadow-sm p-4">
        <h4 class="font-semibold text-gray-800 mb-3">Status:</h4>
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                <span>Terjadwal</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                <span>Check-in</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-purple-500 rounded mr-2"></div>
                <span>Perawatan</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span>Selesai</span>
            </div>
        </div>
    </div>
</div>
