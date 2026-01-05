@extends('layouts.admin')

@section('title', 'Master Data Spesialisasi - Klinik ZIP')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Master Data Spesialisasi</h1>
        <p class="text-gray-600 mt-1">Kelola data spesialisasi dokter</p>
    </div>

    @livewire('admin.speciality.index')
</div>
@endsection
