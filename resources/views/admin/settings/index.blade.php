@extends('layouts.admin')

@section('title', 'Pengaturan Klinik | Klinik ZIP')

@section('content')
<div class="px-6 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Klinik</h1>
        <p class="text-gray-600 text-sm mt-1">Kelola informasi klinik untuk landing page</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="space-y-6">
            <!-- Informasi Dasar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Informasi Dasar Klinik
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Klinik -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Klinik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="clinic_name" 
                               value="{{ old('clinic_name', $settings->clinic_name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('clinic_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tagline -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tagline/Slogan
                        </label>
                        <input type="text" 
                               name="clinic_tagline" 
                               value="{{ old('clinic_tagline', $settings->clinic_tagline) }}"
                               placeholder="Senyum Sehat, Hidup Berkualitas"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Logo -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo Klinik
                        </label>
                        @if($settings->logo_path)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $settings->logo_path) }}" 
                                 alt="Current Logo" 
                                 class="h-20 object-contain">
                        </div>
                        @endif
                        <input type="file" 
                               name="logo" 
                               accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Format: PNG, JPG (Max 2MB)</p>
                    </div>
                </div>
            </div>

            <!-- Kontak & Lokasi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Kontak & Lokasi
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="clinic_address" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('clinic_address', $settings->clinic_address) }}</textarea>
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" 
                               name="clinic_phone" 
                               value="{{ old('clinic_phone', $settings->clinic_phone) }}"
                               placeholder="0411-1234567"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                        <input type="text" 
                               name="clinic_whatsapp" 
                               value="{{ old('clinic_whatsapp', $settings->clinic_whatsapp) }}"
                               placeholder="08123456789"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               name="clinic_email" 
                               value="{{ old('clinic_email', $settings->clinic_email) }}"
                               placeholder="info@klinikzip.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Google Maps Embed -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Maps Embed URL</label>
                        <input type="text" 
                               name="maps_embed_url" 
                               value="{{ old('maps_embed_url', $settings->maps_embed_url) }}"
                               placeholder="https://www.google.com/maps/embed?pb=..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Copy embed URL dari Google Maps</p>
                    </div>
                </div>
            </div>

            <!-- Tentang Klinik -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tentang Klinik
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Klinik</label>
                    <textarea name="about_text" 
                              rows="6"
                              placeholder="Tulis deskripsi singkat tentang klinik Anda..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('about_text', $settings->about_text) }}</textarea>
                </div>
            </div>

            <!-- Social Media -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                    Social Media
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Facebook -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                        <input type="url" 
                               name="facebook_url" 
                               value="{{ old('facebook_url', $settings->facebook_url) }}"
                               placeholder="https://facebook.com/klinikzip"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Instagram -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                        <input type="url" 
                               name="instagram_url" 
                               value="{{ old('instagram_url', $settings->instagram_url) }}"
                               placeholder="https://instagram.com/klinikzip"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Twitter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                        <input type="url" 
                               name="twitter_url" 
                               value="{{ old('twitter_url', $settings->twitter_url) }}"
                               placeholder="https://twitter.com/klinikzip"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- YouTube -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                        <input type="url" 
                               name="youtube_url" 
                               value="{{ old('youtube_url', $settings->youtube_url) }}"
                               placeholder="https://youtube.com/@klinikzip"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-4">
                <button type="reset" 
                        class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Reset
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    💾 Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
