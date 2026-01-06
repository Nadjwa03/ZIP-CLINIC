@extends('layouts.patient')

@section('content')

<!-- Back Button Header -->
<div class="flex items-center mb-6">
    <a href="{{ route('patient.dashboard') }}" class="mr-3 text-gray-600 hover:text-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h2 class="text-xl font-bold text-gray-800">Klaim Profile Anda</h2>
</div>

<!-- Instructions -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-semibold mb-1">Dapatkan Patient Code dan Secret Code dari resepsionis ZIP ORTHODONTIC.</p>
            <p>Kedua kode ini diperlukan untuk mengakses profil pasien yang sudah terdaftar.</p>
        </div>
    </div>
</div>

<!-- Claim Form -->
<form method="POST" action="{{ route('patient.claim.submit') }}" class="space-y-6">
    @csrf
    
    <!-- Patient Code -->
    <div>
        <label class="block text-base font-medium text-gray-800 mb-2">Nomor Rekam Medis(MRN)</label>
        <input 
            type="text" 
            name="medical_record_number" 
            id="medical_record_number"
            value="{{ old('medical_record_number') }}" 
            placeholder="Masukkan Nomor Rekam Medis"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent text-base @error('medical_record_number') border-red-500 @enderror">
        @error('medical_record_number')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Secret Code -->
    <div>
        <label class="block text-base font-medium text-gray-800 mb-2">Secret Code</label>
        <input 
            type="password" 
            name="secret_code" 
            id="secret_code" 
            placeholder="Masukkan Secret Code"
            value="{{ old('secret_code') }}"
            required 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent text-base @error('secret_code') border-red-500 @enderror">
        @error('secret_code')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Error Message (if any) -->
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif
    
    <!-- Buttons -->
    <div class="flex space-x-3 pt-4 pb-24">
        <!-- Batal -->
        <a href="{{ route('patient.dashboard') }}" class="flex-1 bg-white border-2 border-gray-300 text-gray-700 text-center py-4 rounded-lg font-bold hover:bg-gray-50">
            Batal
        </a>
        
        <!-- Klaim Profile -->
        <button type="submit" class="flex-1 bg-[#6B4423] text-white py-4 rounded-lg font-bold hover:bg-[#5A3A1E]">
            Klaim Profile
        </button>
    </div>
</form>

<!-- Extra Space for Bottom Nav -->
<div class="h-20"></div>

<!-- Help Section -->
<div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-6">
    <p class="text-sm text-gray-600 mb-3 font-semibold">Butuh bantuan?</p>
    <div class="space-y-2">
        <a href="https://wa.me/6281234567890" class="flex items-center space-x-2 text-sm text-[#6B4423] hover:text-[#5A3A1E]">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span>Hubungi via WhatsApp</span>
        </a>
        <a href="tel:+6281234567890" class="flex items-center space-x-2 text-sm text-[#6B4423] hover:text-[#5A3A1E]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span>Telepon Klinik</span>
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v3.x.x/dist/cdn.min.js" defer></script>
@endpush