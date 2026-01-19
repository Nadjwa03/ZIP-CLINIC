@extends('layouts.admin')

@if($mode == 'edit')
@section('title', 'Edit Layanan | Klinik ZIP Admin')
@else
@section('title', 'Tambah Layanan | Klinik ZIP Admin')
@endif

@section('body')
<main class="h-screen w-screen flex">
  <!-- Sidebar -->
  <aside class="h-full w-80">
    @include('partials.admin-sidebar')
  </aside>

  <!-- Main Content -->
  <div class="min-h-screen bg-neutral-100 w-full overflow-y-auto">
    <!-- Header -->
    <h1 class="font-medium h-16 flex items-center text-xl px-6 py-4 bg-white">
      {{ $mode == 'edit' ? 'Edit' : 'Tambah' }} Layanan
    </h1>

    <!-- Form Container -->
    <div class="flex flex-col items-center mt-8 px-6 py-6">
      <form 
        action="{{ $mode == 'edit' ? route('admin.service.edit', $data->id) : route('admin.service.create') }}" 
        enctype="multipart/form-data" 
        method="post" 
        class="flex flex-col items-center max-w-2xl w-full bg-white rounded-lg shadow-md p-8"
      >
        @csrf
        @if($mode == 'edit')
        @method('PUT')
        @endif

        <!-- Service Code -->
        <div class="w-full mb-4">
          <label for="code-input" class="block mb-2 text-sm font-medium text-neutral-900">
            Kode Layanan <span class="text-red-600">*</span>
          </label>
          <input 
            value="{{ old('code', $data->code ?? '') }}" 
            name="code" 
            type="text" 
            id="code-input" 
            placeholder="Contoh: SRV-001" 
            class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5"
          >
          @error('code')
          <div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>
          @enderror
          <p class="text-xs text-neutral-500 mt-1">Kode unik untuk identifikasi layanan</p>
        </div>

        <!-- Service Name -->
        <div class="w-full mb-4">
          <label for="name-input" class="block mb-2 text-sm font-medium text-neutral-900">
            Nama Layanan <span class="text-red-600">*</span>
          </label>
          <input 
            value="{{ old('name', $data->name ?? '') }}" 
            name="name" 
            type="text" 
            id="name-input" 
            placeholder="Contoh: Pembersihan Gigi (Scaling)" 
            class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5"
          >
          @error('name')
          <div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>
          @enderror
        </div>

        <!-- Price & Duration (Side by Side) -->
        <div class="w-full mb-4 grid grid-cols-2 gap-4">
          <!-- Price -->
          <div>
            <label for="price-input" class="block mb-2 text-sm font-medium text-neutral-900">
              Harga (Rp) <span class="text-red-600">*</span>
            </label>
            <input 
              value="{{ old('price', $data->price ?? '') }}" 
              name="price" 
              type="number" 
              min="0"
              step="1000"
              id="price-input" 
              placeholder="250000" 
              class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5"
            >
            @error('price')
            <div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>
            @enderror
          </div>

          <!-- Duration -->
          <div>
            <label for="duration-input" class="block mb-2 text-sm font-medium text-neutral-900">
              Durasi (Menit) <span class="text-red-600">*</span>
            </label>
            <input 
              value="{{ old('duration_minutes', $data->duration_minutes ?? '') }}" 
              name="duration_minutes" 
              type="number" 
              min="15"
              step="15"
              id="duration-input" 
              placeholder="45" 
              class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5"
            >
            @error('duration_minutes')
            <div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Icon & Sort Order -->
        <div class="w-full mb-4 grid grid-cols-2 gap-4">
          <div>
            <label for="icon-input" class="block mb-2 text-sm font-medium text-neutral-900">Icon Emoji</label>
            <input value="{{ old('icon', $data->icon ?? '') }}" name="icon" type="text" id="icon-input" placeholder="🦷" maxlength="2" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            <p class="text-xs text-neutral-500 mt-1">Emoji untuk tampilan</p>
          </div>
          <div>
            <label for="sort-input" class="block mb-2 text-sm font-medium text-neutral-900">Urutan Tampil</label>
            <input value="{{ old('sort_order', $data->sort_order ?? 0) }}" name="sort_order" type="number" min="0" id="sort-input" placeholder="0" class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            <p class="text-xs text-neutral-500 mt-1">Angka kecil tampil duluan</p>
          </div>
        </div>

        <!-- Descriptions -->
        <div class="w-full mb-4">
          <label for="description-input" class="block mb-2 text-sm font-medium text-neutral-900">Deskripsi Singkat</label>
          <textarea id="description-input" name="description" rows="2" class="block p-2.5 w-full text-sm text-neutral-900 bg-neutral-50 rounded-lg border border-neutral-300 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Deskripsi singkat untuk kartu layanan...">{{ old('description', $data->description ?? '') }}</textarea>
        </div>

        <div class="w-full mb-4">
          <label for="full-description-input" class="block mb-2 text-sm font-medium text-neutral-900">Deskripsi Lengkap</label>
          <textarea id="full-description-input" name="full_description" rows="4" class="block p-2.5 w-full text-sm text-neutral-900 bg-neutral-50 rounded-lg border border-neutral-300 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Deskripsi detail...">{{ old('full_description', $data->full_description ?? '') }}</textarea>
        </div>

        <!-- Image Upload -->
        <div class="w-full mb-4">
          <label for="image-input" class="block mb-2 text-sm font-medium text-neutral-900">Gambar Layanan</label>
          @if($mode == 'edit' && isset($data->image_path))
          <div class="mb-3">
            <p class="text-sm text-neutral-600 mb-2">Gambar saat ini:</p>
            <img src="{{ asset('storage/' . $data->image_path) }}" alt="Current image" class="h-32 w-auto rounded-lg border border-neutral-300">
          </div>
          @endif
          <input name="image" type="file" id="image-input" accept="image/*" class="block w-full text-sm text-neutral-900 border border-neutral-300 rounded-lg cursor-pointer bg-neutral-50">
          @error('image')
          <div class="text-xs text-red-600 mt-0.5">{{ $message }}</div>
          @enderror
          <p class="text-xs text-neutral-500 mt-1">PNG, JPG, WEBP (Max: 8MB)</p>
        </div>

        <!-- Buttons -->
        <div class="w-full border-t border-neutral-200 my-6"></div>
        <div class="w-full flex gap-x-4">
          <button type="submit" class="flex-1 text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
            {{ $mode == 'edit' ? 'Update Layanan' : 'Simpan Layanan' }}
          </button>
          <a href="{{ route('admin.service.index') }}" class="flex-1 text-neutral-700 hover:text-white border border-neutral-700 hover:bg-neutral-700 focus:ring-4 focus:outline-none focus:ring-neutral-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Batal</a>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection