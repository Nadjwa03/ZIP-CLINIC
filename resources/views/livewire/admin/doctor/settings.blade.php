<div class="space-y-6">
    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Change Password Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            Ubah Password
        </h3>

        <form wire:submit.prevent="changePassword" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                <input
                    type="password"
                    wire:model="new_password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="Minimal 8 karakter"
                >
                @error('new_password')
                    <span class="text-sm text-red-600 mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <input
                    type="password"
                    wire:model="new_password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    placeholder="Ulangi password baru"
                >
            </div>

            <button
                type="submit"
                class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                Simpan Password Baru
            </button>
        </form>
    </div>

    <!-- Status Management Section -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            Status Dokter
        </h3>

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
                <p class="font-medium text-gray-900">Status Aktif</p>
                <p class="text-sm text-gray-600 mt-1">
                    @if(!$doctor->deleted_at)
                        Dokter saat ini aktif dan dapat menerima appointment
                    @else
                        Dokter saat ini tidak aktif dan tidak dapat menerima appointment
                    @endif
                </p>
            </div>
            <button
                wire:click="toggleStatus"
                wire:confirm="Yakin ingin {{ !$doctor->deleted_at ? 'menonaktifkan' : 'mengaktifkan' }} dokter ini?"
                class="px-6 py-2 {{ !$doctor->deleted_at ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-lg transition font-medium">
                {{ !$doctor->deleted_at ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
            </button>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white border border-red-300 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-red-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Zona Berbahaya
        </h3>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-red-800">
                <strong>Perhatian:</strong> Menghapus dokter akan menghapus semua data terkait termasuk jadwal praktek.
                Aksi ini tidak dapat dibatalkan!
            </p>
        </div>

        <button
            wire:click="deleteDoctor"
            wire:confirm="PERINGATAN: Menghapus dokter akan menghapus semua data terkait dan tidak dapat dikembalikan! Yakin ingin melanjutkan?"
            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
            🗑️ Hapus Dokter Permanen
        </button>
    </div>
</div>
