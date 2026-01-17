<div>
    <form wire:submit.prevent="save">
        <div class="bg-white shadow rounded-lg p-6">
            <!-- Speciality Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Spesialisasi <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    wire:model="speciality_name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('speciality_name') border-red-500 @enderror"
                    placeholder="Contoh: Dokter Umum, Dokter Gigi, Spesialis Anak, dll">
                @error('speciality_name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea 
                    wire:model="description"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-500 @enderror"
                    placeholder="Deskripsi singkat tentang spesialisasi ini..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Maksimal 500 karakter</p>
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model="is_active"
                        class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Aktif</span>
                </label>
                <p class="mt-1 text-xs text-gray-500">Spesialisasi aktif dapat dipilih saat menambahkan dokter</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-6 border-t">
                <a href="{{ route('admin.specialities.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    💾 {{ $specialityId ? 'Update' : 'Simpan' }}
                </button>
            </div>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-blue-800 font-medium mb-1">Tips Mengelola Spesialisasi</p>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Gunakan nama yang jelas dan mudah dipahami</li>
                    <li>• Nonaktifkan spesialisasi yang tidak terpakai</li>
                    <li>• Spesialisasi yang sudah digunakan dokter tidak dapat dihapus</li>
                    <li>• Deskripsi membantu admin lain memahami spesialisasi</li>
                </ul>
            </div>
        </div>
    </div>
</div>
