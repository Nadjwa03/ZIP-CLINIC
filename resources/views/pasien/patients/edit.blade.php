@extends('layouts.patient')

@section('content')

<!-- Error/Success Messages -->
@if(session('success'))
<div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down">
    <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50">
    <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
        <span class="font-semibold">{{ session('error') }}</span>
    </div>
</div>
@endif

<!-- Back Button -->
<a href="{{ route('pasien.patients.index') }}" class="inline-flex items-center text-gray-600 hover:text-[#6B4423] mb-4 transition-colors">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    <span class="text-sm font-medium">Kembali</span>
</a>

<!-- Page Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Edit Data Pasien</h2>
    <p class="text-sm text-gray-600 mt-1">{{ $patient->full_name }} • {{ $patient->medical_record_number }}</p>
</div>

<!-- Edit Form -->
<form action="{{ route('pasien.patients.update', $patient) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
    @csrf
    @method('PUT')
    
    <!-- Informasi Identitas -->
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#6B4423]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
            Informasi Identitas
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Jenis Identitas -->
            <div>
                <label for="id_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Identitas <span class="text-red-500">*</span>
                </label>
                <select id="id_type" 
                        name="id_type" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent">
                    <option value="">Pilih Jenis Identitas</option>
                    <option value="KTP" {{ old('id_type', $patient->id_type) === 'KTP' ? 'selected' : '' }}>KTP</option>
                    <option value="SIM" {{ old('id_type', $patient->id_type) === 'SIM' ? 'selected' : '' }}>SIM</option>
                    <option value="PASSPORT" {{ old('id_type', $patient->id_type) === 'PASSPORT' ? 'selected' : '' }}>Passport</option>
                    <option value="KK" {{ old('id_type', $patient->id_type) === 'KK' ? 'selected' : '' }}>KK</option>
                </select>
                @error('id_type')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nomor Identitas -->
            <div>
                <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Identitas <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="id_number" 
                       name="id_number" 
                       value="{{ old('id_number', $patient->id_number) }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                       placeholder="Masukkan nomor identitas">
                @error('id_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    
    <!-- Data Pribadi -->
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#6B4423]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Data Pribadi
        </h3>
        
        <div class="space-y-4">
            <!-- Nama Lengkap -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="full_name" 
                       name="full_name" 
                       value="{{ old('full_name', $patient->full_name) }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                       placeholder="Masukkan nama lengkap">
                @error('full_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal Lahir -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="date_of_birth" 
                           name="date_of_birth" 
                           value="{{ old('date_of_birth', $patient->date_of_birth) }}"
                           required
                           max="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent">
                    @error('date_of_birth')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Jenis Kelamin -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select id="gender" 
                            name="gender" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender', $patient->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $patient->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $patient->email) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                       placeholder="email@example.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nomor Telepon -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Telepon <span class="text-red-500">*</span>
                </label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone', $patient->phone) }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                       placeholder="08xx-xxxx-xxxx">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Alamat -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Alamat Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea id="address" 
                          name="address" 
                          rows="3"
                          required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                          placeholder="Masukkan alamat lengkap">{{ old('address', $patient->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Golongan Darah -->
            <div>
                <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Golongan Darah
                </label>
                <select id="blood_type" 
                        name="blood_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent">
                    <option value="">Pilih Golongan Darah</option>
                    <option value="A" {{ old('blood_type', $patient->blood_type) === 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ old('blood_type', $patient->blood_type) === 'B' ? 'selected' : '' }}>B</option>
                    <option value="AB" {{ old('blood_type', $patient->blood_type) === 'AB' ? 'selected' : '' }}>AB</option>
                    <option value="O" {{ old('blood_type', $patient->blood_type) === 'O' ? 'selected' : '' }}>O</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Kontak Darurat -->
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#6B4423]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            Kontak Darurat
        </h3>
        
        <div class="space-y-4">
            <!-- Nama Kontak Darurat -->
            <div>
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Kontak Darurat
                </label>
                <input type="text" 
                       id="emergency_contact_name" 
                       name="emergency_contact_name" 
                       value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                       placeholder="Nama orang yang dapat dihubungi">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nomor Telepon Darurat -->
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Telepon Darurat
                    </label>
                    <input type="tel" 
                           id="emergency_contact_phone" 
                           name="emergency_contact_phone" 
                           value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                           placeholder="08xx-xxxx-xxxx">
                </div>
                
                <!-- Hubungan -->
                <div>
                    <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 mb-2">
                        Hubungan
                    </label>
                    <input type="text" 
                           id="emergency_contact_relation" 
                           name="emergency_contact_relation" 
                           value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                           placeholder="Contoh: Ibu, Ayah, Kakak">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informasi Medis -->
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#6B4423]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Informasi Medis
        </h3>
        
        <div class="space-y-4">
            <!-- Alergi -->
            <div>
                <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                    Alergi
                </label>
                <textarea id="allergies" 
                          name="allergies" 
                          rows="2"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                          placeholder="Contoh: Alergi obat penisilin, makanan laut">{{ old('allergies', $patient->allergies) }}</textarea>
            </div>
            
            <!-- Riwayat Penyakit -->
            <div>
                <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                    Riwayat Penyakit
                </label>
                <textarea id="medical_history" 
                          name="medical_history" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"
                          placeholder="Riwayat penyakit sebelumnya">{{ old('medical_history', $patient->medical_history) }}</textarea>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <a href="{{ route('pasien.patients.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            Batal
        </a>
        <button type="submit" class="px-6 py-3 bg-[#6B4423] text-white rounded-lg hover:bg-[#5A3A1E] transition-colors font-bold">
            Simpan Perubahan
        </button>
    </div>
    
</form>

<!-- Extra Space for Bottom Nav -->
<div class="h-20"></div>

@endsection