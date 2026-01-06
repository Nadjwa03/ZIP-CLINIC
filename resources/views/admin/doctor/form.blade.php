@extends('layouts.admin')

@section('title', $doctorId ? 'Edit Dokter' : 'Tambah Dokter')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $doctorId ? 'Edit Dokter' : 'Tambah Dokter Baru' }}
        </h1>
        <p class="text-gray-600 mt-1">
            {{ $doctorId ? 'Update informasi dokter' : 'Tambahkan dokter baru untuk klinik Anda' }}
        </p>
    </div>

    @livewire('admin.doctor.form', ['doctorId' => $doctorId])
</div>
@endsection
