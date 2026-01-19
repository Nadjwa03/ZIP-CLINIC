@extends('layouts.admin')

@if($mode == 'edit')
@else
@section('title', 'Tambah Dokter | Klinik ZIP Admin')
@endif
<!-- @section('content') -->

@section('body')
<main class="h-screen w-screen flex">
  <aside class="h-full w-80">
    @include('components.admin-sidebar')
  </aside>

  <div class="min-h-screen bg-neutral-100 w-full overflow-y-auto">
  <h1 class="font-medium h-16 flex items-center text-xl px-6 py-4 bg-white">
    {{ $mode == 'edit' ? 'Edit' : 'Tambah' }} Dokter
  </h1>

  <div class="flex flex-col items-center mt-8 px-6 py-6 pb-16">
    <form
  action="{{ $mode === 'edit'
      ? route('admin.doctors.edit', $data->user_id)
      : route('admin.doctors.create') }}"
  method="POST"
  enctype="multipart/form-data"
  class="flex flex-col max-w-3xl w-full bg-white rounded-lg shadow-md p-8"
>
  @csrf
  @if($mode === 'edit')
    @method('PUT')
  @endif

        <h2 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Akun</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block mb-2 text-sm font-medium text-neutral-900">Nama Lengkap <span class="text-red-600">*</span></label>
            <input value="{{ old('name', $data->user->name ?? '') }}" name="name" type="text" placeholder="Dr. John Doe" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            @error('name')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium text-neutral-900">Email <span class="text-red-600">*</span></label>
            <input value="{{ old('email', $data->user->email ?? '') }}" name="email" type="email" placeholder="doctor@klinikzip.com" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            @error('email')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
          <div>
            <label class="block mb-2 text-sm font-medium text-neutral-900">Nomor Telepon <span class="text-red-600">*</span></label>
            <input value="{{ old('phone', $data->user->phone ?? $data->phone ?? '') }}" name="phone" type="text" placeholder="081234567890" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            @error('phone')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium text-neutral-900">Password @if($mode == 'create')<span class="text-red-600">*</span>@endif</label>
            <input name="password" type="password" placeholder="{{ $mode == 'edit' ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            @error('password')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="border-t border-neutral-200 my-6"></div>
        <h2 class="text-lg font-semibold text-neutral-900 mb-4">Informasi Dokter</h2>

        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block mb-2 text-sm font-medium text-neutral-900">Nomor SIP/STR <span class="text-red-600">*</span></label>
            <input value="{{ old('registration_number', $data->registration_number ?? '') }}" name="registration_number" type="text" placeholder="SIP-001-2024" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            @error('registration_number')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="block mb-2 text-sm font-medium text-neutral-900">Nama Tampilan <span class="text-red-600">*</span></label>
            <input value="{{ old('display_name', $data->display_name ?? '') }}" name="display_name" type="text" placeholder="drg. John Doe, Sp.Ort" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            @error('display_name')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mb-4">
          <label class="block mb-2 text-sm font-medium text-neutral-900">Spesialisasi <span class="text-red-600">*</span></label>
          <input value="{{ old('speciality', $data->speciality ?? '') }}" name="speciality" type="text" placeholder="Orthodontic Specialist" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
          @error('speciality')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
          <label class="block mb-2 text-sm font-medium text-neutral-900">Bio / Profil</label>
          <textarea name="bio" rows="3" class="block p-2.5 w-full text-sm text-neutral-900 bg-neutral-50 rounded-lg border border-neutral-300 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Pengalaman, pendidikan, keahlian khusus...">{{ old('bio', $data->bio ?? '') }}</textarea>
        </div>

        <div class="mb-6">
          <label class="block mb-2 text-sm font-medium text-neutral-900">Foto Dokter</label>
          @if($mode == 'edit' && isset($data->photo_path))
          <div class="mb-3"><p class="text-sm text-neutral-600 mb-2">Foto saat ini:</p><img src="{{ asset('storage/' . $data->photo_path) }}" alt="Current photo" class="h-32 w-32 rounded-full object-cover border-2 border-neutral-300"></div>
          @endif
          <input name="photo" type="file" accept="image/*" class="block w-full text-sm text-neutral-900 border border-neutral-300 rounded-lg cursor-pointer bg-neutral-50">
          @error('photo')<div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>@enderror
          <p class="text-xs text-neutral-500 mt-1">PNG, JPG, WEBP (Max: 8MB)</p>
        </div>

        <div class="border-t border-neutral-200 my-6"></div>
        <h2 class="text-lg font-semibold text-neutral-900 mb-2">Jadwal Praktek</h2>
        <p class="text-sm text-neutral-500 mb-4">Pilih hari dan jam praktek dokter</p>

        <div id="schedules-container" class="space-y-3 mb-6">
          @php
            $days = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0];
            $existingSchedules = $mode == 'edit' && isset($data->schedules) ? $data->schedules->keyBy('day_of_week') : collect();
          @endphp
          
          @foreach($days as $dayName => $dayNum)
            @php $schedule = $existingSchedules->get($dayNum); @endphp
            <div class="flex items-center gap-4 p-3 bg-neutral-50 rounded-lg">
              <div class="w-24"><label class="text-sm font-medium text-neutral-700">{{ $dayName }}</label></div>
              <input type="time" name="schedules[{{ $dayNum }}][start_time]" value="{{ $schedule->start_time ?? '' }}" class="bg-white border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 p-2">
              <span class="text-neutral-500">-</span>
              <input type="time" name="schedules[{{ $dayNum }}][end_time]" value="{{ $schedule->end_time ?? '' }}" class="bg-white border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 p-2">
              <input type="hidden" name="schedules[{{ $dayNum }}][day_of_week]" value="{{ $dayNum }}">
            </div>
          @endforeach
        </div>

        <div class="border-t border-neutral-200 my-6"></div>
        <div class="w-full flex gap-x-4">
          <button type="submit" class="flex-1 text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">{{ $mode == 'edit' ? 'Update Dokter' : 'Simpan Dokter' }}</button>
          <a href="{{ route('admin.doctors.index') }}" class="flex-1 text-neutral-700 hover:text-white border border-neutral-700 hover:bg-neutral-700 focus:ring-4 focus:outline-none focus:ring-neutral-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Batal</a>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection