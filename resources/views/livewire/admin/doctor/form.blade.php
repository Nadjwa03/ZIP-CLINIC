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
            <!-- Section: Informasi Akun -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name"
                            wire:model="name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-500 @enderror"
                            placeholder="Contoh: dr. John Doe"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            wire:model="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('email') border-red-500 @enderror"
                            placeholder="dokter@example.com"
                            required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="phone"
                            wire:model="phone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('phone') border-red-500 @enderror"
                            placeholder="08123456789"
                            required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password @if(!$isEdit)<span class="text-red-500">*</span>@else<span class="text-gray-400 text-xs">(Kosongkan jika tidak ingin mengubah)</span>@endif
                        </label>
                        <input 
                            type="password" 
                            id="password"
                            wire:model="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('password') border-red-500 @enderror"
                            placeholder="Minimal 6 karakter"
                            @if(!$isEdit) required @endif>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password @if(!$isEdit)<span class="text-red-500">*</span>@endif
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Ulangi password"
                            @if(!$isEdit) required @endif>
                    </div>
                </div>
            </div>

            <!-- Section: Informasi Profesional -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Profesional</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Registration Number -->
                    <div>
                        <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Registrasi (SIP/STR) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="registration_number"
                            wire:model="registration_number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('registration_number') border-red-500 @enderror"
                            placeholder="Contoh: 123456789"
                            required>
                        @error('registration_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Tampilan <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="display_name"
                            wire:model="display_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('display_name') border-red-500 @enderror"
                            placeholder="Contoh: Dr. John Doe, Sp.PD"
                            required>
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Nama yang akan ditampilkan di website</p>
                    </div>

                    <!-- Speciality -->
                    <div class="md:col-span-2">
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
                </div>
            </div>

            <!-- Section: Profil -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Profil</h3>
                
                <!-- Bio -->
                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Biografi <span class="text-gray-400 text-xs">(Opsional)</span>
                    </label>
                    <textarea 
                        id="bio"
                        wire:model="bio"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('bio') border-red-500 @enderror"
                        placeholder="Ceritakan tentang pengalaman dan keahlian dokter..."></textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Profil <span class="text-gray-400 text-xs">(Opsional, Max 8MB)</span>
                    </label>
                    
                    @if($existing_photo_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $existing_photo_path) }}" alt="Current photo" class="h-24 w-24 rounded-full object-cover">
                            <p class="text-xs text-gray-500 mt-1">Foto saat ini</p>
                        </div>
                    @endif
                    
                    <input 
                        type="file" 
                        wire:model="photo"
                        accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('photo') border-red-500 @enderror">
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    @if($photo)
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Preview:</p>
                            <img src="{{ $photo->temporaryUrl() }}" class="mt-2 h-24 w-24 rounded-full object-cover">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <a 
                    href="{{ route('admin.doctors.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    Batal
                </a>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                    {{ $isEdit ? 'Update Dokter' : 'Simpan Dokter' }}
                </button>
            </div>
        </div>
    </form>
</div>
