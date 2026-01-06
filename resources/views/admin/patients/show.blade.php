@extends('layouts.admin')

@section('title', 'Detail Pasien | Klinik ZIP')

@section('content')
<div class="px-6 py-6">
    <!-- Secret Code Alert (shown after creating new patient) -->
    @if(session('show_claim_info') && session('secret_code'))
    <div class="mb-6 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-2">✅ Pasien Berhasil Didaftarkan!</h3>
                <p class="mb-4 opacity-90">Berikan informasi berikut kepada pasien untuk claim akun mereka:</p>

                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-75">Medical Record Number (MRN)</p>
                            <p class="text-2xl font-mono font-bold">{{ $patient->medical_record_number }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ $patient->medical_record_number }}')"
                                class="px-3 py-2 bg-white/30 hover:bg-white/40 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between border-t border-white/30 pt-3">
                        <div>
                            <p class="text-sm opacity-75">Secret Code (Kode Rahasia)</p>
                            <p class="text-4xl font-mono font-bold tracking-wider">{{ session('secret_code') }}</p>
                        </div>
                        <button onclick="copyToClipboard('{{ session('secret_code') }}')"
                                class="px-3 py-2 bg-white/30 hover:bg-white/40 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <p class="mt-4 text-sm opacity-90">
                    ⚠️ <strong>PENTING:</strong> Secret code hanya ditampilkan sekali. Pastikan pasien mencatat atau screenshot informasi ini.
                </p>
            </div>
        </div>
    </div>
    @endif

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
            <!-- Claim Status Badge -->
            @if($patient->is_claimed)
            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Claimed
            </span>
            @else
            <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-semibold rounded-full">
                Belum Di-claim
            </span>
            @endif

            @if(!$patient->is_active)
            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                Tidak Aktif
            </span>
            @else
            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                Aktif
            </span>
            @endif

            <!-- Regenerate Secret Code Button (only if not claimed) -->
            @if(!$patient->is_claimed)
            <button onclick="showRegenerateModal()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generate Kode Baru
            </button>
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

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        alert('Berhasil disalin: ' + text);
    }, function(err) {
        console.error('Failed to copy: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Berhasil disalin: ' + text);
    });
}

// Show regenerate modal
function showRegenerateModal() {
    document.getElementById('regenerateModal').classList.remove('hidden');
}

// Hide regenerate modal
function hideRegenerateModal() {
    document.getElementById('regenerateModal').classList.add('hidden');
}
</script>

<!-- Regenerate Secret Code Modal -->
<div id="regenerateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Generate Kode Baru?</h3>
                <button onclick="hideRegenerateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Perhatian!</p>
                        <p>Kode rahasia lama akan <strong>tidak berlaku</strong> setelah generate kode baru. Pastikan pasien mencatat kode baru yang akan diberikan.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.patients.regenerate-code', $patient->patient_id) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="button"
                            onclick="hideRegenerateModal()"
                            class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Ya, Generate Kode Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection
