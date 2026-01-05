@extends('layouts.admin')

@section('title', 'Detail Pasien | Klinik ZIP')

@section('content')
<div class="px-6 py-6">
    <!-- Back Button & Header -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.patients.index') }}" 
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $patient->full_name }}</h1>
                <p class="text-gray-600 text-sm mt-1">{{ $patient->medical_record_number }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @if(!$patient->is_active)
            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                Tidak Aktif
            </span>
            @else
            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                Aktif
            </span>
            @endif
            
            <a href="{{ route('admin.patients.edit', $patient) }}"
               class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors">
                ✏️ Edit Data
            </a>
        </div>
    </div>

    <!-- Patient Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-start gap-6">
            <!-- Avatar -->
            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-3xl flex-shrink-0">
                {{ strtoupper(substr($patient->full_name, 0, 1)) }}
            </div>

            <!-- Info Grid -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Gender & Usia</p>
                    <p class="text-base text-gray-900 mt-1">
                        {{ $patient->gender === 'L' ? '👨 Laki-laki' : '👩 Perempuan' }}
                        @if($patient->age)
                        • {{ $patient->age }} tahun
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Kontak</p>
                    <p class="text-base text-gray-900 mt-1">📞 {{ $patient->phone }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Terdaftar Sejak</p>
                    <p class="text-base text-gray-900 mt-1">📅 {{ $patient->formatted_registered }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Golongan Darah</p>
                    <p class="text-base text-gray-900 mt-1">{{ $patient->blood_type ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Last Visit</p>
                    <p class="text-base text-gray-900 mt-1">{{ $patient->formatted_last_visit }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Last Treatment</p>
                    <p class="text-base text-gray-900 mt-1">{{ $patient->last_treatment ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto">
                <button 
                    onclick="showTab('information')"
                    id="tab-information"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors border-blue-500 text-blue-600">
                    📋 Informasi Pasien
                </button>
                <button 
                    onclick="showTab('appointments')"
                    id="tab-appointments"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors border-transparent text-gray-500 hover:text-gray-700">
                    📅 Appointment
                </button>
                <button 
                    onclick="showTab('medical')"
                    id="tab-medical"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors border-transparent text-gray-500 hover:text-gray-700">
                    🏥 Medical Record
                </button>
                <button 
                    onclick="showTab('media')"
                    id="tab-media"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-colors border-transparent text-gray-500 hover:text-gray-700">
                    📸 Foto & Media
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="content-information" class="tab-content">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-3 border-b border-gray-200">Data Lengkap Pasien</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Rekam Medis</label>
                        <p class="text-base text-gray-900">{{ $patient->medical_record_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                        <p class="text-base text-gray-900">{{ $patient->full_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jenis & No. Identitas</label>
                        <p class="text-base text-gray-900">{{ $patient->id_type }} - {{ $patient->id_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Lahir</label>
                        <p class="text-base text-gray-900">
                            {{ $patient->date_of_birth->format('d F Y') }} ({{ $patient->age }} tahun)
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Kelamin</label>
                        <p class="text-base text-gray-900">{{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Golongan Darah</label>
                        <p class="text-base text-gray-900">{{ $patient->blood_type ?? '-' }}</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-base text-gray-900">{{ $patient->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Telepon</label>
                        <p class="text-base text-gray-900">{{ $patient->phone }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                        <p class="text-base text-gray-900">{{ $patient->address }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Kontak Darurat</label>
                        <p class="text-base text-gray-900">
                            @if($patient->emergency_contact_name)
                            {{ $patient->emergency_contact_name }}
                            @if($patient->emergency_contact_phone)
                            <br><span class="text-sm text-gray-600">{{ $patient->emergency_contact_phone }}</span>
                            @endif
                            @if($patient->emergency_contact_relation)
                            <br><span class="text-sm text-gray-600">({{ $patient->emergency_contact_relation }})</span>
                            @endif
                            @else
                            -
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Alergi</label>
                        <p class="text-base text-gray-900">{{ $patient->allergies ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Riwayat Penyakit</label>
                        <p class="text-base text-gray-900">{{ $patient->medical_history ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="content-appointments" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Appointment History</h3>
            <p class="text-gray-600">Fitur ini sedang dalam pengembangan</p>
        </div>
    </div>

    <div id="content-medical" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Medical Record</h3>
            <p class="text-gray-600">Fitur ini sedang dalam pengembangan</p>
        </div>
    </div>

    <div id="content-media" class="tab-content hidden">
        @livewire('admin.patients.media-gallery', ['patientId' => $patient->patient_id])
    </div>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active state to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-blue-500', 'text-blue-600');
}
</script>
@endpush
@endsection
