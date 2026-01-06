@extends('layouts.patient')

@section('content')

<!-- Back Button & Header -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center">
        <a href="{{ route('patient.dashboard') }}" class="mr-3 text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Janji Temu Saya</h2>
            <p class="text-sm text-gray-600">{{ $patient->full_name }}</p>
        </div>
    </div>
    <a href="{{ route('patient.appointments.create') }}" class="bg-[#6B4423] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[#5A3A1E] flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Buat Baru</span>
    </a>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-lg shadow-sm p-2 mb-6">
    <div class="flex space-x-2">
        <a href="{{ route('patient.appointments.index', ['status' => 'all']) }}"
           class="flex-1 text-center py-2 px-4 rounded-lg font-semibold transition-all {{ $filter == 'all' ? 'bg-[#6B4423] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Semua ({{ $counts['all'] }})
        </a>
        <a href="{{ route('patient.appointments.index', ['status' => 'upcoming']) }}"
           class="flex-1 text-center py-2 px-4 rounded-lg font-semibold transition-all {{ $filter == 'upcoming' ? 'bg-[#6B4423] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Mendatang ({{ $counts['upcoming'] }})
        </a>
        <a href="{{ route('patient.appointments.index', ['status' => 'completed']) }}"
           class="flex-1 text-center py-2 px-4 rounded-lg font-semibold transition-all {{ $filter == 'completed' ? 'bg-[#6B4423] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Selesai ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('patient.appointments.index', ['status' => 'cancelled']) }}"
           class="flex-1 text-center py-2 px-4 rounded-lg font-semibold transition-all {{ $filter == 'cancelled' ? 'bg-[#6B4423] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            Dibatalkan ({{ $counts['cancelled'] }})
        </a>
    </div>
</div>

<!-- Appointments List -->
@if($appointments->count() > 0)
<div class="space-y-4 mb-20">
    @foreach($appointments as $appointment)
    <a href="{{ route('patient.appointments.show', $appointment->appointment_id) }}"
       class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-all border-l-4
              {{ $appointment->status == 'BOOKED' ? 'border-blue-500' : '' }}
              {{ $appointment->status == 'CHECKED_IN' ? 'border-green-500' : '' }}
              {{ $appointment->status == 'IN_TREATMENT' ? 'border-yellow-500' : '' }}
              {{ $appointment->status == 'COMPLETED' ? 'border-gray-400' : '' }}
              {{ $appointment->status == 'CANCELLED' ? 'border-red-500' : '' }}">

        <!-- Header: Service & Status -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 text-lg">{{ $appointment->service->service_name }}</h3>
                <p class="text-sm text-gray-500">{{ $appointment->service->speciality->name ?? 'Layanan Umum' }}</p>
            </div>
            <span class="px-3 py-1 text-xs font-bold rounded-full
                        {{ $appointment->status == 'BOOKED' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $appointment->status == 'CHECKED_IN' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $appointment->status == 'IN_TREATMENT' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $appointment->status == 'COMPLETED' ? 'bg-gray-100 text-gray-700' : '' }}
                        {{ $appointment->status == 'CANCELLED' ? 'bg-red-100 text-red-700' : '' }}">
                @switch($appointment->status)
                    @case('BOOKED') Terjadwal @break
                    @case('CHECKED_IN') Check-in @break
                    @case('IN_TREATMENT') Perawatan @break
                    @case('COMPLETED') Selesai @break
                    @case('CANCELLED') Dibatalkan @break
                    @default {{ $appointment->status }}
                @endswitch
            </span>
        </div>

        <!-- Doctor Info -->
        <div class="flex items-center space-x-3 mb-3 pb-3 border-b border-gray-100">
            <div class="w-12 h-12 bg-[#6B4423] rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr($appointment->doctor->user->name ?? 'D', 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-800">{{ $appointment->doctor->user->name ?? 'Dokter' }}</p>
                <p class="text-sm text-gray-500">{{ $appointment->doctor->specialization ?? 'Dokter Gigi' }}</p>
            </div>
        </div>

        <!-- Date & Time Info -->
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tanggal</p>
                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Waktu</p>
                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Price Info -->
        <!-- <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm text-gray-600">Estimasi Biaya</span>
            </div>
            <span class="font-bold text-[#6B4423]">Rp {{ number_format($appointment->service->price, 0, ',', '.') }}</span>
        </div> -->

        <!-- Complaint (if exists) -->
        @if($appointment->complaint)
        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
            <p class="text-xs font-semibold text-yellow-800 mb-1">Keluhan:</p>
            <p class="text-sm text-yellow-900">{{ Str::limit($appointment->complaint, 100) }}</p>
        </div>
        @endif

        <!-- View Detail Arrow -->
        <div class="flex items-center justify-end mt-3 text-[#6B4423] font-semibold">
            <span class="text-sm">Lihat Detail</span>
            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </a>
    @endforeach
</div>

<!-- Pagination -->
<div class="mb-20">
    {{ $appointments->links() }}
</div>

@else
<!-- Empty State -->
<div class="bg-white rounded-lg shadow-sm p-8 text-center mb-20">
    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <h3 class="text-lg font-bold text-gray-800 mb-2">
        @if($filter == 'upcoming')
            Tidak Ada Janji Temu Mendatang
        @elseif($filter == 'completed')
            Belum Ada Janji Temu Selesai
        @elseif($filter == 'cancelled')
            Tidak Ada Janji Temu Dibatalkan
        @else
            Belum Ada Janji Temu
        @endif
    </h3>
    <p class="text-gray-500 mb-6">
        @if($filter == 'all')
            Buat janji temu pertama Anda sekarang untuk mulai perawatan
        @else
            Tidak ada janji temu dengan status ini
        @endif
    </p>
    @if($filter == 'all' || $filter == 'upcoming')
    <a href="{{ route('patient.appointments.create') }}" class="inline-block bg-[#6B4423] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#5A3A1E]">
        Buat Janji Temu
    </a>
    @endif
</div>
@endif

@endsection
