@extends('layouts.doctor')

@section('content')

<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('doctor.appointments.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Janji Temu
    </a>
</div>

<!-- Page Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Detail Janji Temu</h2>
    <p class="text-gray-600">Informasi lengkap janji temu pasien</p>
</div>

@livewire('doctor.appointments.appointment-detail', ['appointmentId' => $appointment->appointment_id])

@endsection
