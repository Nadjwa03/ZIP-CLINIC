@extends('layouts.admin')

@section('title', 'Master Data Layanan - Klinik ZIP')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Master Data Layanan</h1>
        <p class="text-gray-600 mt-1">Kelola layanan/service yang ditawarkan klinik</p>
    </div>

    @livewire('admin.service.index')
</div>
@endsection