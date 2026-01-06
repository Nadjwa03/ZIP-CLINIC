@extends('layouts.admin')

@section('title', 'Detail Dokter - ' . $doctor->display_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Dokter
        </a>
    </div>

    <!-- Doctor Profile Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start gap-6">
            <!-- Profile Photo -->
            <div class="flex-shrink-0">
                @if($doctor->photo_path)
                    <img src="{{ asset('storage/' . $doctor->photo_path) }}"
                         alt="{{ $doctor->display_name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-emerald-100">
                @else
                    <div class="w-32 h-32 rounded-full bg-emerald-100 flex items-center justify-center border-4 border-emerald-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Doctor Info -->
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $doctor->display_name }}</h1>
                        <div class="flex items-center gap-4 text-gray-600 mb-4">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ $doctor->speciality->speciality_name ?? 'Umum' }}
                            </span>
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $doctor->user->email }}
                            </span>
                            @if($doctor->phone)
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $doctor->phone }}
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if(!$doctor->deleted_at)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    ● Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                    ● Tidak Aktif
                                </span>
                            @endif
                            <span class="text-sm text-gray-500">No. Registrasi: {{ $doctor->registration_number }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button
                    onclick="switchTab('profile')"
                    id="tab-profile"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-emerald-600 text-emerald-600">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil
                </button>
                <button
                    onclick="switchTab('schedule')"
                    id="tab-schedule"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Jadwal Praktek
                </button>
                <button
                    onclick="switchTab('settings')"
                    id="tab-settings"
                    class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Profile Tab -->
            <div id="content-profile" class="tab-content">
                @include('admin.doctor.tabs.profile', ['doctor' => $doctor])
            </div>

            <!-- Schedule Tab -->
            <div id="content-schedule" class="tab-content hidden">
                @livewire('admin.doctor.schedule', ['doctorId' => $doctor->doctor_user_id])
            </div>

            <!-- Settings Tab -->
            <div id="content-settings" class="tab-content hidden">
                @include('admin.doctor.tabs.settings', ['doctor' => $doctor])
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-emerald-600', 'text-emerald-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active state to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-emerald-600', 'text-emerald-600');
}
</script>
@endsection
