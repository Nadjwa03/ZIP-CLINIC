<!--resources/views/pasien/index.blade.php-->
@extends('layouts.patient')

@section('content')

<!-- Welcome Section (ALWAYS SHOW) -->
<div class="flex items-center space-x-4 mb-6">
    <!-- Avatar -->
    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
    </div>
    
    <!-- Greeting -->
    <div>
        <p class="text-sm text-gray-600">Hello!</p>
        <h2 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
    </div>
</div>

<!-- List Pasien Section (ALWAYS SHOW) -->
<div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('pasien.patients.index') }}" class="flex items-center text-gray-800 hover:text-[#6B4423] transition-colors">
    <h3 class="text-lg font-bold">List Pasien</h3>
    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>

        
        <!-- Add Patient Button (Modal) -->
        @include('components.patient-add-modal', ['iconOnly' => true])
        </div>
    
    @if(isset($patients) && $patients->isNotEmpty())
        <!-- Show Patient Cards (max 2) -->
        <div class="space-y-3">
            @foreach($patients->take(2) as $patient)
            <div class="bg-[#F5F5DC] rounded-lg p-4 mb-3">
                <h4 class="font-bold text-gray-800 mb-2">{{ $patient->full_name }}</h4>
                <div class="space-y-1 text-sm text-gray-600">
                    <!-- Email (auto from user if empty) -->
                    <!-- <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $patient->email ?? Auth::user()->email }}</span>
                    </div> -->
                    
                    <!-- Phone -->
                    @if($patient->phone)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>{{ $patient->phone }}</span>
                    </div>
                    @endif
            <!-- Additional Info Row -->
                <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-600">
                    @if($patient->date_of_birth)
                    <div class="flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $patient->date_of_birth->format('d/m/Y') }}</span>
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->date_of_birth->age }} tahun</span>
                    </div>
                    @endif
                    
                    @if($patient->gender)
                    <div class="flex items-center space-x-1">
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    @endif
                    
                    @if($patient->blood_type)
                    <div class="flex items-center space-x-1">
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->blood_type }}</span>
                    </div>
                    @endif
                </div>
                </div>
            </div>
            @endforeach
            
            @if($patients->count() > 2)
            <a href="{{ route('pasien.patients.index') }}" class="block text-center text-sm text-[#6B4423] hover:underline">
                Lihat semua ({{ $patients->count() }} pasien)
            </a>
            @endif
        </div>
    @else
        <!-- Empty State for List Pasien -->
        <div class="bg-gray-50 rounded-lg p-6 text-center border-2 border-dashed border-gray-300">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-600 text-sm">Belum ada data pasien</p>
        </div>
    @endif
</div>

<!-- Reservasi Section (ALWAYS SHOW) -->
<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-800 flex items-center mb-4">
        Reservasi
        <svg class="w-5 h-5 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </h3>
    
    @if(isset($upcomingAppointments) && $upcomingAppointments->isNotEmpty())
        <!-- Show Appointments -->
        <div class="space-y-3">
        @foreach($upcomingAppointments as $appointment)
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800">{{ $appointment->service->name ?? 'Layanan' }}</p>
                    
                    @if($appointment->slot)
                    <p class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($appointment->slot->date)->format('d M Y') }}
                        • {{ substr($appointment->slot->start_time, 0, 5) }} - {{ substr($appointment->slot->end_time, 0, 5) }}
                    </p>
                    @else
                    <p class="text-sm text-gray-600">Tanggal belum ditentukan</p>
                    @endif
                </div>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                    {{ ucfirst(strtolower($appointment->status)) }}
                </span>
            </div>
        </div>
        @endforeach
        </div>
    @else
        <!-- Empty State for Reservasi -->
        <div class="bg-white rounded-lg p-8 text-center border border-gray-200">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h4 class="font-bold text-gray-800 mb-2">Belum Ada Jadwal Reservasi</h4>
            <p class="text-gray-600 text-sm mb-4">Buat Reservasi Baru untuk Mengatur Jadwal Anda</p>
            <a href="{{ route('pasien.appointments.create') }}" class="inline-block bg-[#6B4423] text-white px-6 py-3 rounded-lg font-bold hover:bg-[#5A3A1E]">
                Buat Pertemuan
            </a>
        </div>
    @endif
</div>

<!-- Lokasi Klinik Section (ALWAYS SHOW) -->
<div class="mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Lokasi Klinik</h3>
    
    <!-- Map -->
    <div class="bg-gray-200 rounded-lg overflow-hidden h-48 mb-3">
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3973.6236462940187!2d119.43715217549816!3d-5.164094552161419!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbee394cd95f315%3A0x9084b88d55a51ab9!2sZIP%20Orthodontics%20%26%20Dental%20Specialist!5e0!3m2!1sen!2sid!4v1767079385174!5m2!1sen!2sid"
        width="100%" 
        height="100%" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>
    
    <!-- Clinic Info -->
    <div class="bg-white rounded-lg p-4 border border-gray-200">
        <p class="font-semibold text-gray-800">Klinik ZIP</p>
        <p class="text-sm text-gray-600">Jl. Contoh No. 123, Makassar</p>
        <a href="https://maps.google.com/?q=Klinik+ZIP+Makassar" target="_blank" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
            View larger map
        </a>
    </div>
</div>

<!-- Extra Space for Bottom Nav -->
<div class="h-20"></div>

@endsection
