@extends('layouts.doctor')

@section('content')

<div class="max-w-7xl mx-auto" x-data="{ activeTab: 'upcoming' }">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('doctor.patients.index') }}" 
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Patient List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT COLUMN: Patient Info -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Profile Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Avatar -->
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 rounded-full bg-[#6B4423] flex items-center justify-center">
                        <span class="text-white font-bold text-3xl">
                            {{ strtoupper(substr($patient->full_name, 0, 2)) }}
                        </span>
                    </div>
                </div>

                <!-- Name & Email -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $patient->full_name }}</h2>
                    @if($patient->email)
                        <p class="text-sm text-gray-600">{{ $patient->email }}</p>
                    @endif
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-200">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['past_count'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">Past</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['upcoming_count'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">Upcoming</p>
                    </div>
                </div>

                <!-- Demographics -->
                <div class="space-y-3 text-sm">
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-gray-600">Gender</span>
                        <span class="font-medium text-gray-900">
                            {{ $patient->gender ? ucfirst(strtolower($patient->gender)) : '-' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-gray-600">Birthday</span>
                        <span class="font-medium text-gray-900">
                            {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') : '-' }}
                        </span>
                    </div>

                    @if($patient->phone)
                        <div class="grid grid-cols-2 gap-2">
                            <span class="text-gray-600">Phone</span>
                            <span class="font-medium text-gray-900">{{ $patient->phone }}</span>
                        </div>
                    @endif

                    @if($patient->address)
                        <div class="grid grid-cols-2 gap-2">
                            <span class="text-gray-600">Address</span>
                            <span class="font-medium text-gray-900">{{ Str::limit($patient->address, 30) }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-gray-600">Status</span>
                        <span>
                            @if($isActive)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-gray-600">MRN</span>
                        <span class="font-medium text-gray-900">{{ $patient->medical_record_number ?? '-' }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Appointments & Timeline -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'upcoming'" 
                                :class="activeTab === 'upcoming' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                                class="px-6 py-3 text-sm font-medium border-b-2 transition">
                            Upcoming Appointments
                        </button>
                        <button @click="activeTab = 'past'" 
                                :class="activeTab === 'past' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300'"
                                class="px-6 py-3 text-sm font-medium border-b-2 transition">
                            Past Appointments
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    
                    <!-- Upcoming Tab -->
                    <div x-show="activeTab === 'upcoming'">
                        @if($upcomingAppointments->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingAppointments as $appointment)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $appointment->service->service_name }}</p>
                                            <div class="flex items-center gap-4 text-sm text-gray-600 mt-1">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('d M Y') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                        <a href="{{ route('doctor.appointments.show', $appointment->appointment_id) }}" 
                                           class="px-4 py-2 text-sm font-medium text-[#6B4423] hover:text-[#5A3A1E]">
                                            View →
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No upcoming appointments</p>
                        @endif
                    </div>

                    <!-- Past Tab -->
                    <div x-show="activeTab === 'past'">
                        @if($pastAppointments->count() > 0)
                            
                            <!-- Treatment Timeline -->
                            <div class="relative">
                                @foreach($pastAppointments as $index => $appointment)
                                    <div class="relative pl-8 pb-8 {{ $loop->last ? 'pb-0' : '' }}">
                                        
                                        <!-- Timeline Line -->
                                        @if(!$loop->last)
                                            <div class="absolute left-2 top-8 bottom-0 w-0.5 bg-gray-200"></div>
                                        @endif

                                        <!-- Timeline Dot -->
                                        <div class="absolute left-0 top-2 w-4 h-4 rounded-full bg-blue-600 border-2 border-white"></div>

                                        <!-- Content -->
                                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $appointment->service->service_name }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('d M Y') }} • 
                                                        {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}
                                                    </p>
                                                </div>
                                                
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    {{ $appointment->status === 'COMPLETED' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $appointment->status === 'CANCELLED' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $appointment->status === 'NO_SHOW' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                    {{ ucwords(str_replace('_', ' ', strtolower($appointment->status))) }}
                                                </span>
                                            </div>

                                            @if($appointment->complaint)
                                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                                    <p class="text-xs font-semibold text-yellow-800">Complaint:</p>
                                                    <p class="text-sm text-yellow-900">{{ $appointment->complaint }}</p>
                                                </div>
                                            @endif

                                            @if($appointment->notes)
                                                <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded">
                                                    <p class="text-xs font-semibold text-blue-800">Notes:</p>
                                                    <p class="text-sm text-blue-900">{{ $appointment->notes }}</p>
                                                </div>
                                            @endif

                                            <div class="mt-3 flex justify-end">
                                                <a href="{{ route('doctor.appointments.show', $appointment->appointment_id) }}" 
                                                   class="text-sm font-medium text-[#6B4423] hover:text-[#5A3A1E]">
                                                    View Details →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @else
                            <p class="text-center text-gray-500 py-8">No past appointments</p>
                        @endif
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@endsection
