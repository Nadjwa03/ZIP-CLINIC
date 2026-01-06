@extends('layouts.admin')

@section('title', 'Master Data Layanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Master Data Layanan</h1>
        <p class="text-gray-600 mt-1">Kelola layanan klinik Anda</p>
    </div>

    @livewire('admin.service.index')
</div>
@endsection
