@extends('layouts.admin')

@section('title', ($specialityId ? 'Edit' : 'Tambah') . ' Spesialisasi - Klinik ZIP')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-gray-600 mt-1">{{ $specialityId ? 'Ubah data spesialisasi' : 'Tambahkan spesialisasi baru' }}</p>
        </div>
        <a href="{{ route('admin.speciality.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            ← Kembali
        </a>
    </div>

    @livewire('admin.speciality.form', ['id' => $specialityId ?? null])
</div>
@endsection
