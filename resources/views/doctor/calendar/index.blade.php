@extends('layouts.doctor')

@section('content')

<!-- Page Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Kalender Janji Temu</h2>
    <p class="text-gray-600">Lihat jadwal mingguan Anda</p>
</div>

<!-- Calendar Component (Livewire with Auto-refresh) -->
@livewire('doctor.calendar.weekly-calendar')

@endsection
