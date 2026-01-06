@extends('layouts.admin')

@section('title', 'Master Data Dokter')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Master Data Dokter</h1>
        <p class="text-gray-600 mt-1">Kelola dokter di klinik Anda</p>
    </div>

    @livewire('admin.doctor.index')
</div>
@endsection
