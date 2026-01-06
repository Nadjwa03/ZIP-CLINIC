<div class="px-6 py-6">
    <!-- Header Section -->
    <div class="mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reservations</h1>
            <p class="text-gray-600 text-sm mt-1">Manage clinic appointments and reservations</p>
        </div>
        
        <!-- Add Appointment Button -->
        <a href="{{ route('admin.appointments.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Appointment
        </a>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                Calendar
            </button>
            <button class="border-transparent hover:border-gray-300 py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                Log History
            </button>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                </svg>
                <div class="ml-3">
                    <p class="text-2xl font-bold text-gray-900">{{ $todayStats['total'] }}</p>
                    <p class="text-xs text-gray-500">total appointments</p>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-100 p-4">
            <p class="text-2xl font-bold text-blue-600">{{ $todayStats['booked'] }}</p>
            <p class="text-xs text-blue-600 font-medium">BOOKED</p>
        </div>
        
        <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-100 p-4">
            <p class="text-2xl font-bold text-yellow-600">{{ $todayStats['checked_in'] }}</p>
            <p class="text-xs text-yellow-600 font-medium">CHECKED IN</p>
        </div>
        
        <div class="bg-purple-50 rounded-lg shadow-sm border border-purple-100 p-4">
            <p class="text-2xl font-bold text-purple-600">{{ $todayStats['in_treatment'] }}</p>
            <p class="text-xs text-purple-600 font-medium">IN TREATMENT</p>
        </div>
        
        <div class="bg-green-50 rounded-lg shadow-sm border border-green-100 p-4">
            <p class="text-2xl font-bold text-green-600">{{ $todayStats['completed'] }}</p>
            <p class="text-xs text-green-600 font-medium">COMPLETED</p>
        </div>
        
        <div class="bg-red-50 rounded-lg shadow-sm border border-red-100 p-4">
            <p class="text-2xl font-bold text-red-600">{{ $todayStats['cancelled'] }}</p>
            <p class="text-xs text-red-600 font-medium">CANCELLED</p>
        </div>
    </div>

    <!-- Filters and Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-between">
            <!-- Date Navigation -->
            <div class="flex items-center space-x-3">
                <button wire:click="goToToday" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Today
                </button>
                
                <button wire:click="previousDay" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                
                <div class="px-4 py-2 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($currentDate)->format('D, d M Y') }}
                    </p>
                </div>
                
                <button wire:click="nextDay" class="p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                
                <div class="flex space-x-2 ml-4">
                    <button wire:click="$set('viewMode', 'day')" 
                            class="px-3 py-1 text-sm {{ $viewMode === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' }} rounded-lg">
                        Day
                    </button>
                    <button wire:click="$set('viewMode', 'week')" 
                            class="px-3 py-1 text-sm {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300' }} rounded-lg">
                        Week
                    </button>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="flex items-center space-x-3">
                <!-- Doctor Filter -->
                <select wire:model="selectedDoctor" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Dentist</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->doctor_user_id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
                
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           wire:model.debounce.300ms="searchTerm"
                           placeholder="Search for anything here..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                
                <!-- Filters Button -->
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    @if($viewMode === 'day')
    <!-- DAY VIEW -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-4 gap-0 border-b border-gray-200">
            <!-- Time Column Header -->
            <div class="col-span-1 bg-gray-50 p-4 border-r border-gray-200">
                <p class="text-sm font-medium text-gray-600">TIME</p>
            </div>

            <!-- Doctor Columns Headers -->
            @foreach($doctors->take(3) as $doctor)
                <div class="p-4 bg-gray-50 border-r border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-semibold text-sm">{{ substr($doctor->user->name ?? $doctor->display_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $doctor->user->name ?? $doctor->display_name }}</p>
                            <p class="text-xs text-gray-500">Today's appointment: {{ $appointments->where('doctor_user_id', $doctor->doctor_user_id)->count() }} patient(s)</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Time Slots with Appointments -->
        <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
            @foreach($timeSlots as $timeSlot)
                <div class="grid grid-cols-4 gap-0">
                    <!-- Time -->
                    <div class="col-span-1 p-4 bg-gray-50 border-r border-gray-200">
                        <p class="text-sm font-medium text-gray-600">{{ $timeSlot }}</p>
                    </div>
                    
                    <!-- Doctor Slots -->
                    @foreach($doctors->take(3) as $doctor)
                        <div class="p-2 border-r border-gray-200 min-h-[80px]">
                            @php
                                $slotStart = \Carbon\Carbon::parse($currentDate . ' ' . $timeSlot);
                                $slotEnd = $slotStart->copy()->addHour();

                                $doctorAppointments = $appointments->filter(function($apt) use ($doctor, $slotStart, $slotEnd) {
                                    $aptStart = \Carbon\Carbon::parse($apt->scheduled_start_at);
                                    return $apt->doctor_user_id == $doctor->doctor_user_id &&
                                           $aptStart->gte($slotStart) && $aptStart->lt($slotEnd);
                                });
                            @endphp
                            
                            @foreach($doctorAppointments as $appointment)
                                <div wire:click="viewAppointmentDetail({{ $appointment->appointment_id }})"
                                     class="mb-2 p-2 rounded-lg cursor-pointer hover:shadow-md transition-shadow {{ $this->getStatusColor($appointment->status) }} border">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold">{{ $appointment->patient->full_name ?? 'N/A' }}</span>
                                        @if($appointment->status === 'COMPLETED')
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-xs">
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}
                                    </p>
                                    <p class="text-xs mt-1">{{ $appointment->service->name ?? 'N/A' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    @else
    <!-- WEEK VIEW -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-8 gap-0 border-b border-gray-200">
            <!-- Time Column Header -->
            <div class="col-span-1 bg-gray-50 p-4 border-r border-gray-200">
                <p class="text-sm font-medium text-gray-600">TIME</p>
            </div>

            <!-- Week Day Headers -->
            @foreach($weekDates as $date)
                <div class="p-4 bg-gray-50 border-r border-gray-200">
                    <p class="text-xs text-gray-500">{{ $date->format('D') }}</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $date->format('d') }}</p>
                    <p class="text-xs text-gray-500">{{ $date->format('M') }}</p>
                </div>
            @endforeach
        </div>

        <!-- Time Slots with Appointments -->
        <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
            @foreach($timeSlots as $timeSlot)
                <div class="grid grid-cols-8 gap-0">
                    <!-- Time -->
                    <div class="col-span-1 p-4 bg-gray-50 border-r border-gray-200">
                        <p class="text-sm font-medium text-gray-600">{{ $timeSlot }}</p>
                    </div>

                    <!-- Day Slots -->
                    @foreach($weekDates as $date)
                        <div class="p-2 border-r border-gray-200 min-h-[80px]">
                            @php
                                $dateStr = $date->format('Y-m-d');
                                $slotStart = \Carbon\Carbon::parse($dateStr . ' ' . $timeSlot);
                                $slotEnd = $slotStart->copy()->addHour();

                                $dayAppointments = $appointments->filter(function($apt) use ($slotStart, $slotEnd) {
                                    $aptStart = \Carbon\Carbon::parse($apt->scheduled_start_at);
                                    return $aptStart->gte($slotStart) && $aptStart->lt($slotEnd);
                                });
                            @endphp

                            @foreach($dayAppointments as $appointment)
                                <div wire:click="viewAppointmentDetail({{ $appointment->appointment_id }})"
                                     class="mb-2 p-2 rounded-lg cursor-pointer hover:shadow-md transition-shadow {{ $this->getStatusColor($appointment->status) }} border">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold">{{ $appointment->patient->full_name ?? 'N/A' }}</span>
                                        @if($appointment->status === 'COMPLETED')
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-xs">
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }}
                                    </p>
                                    <p class="text-xs mt-1 truncate">{{ $appointment->service->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-600 truncate">Dr. {{ $appointment->doctor->user->name ?? $appointment->doctor->display_name }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedAppointment)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-show="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeDetailModal"></div>
                
                <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Appointment Details</h3>
                        
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Patient</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $selectedAppointment->patient->full_name ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $selectedAppointment->doctor->name ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Service</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $selectedAppointment->service->name ?? 'N/A' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($selectedAppointment->scheduled_start_at)->format('d M Y, H:i') }} - 
                                    {{ \Carbon\Carbon::parse($selectedAppointment->scheduled_end_at)->format('H:i') }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Complaint</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $selectedAppointment->complaint ?? '-' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($selectedAppointment->status) }}">
                                        {{ $selectedAppointment->status }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                        
                        <div class="mt-6 flex space-x-3">
                            @if($selectedAppointment->status === 'BOOKED')
                                <button wire:click="updateStatus({{ $selectedAppointment->appointment_id }}, 'CHECKED_IN')"
                                        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                    Check In
                                </button>
                            @elseif($selectedAppointment->status === 'CHECKED_IN')
                                <button wire:click="updateStatus({{ $selectedAppointment->appointment_id }}, 'IN_TREATMENT')"
                                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    Start Treatment
                                </button>
                            @endif
                            
                            <button wire:click="closeDetailModal"
                                    class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>