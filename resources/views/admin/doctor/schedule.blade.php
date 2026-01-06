@extends('layouts.admin')

@section('title', 'Jadwal Dokter - ' . ($doctor->display_name ?? 'Dokter'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                @if($doctor->photo_url)
                    <img src="{{ $doctor->photo_url }}" alt="{{ $doctor->display_name }}" class="w-12 h-12 rounded-full object-cover">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Jadwal Praktek</h1>
                <p class="text-gray-600">{{ $doctor->display_name }} - {{ $doctor->speciality->speciality_name ?? 'Umum' }}</p>
            </div>
        </div>
    </div>

    @livewire('admin.doctor.schedule', ['doctorId' => $doctor->doctor_user_id])
</div>
@endsection
