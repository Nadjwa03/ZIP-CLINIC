@extends('layouts.admin')

@section('title', $serviceId ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $serviceId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
        </h1>
        <p class="text-gray-600 mt-1">
            {{ $serviceId ? 'Update informasi layanan klinik' : 'Tambahkan layanan baru untuk klinik Anda' }}
        </p>
    </div>

    @livewire('admin.service.form', ['serviceId' => $serviceId])
</div>
@endsection
