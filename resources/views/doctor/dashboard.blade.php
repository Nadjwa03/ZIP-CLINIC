@extends('layouts.doctor')

@section('content')

<!-- Welcome Section -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, Dr. {{ $doctor->user->name }}! 👋</h2>
    <p class="text-gray-600">{{ $doctor->speciality ? $doctor->speciality->speciality_name : 'Dokter' }}</p>
</div>

<!-- Quick Stats (Livewire Component) -->
<div class="mb-6">
    @livewire('doctor.dashboard.stats')
</div>

<!-- Today's Appointments & Upcoming (Livewire Component) -->
@livewire('doctor.dashboard.today-appointments')

@endsection
