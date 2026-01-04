@extends('layouts.admin')

@section('title', 'Dashboard Admin | Klinik ZIP')

@section('content')
<div class="px-4 py-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Selamat datang kembali, {{ auth()->user()->name }}! 👋</p>
    </div>

    {{-- Livewire Component --}}
    @livewire('admin-dashboard')
</div>
@endsection