@extends('layouts.patient')

@section('content')

<!-- Patient Selection (If multiple patients) -->
@if($patients->count() > 1)
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pasien:</label>
        <select id="patient-selector" onchange="switchPatient(this.value)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent">
            @foreach($patients as $p)
            <option value="{{ $p->id }}" {{ $patient->id == $p->id ? 'selected' : '' }}>
                {{ $p->full_name }} (MRN: {{ $p->medical_record_number }})
            </option>
            @endforeach
        </select>
    </div>
</div>
@endif

<!-- Welcome Section -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Halo, {{ $patient->full_name }}! 👋</h2>
    <p class="text-gray-600">MRN: {{ $patient->medical_record_number }}</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-3 gap-3 mb-6 -mt-2">
    <!-- Appointments -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-2 mb-2">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['appointments'] }}</p>
        <p class="text-xs text-gray-500">Janji Temu</p>
    </div>
    
    <!-- Medical Records -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-2 mb-2">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['visits'] }}</p>
        <p class="text-xs text-gray-500">Kunjungan</p>
    </div>
    
    <!-- Total Visits -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-2 mb-2">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['visits'] }}</p>
        <p class="text-xs text-gray-500">Total Kunjungan</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-2 gap-3 mb-6">
    <!-- Book Appointment -->
    <a href="{{ route('pasien.appointments.create', ['patient_id' => $patient->id]) }}" class="bg-[#6B4423] hover:bg-[#5A3A1E] text-white rounded-lg p-4 shadow-sm transform hover:scale-105 transition-all">
        <div class="flex flex-col items-center space-y-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="font-semibold">Buat Janji Temu</span>
        </div>
    </a>
    
    <!-- My Appointments -->
    <a href="{{ route('pasien.appointments.index', ['patient_id' => $patient->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white rounded-lg p-4 shadow-sm transform hover:scale-105 transition-all">
        <div class="flex flex-col items-center space-y-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="font-semibold">Lihat Janji Temu</span>
        </div>
    </a>
    
    <!-- Medical Records -->
    <a href="{{ route('pasien.medical-records.index', ['patient_id' => $patient->id]) }}" class="bg-green-500 hover:bg-green-600 text-white rounded-lg p-4 shadow-sm transform hover:scale-105 transition-all">
        <div class="flex flex-col items-center space-y-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="font-semibold">Rekam Medis</span>
        </div>
    </a>
    
    <!-- Profile -->
    <a href="{{ route('pasien.settings') }}" class="bg-purple-500 hover:bg-purple-600 text-white rounded-lg p-4 shadow-sm transform hover:scale-105 transition-all">
        <div class="flex flex-col items-center space-y-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="font-semibold">Profil Saya</span>
        </div>
    </a>
</div>

<!-- Upcoming Appointments -->
@if($upcomingAppointments->count() > 0)
<div class="mb-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-bold text-gray-800">Janji Temu Mendatang</h3>
        <a href="{{ route('pasien.appointments.index', ['patient_id' => $patient->id]) }}" class="text-sm text-[#6B4423] font-semibold">Lihat Semua →</a>
    </div>
    
    <div class="space-y-3">
        @foreach($upcomingAppointments as $appointment)
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 {{ $appointment->status == 'APPROVED' ? 'border-green-500' : 'border-blue-500' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $appointment->assignedDoctor->name ?? $appointment->preferredDoctor->name ?? 'Dokter belum ditentukan' }}</p>
                            <p class="text-sm text-gray-500">{{ $appointment->service->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $appointment->slot->slot_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $appointment->status == 'APPROVED' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $appointment->status }}
                    </span>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('pasien.appointments.show', $appointment->id) }}" class="text-sm text-[#6B4423] font-semibold hover:underline">Lihat Detail →</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-500 mb-4">Belum ada janji temu yang dijadwalkan</p>
        <a href="{{ route('pasien.appointments.create', ['patient_id' => $patient->id]) }}" class="inline-block bg-[#6B4423] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#5A3A1E]">
            Buat Janji Temu Sekarang
        </a>
    </div>
</div>
@endif

<!-- Info Banner -->
<div class="bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] rounded-lg p-4 text-white mb-6">
    <div class="flex items-center space-x-3">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-bold mb-1">💡 Tips Kesehatan Gigi</p>
            <p class="text-sm opacity-90">Jangan lupa sikat gigi 2x sehari dan kontrol rutin setiap 6 bulan!</p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function switchPatient(patientId) {
    // Store selected patient in session and reload
    fetch('{{ route("pasien.switch-patient") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ patient_id: patientId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}
</script>
@endpush