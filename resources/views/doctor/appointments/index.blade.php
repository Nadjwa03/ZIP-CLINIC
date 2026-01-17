@extends('layouts.doctor')

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Daftar Janji Temu</h2>
    <p class="text-gray-600">Kelola semua janji temu pasien Anda</p>
</div>

<!-- Filter Tabs -->
<div class="mb-6 bg-white rounded-lg shadow-sm p-4">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('doctor.appointments.index') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ (!request('status') || request('status') == 'all') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Semua ({{ $counts['all'] }})
        </a>
        <a href="{{ route('doctor.appointments.index', ['status' => 'booked']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request('status') == 'booked' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Terjadwal ({{ $counts['booked'] }})
        </a>
        <a href="{{ route('doctor.appointments.index', ['status' => 'checked_in']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request('status') == 'checked_in' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Check-in ({{ $counts['checked_in'] }})
        </a>
        <a href="{{ route('doctor.appointments.index', ['status' => 'in_treatment']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request('status') == 'in_treatment' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Perawatan ({{ $counts['in_treatment'] }})
        </a>
        <a href="{{ route('doctor.appointments.index', ['status' => 'completed']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request('status') == 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Selesai ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('doctor.appointments.index', ['status' => 'cancelled']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ request('status') == 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Dibatalkan ({{ $counts['cancelled'] }})
        </a>
    </div>
</div>

<!-- Appointments List -->
@if($appointments->count() > 0)
<div class="space-y-4">
    @foreach($appointments as $appointment)
    <a href="{{ route('doctor.appointments.show', $appointment->appointment_id) }}"
       class="block bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border-l-4
              {{ $appointment->status == 'BOOKED' ? 'border-blue-500' : '' }}
              {{ $appointment->status == 'CHECKED_IN' ? 'border-yellow-500' : '' }}
              {{ $appointment->status == 'IN_TREATMENT' ? 'border-purple-500' : '' }}
              {{ $appointment->status == 'COMPLETED' ? 'border-green-500' : '' }}
              {{ $appointment->status == 'CANCELLED' ? 'border-red-500' : '' }}">

        <div class="p-5">
            <!-- Header: Patient & Status -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3 flex-1">
                    <!-- Patient Avatar -->
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-lg">
                            {{ strtoupper(substr($appointment->patient->full_name, 0, 1)) }}
                        </span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 text-lg truncate">{{ $appointment->patient->full_name }}</h3>
                        <p class="text-sm text-gray-600">{{ $appointment->service->service_name }}</p>
                    </div>
                </div>

                <!-- Status Badge -->
                <span class="px-3 py-1 text-xs font-bold rounded-full whitespace-nowrap
                            {{ $appointment->status == 'BOOKED' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $appointment->status == 'CHECKED_IN' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $appointment->status == 'IN_TREATMENT' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $appointment->status == 'COMPLETED' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $appointment->status == 'CANCELLED' ? 'bg-red-100 text-red-700' : '' }}">
                    @switch($appointment->status)
                        @case('BOOKED') Terjadwal @break
                        @case('CHECKED_IN') Check-in @break
                        @case('IN_TREATMENT') Perawatan @break
                        @case('COMPLETED') Selesai @break
                        @case('CANCELLED') Dibatalkan @break
                    @endswitch
                </span>
            </div>

            <!-- Date & Time Info -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('d M Y') }}</span>
                </div>

                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}</span>
                </div>
            </div>

            <!-- Complaint Preview -->
            @if($appointment->complaint)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <p class="text-xs font-semibold text-yellow-800 mb-1">Keluhan:</p>
                <p class="text-sm text-yellow-900">{{ Str::limit($appointment->complaint, 120) }}</p>
            </div>
            @endif

            <!-- Patient Info -->
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span>MRN: {{ $appointment->patient->mrn }}</span>
                    </div>
                    <span class="text-blue-600 font-semibold">Lihat Detail →</span>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $appointments->links() }}
</div>

@else
<!-- Empty State -->
<div class="bg-white rounded-lg shadow-sm p-12 text-center">
    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Tidak Ada Janji Temu</h3>
    <p class="text-gray-600">
        @if(request('status'))
            Tidak ada janji temu dengan status ini
        @else
            Belum ada janji temu yang terdaftar
        @endif
    </p>
</div>
@endif

@endsection
