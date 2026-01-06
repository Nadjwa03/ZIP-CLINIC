<div>
    <form wire:submit.prevent="save">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Kode Layanan (Optional) -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Layanan <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>
                <input 
                    type="text" 
                    id="code"
                    wire:model="code"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('code') border-red-500 @enderror"
                    placeholder="Contoh: SRV001">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Layanan -->
            <div>
                <label for="service_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Layanan <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="service_name"
                    wire:model="service_name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('service_name') border-red-500 @enderror"
                    placeholder="Contoh: Pembersihan Karang Gigi"
                    required>
                @error('service_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Spesialisasi -->
            <div>
                <label for="speciality_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Spesialisasi <span class="text-red-500">*</span>
                </label>
                <select 
                    id="speciality_id"
                    wire:model="speciality_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('speciality_id') border-red-500 @enderror"
                    required>
                    <option value="">-- Pilih Spesialisasi --</option>
                    @foreach($specialities as $speciality)
                        <option value="{{ $speciality->speciality_id }}">
                            {{ $speciality->speciality_name }}
                        </option>
                    @endforeach
                </select>
                @error('speciality_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi <span class="text-gray-400 text-xs">(Opsional)</span>
                </label>
                <textarea 
                    id="description"
                    wire:model="description"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-500 @enderror"
                    placeholder="Deskripsi singkat layanan..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Harga -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Harga (Rp) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="price"
                    wire:model="price"
                    min="0"
                    step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('price') border-red-500 @enderror"
                    placeholder="Contoh: 150000"
                    required>
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Durasi -->
            <div>
                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                    Durasi (Menit) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="duration_minutes"
                    wire:model="duration_minutes"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('duration_minutes') border-red-500 @enderror"
                    placeholder="Contoh: 30"
                    required>
                @error('duration_minutes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Estimasi waktu layanan dalam menit</p>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="is_active"
                    wire:model="is_active"
                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Aktif (layanan dapat dipilih saat booking)
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a 
                    href="{{ route('admin.services.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    Batal
                </a>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                    {{ $isEdit ? 'Update Layanan' : 'Simpan Layanan' }}
                </button>
            </div>
        </div>
    </form>
</div>
