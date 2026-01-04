@extends('layouts.admin')

@section('title', 'Tambah Dokter Landing Page - Klinik ZIP')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.doctors.index') }}" 
               class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Tambah Dokter untuk Landing Page</h1>
                <p class="text-sm text-gray-600 mt-1">Tambahkan dokter baru yang akan ditampilkan di halaman utama website</p>
            </div>
        </div>

        <!-- Info Banner (Small) -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r-lg">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-blue-700">
                    <strong>Tips:</strong> Centang "Aktifkan dokter ini" agar dokter langsung muncul di landing page setelah disimpan.
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">
                
                <!-- User Account Section -->
                <div class="pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Akun Login</h2>
                            <p class="text-xs text-gray-600">Informasi akses untuk dokter masuk ke sistem</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="Dr. John Doe"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="doctor@klinik.com"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            @error('email')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   required
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            @error('password')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   required
                                   placeholder="Ulangi password"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Doctor Info Section -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Informasi Dokter</h2>
                            <p class="text-xs text-gray-600">Data yang akan ditampilkan di landing page</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Speciality -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Spesialisasi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="speciality" 
                                   value="{{ old('speciality') }}"
                                   required
                                   placeholder="Contoh: Orthodontist, Endodontist"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <p class="text-xs text-gray-500 mt-1.5">Spesialis Behel, Saluran Akar, Gigi Anak, dll.</p>
                            @error('speciality')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- License Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Lisensi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="license_number" 
                                   value="{{ old('license_number') }}"
                                   required
                                   placeholder="Contoh: STR-123456789"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors font-mono">
                            @error('license_number')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="Contoh: 08123456789"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                            <p class="text-xs text-gray-500 mt-1.5">Untuk tombol WhatsApp di landing page</p>
                            @error('phone')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Dokter
                            </label>
                            <input type="file" 
                                   name="photo" 
                                   accept="image/*"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1.5">PNG, JPG, atau WEBP (Max 2MB). Foto profesional dengan background putih lebih baik.</p>
                            @error('photo')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Biografi
                        </label>
                        <textarea name="bio" 
                                  rows="4"
                                  placeholder="Tulis biografi singkat dokter. Contoh: Dr. John Doe adalah dokter spesialis ortodonti dengan pengalaman 10 tahun..."
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none">{{ old('bio') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1.5">Bio singkat akan ditampilkan di card dokter (max 3 baris)</p>
                        @error('bio')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
                            <div class="ml-3">
                                <span class="text-sm font-medium text-gray-900">Aktifkan dokter ini di landing page</span>
                                <p class="text-xs text-gray-600 mt-1">
                                    Jika dicentang, dokter akan langsung muncul di landing page. 
                                    Jika tidak, dokter tetap tersimpan tapi tidak ditampilkan ke publik.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <a href="{{ route('admin.doctors.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Dokter
                </button>
            </div>
        </div>
    </form>
</div>
@endsection