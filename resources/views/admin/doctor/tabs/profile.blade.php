<div class="space-y-6">
    <!-- Bio Section -->
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tentang Dokter</h3>
        @if($doctor->bio)
            <p class="text-gray-700 leading-relaxed">{{ $doctor->bio }}</p>
        @else
            <p class="text-gray-500 italic">Belum ada bio</p>
        @endif
    </div>

    <!-- Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Personal
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Nama Lengkap</span>
                    <span class="font-medium text-gray-900">{{ $doctor->user->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Nama Tampilan</span>
                    <span class="font-medium text-gray-900">{{ $doctor->display_name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Email</span>
                    <span class="font-medium text-gray-900">{{ $doctor->user->email }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Telepon</span>
                    <span class="font-medium text-gray-900">{{ $doctor->phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Informasi Profesional
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">No. Registrasi (SIP/STR)</span>
                    <span class="font-medium text-gray-900">{{ $doctor->registration_number }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Spesialisasi</span>
                    <span class="font-medium text-gray-900">{{ $doctor->speciality->speciality_name ?? 'Umum' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Status</span>
                    @if(!$doctor->deleted_at)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Tidak Aktif</span>
                    @endif
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Bergabung Sejak</span>
                    <span class="font-medium text-gray-900">{{ $doctor->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Appointments -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium mb-1">Total Appointment</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $doctor->appointments()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Schedules -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium mb-1">Jadwal Aktif</p>
                    <p class="text-3xl font-bold text-green-900">{{ $doctor->schedules()->where('is_active', true)->count() }}</p>
                    <p class="text-xs text-green-600 mt-1">hari / minggu</p>
                </div>
                <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
            <p class="text-sm text-purple-600 font-medium mb-3">Aksi Cepat</p>
            <div class="space-y-2">
                <a href="{{ route('admin.doctors.edit', $doctor->doctor_user_id) }}"
                   class="block w-full px-3 py-2 bg-white hover:bg-purple-50 text-purple-700 rounded-lg text-sm font-medium transition text-center">
                    ✏️ Edit Profil
                </a>
                <button
                    onclick="switchTab('schedule')"
                    class="block w-full px-3 py-2 bg-white hover:bg-purple-50 text-purple-700 rounded-lg text-sm font-medium transition">
                    📅 Atur Jadwal
                </button>
            </div>
        </div>
    </div>
</div>
