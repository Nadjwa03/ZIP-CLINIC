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

    <!-- Schedule Form -->
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Weekly Schedule -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Jadwal Praktek Mingguan</h3>
                <p class="text-sm text-gray-600 mt-1">Atur jam praktek untuk setiap hari. Bisa menambahkan multiple shift per hari.</p>
            </div>

            <div class="p-6 space-y-6">
                @foreach($daysOfWeek as $day => $dayName)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <!-- Day Header -->
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-900 text-lg">{{ $dayName }}</h4>
                            <button
                                type="button"
                                wire:click="addShift({{ $day }})"
                                class="px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Shift
                            </button>
                        </div>

                        <!-- Shifts List -->
                        @if(count($schedules[$day]) > 0)
                            <div class="space-y-3">
                                @foreach($schedules[$day] as $index => $shift)
                                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg">
                                        <!-- Shift Number -->
                                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-semibold text-emerald-700">{{ $index + 1 }}</span>
                                        </div>

                                        <!-- Start Time -->
                                        <div class="flex-1">
                                            <label class="block text-xs text-gray-600 mb-1">Jam Mulai</label>
                                            <input
                                                type="time"
                                                wire:model="schedules.{{ $day }}.{{ $index }}.start_time"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            >
                                        </div>

                                        <!-- End Time -->
                                        <div class="flex-1">
                                            <label class="block text-xs text-gray-600 mb-1">Jam Selesai</label>
                                            <input
                                                type="time"
                                                wire:model="schedules.{{ $day }}.{{ $index }}.end_time"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            >
                                        </div>

                                        <!-- Duration Display -->
                                        <div class="flex-shrink-0 text-sm text-gray-600 min-w-[80px]">
                                            @php
                                                $start = \Carbon\Carbon::parse($shift['start_time']);
                                                $end = \Carbon\Carbon::parse($shift['end_time']);
                                                $duration = $start->diffInMinutes($end);
                                                $hours = floor($duration / 60);
                                                $minutes = $duration % 60;
                                            @endphp
                                            <span class="block text-xs text-gray-500">Durasi:</span>
                                            <span class="font-medium">{{ $hours }}j {{ $minutes }}m</span>
                                        </div>

                                        <!-- Delete Button -->
                                        <button
                                            type="button"
                                            wire:click="removeShift({{ $day }}, {{ $index }})"
                                            wire:confirm="Hapus shift ini?"
                                            class="flex-shrink-0 p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Hapus shift">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500 italic">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p>Tidak ada jadwal praktek</p>
                                <p class="text-sm mt-1">Klik "Tambah Shift" untuk menambahkan jadwal</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end items-center pt-4">
            <button
                type="submit"
                class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-2 font-medium"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Semua Jadwal
            </button>
        </div>
    </form>

    <!-- Help Text -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <h4 class="font-semibold text-blue-900 mb-2">Tips Penggunaan:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>Multiple Shift:</strong> Dokter bisa praktek di beberapa sesi dalam 1 hari (misal: pagi 08:00-12:00, sore 14:00-18:00)</li>
                    <li>• <strong>Validasi Otomatis:</strong> Sistem akan mencegah jadwal yang bertumpukan di hari yang sama</li>
                    <li>• <strong>Booking Integration:</strong> Pasien hanya bisa booking di jam praktek yang sudah ditentukan</li>
                    <li>• <strong>Editable:</strong> Jam praktek bisa diubah sewaktu-waktu sesuai kebutuhan</li>
                    <li>• <strong>No Hari Libur:</strong> Jika tidak ada shift di suatu hari, berarti dokter libur di hari tersebut</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Current Schedule Summary -->
    @php
        $totalShifts = 0;
        $activeDays = 0;
        foreach($schedules as $day => $shifts) {
            if(count($shifts) > 0) {
                $activeDays++;
                $totalShifts += count($shifts);
            }
        }
    @endphp

    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-semibold text-emerald-900 mb-1">Ringkasan Jadwal</h4>
                <p class="text-sm text-emerald-700">
                    <strong>{{ $activeDays }}</strong> hari praktek dengan total <strong>{{ $totalShifts }}</strong> shift per minggu
                </p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-emerald-600">{{ $totalShifts }}</div>
                <div class="text-xs text-emerald-700">Total Shift</div>
            </div>
        </div>
    </div>
</div>
