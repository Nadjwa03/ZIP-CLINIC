<div class="appointment-detail-wrapper">
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg p-4 flex items-center">
        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg p-4 flex items-center">
        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Floating notification for Livewire events -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed top-4 right-4 z-50 max-w-sm">
        <div :class="{
            'bg-green-50 border-green-500 text-green-800': type === 'success',
            'bg-red-50 border-red-500 text-red-800': type === 'error'
        }" class="border-l-4 rounded-lg p-4 shadow-lg flex items-center">
            <svg x-show="type === 'success'" class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <svg x-show="type === 'error'" class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium" x-text="message"></span>
            <button @click="show = false" class="ml-4 text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Status Janji Temu</h3>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600">Status Saat Ini:</span>
                    <span class="px-4 py-2 text-sm font-bold rounded-full
                                {{ $appointment->status == 'BOOKED' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $appointment->status == 'CHECKED_IN' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $appointment->status == 'IN_TREATMENT' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $appointment->status == 'COMPLETED' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $appointment->status == 'CANCELLED' ? 'bg-red-100 text-red-700' : '' }}">
                        @switch($appointment->status)
                            @case('BOOKED') Terjadwal @break
                            @case('CHECKED_IN') Check-in @break
                            @case('IN_TREATMENT') Perawatan @break
                            @case('COMPLETED') Selesai @break
                            @case('CANCELLED') Dibatalkan @break
                        @endswitch
                    </span>
                </div>

                <!-- Status Update Buttons -->
                @if($appointment->status !== 'COMPLETED' && $appointment->status !== 'CANCELLED')
                <div class="space-y-2">
                    <p class="text-sm text-gray-600 mb-3">Ubah Status:</p>
                    <div class="grid grid-cols-2 gap-2">
                        @if($appointment->status == 'BOOKED')
                        <button wire:click="updateStatus('CHECKED_IN')"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-medium">
                            Check-in Pasien
                        </button>
                        @endif

                        @if($appointment->status == 'CHECKED_IN' || $appointment->status == 'BOOKED')
                        <button wire:click="updateStatus('IN_TREATMENT')"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateStatus">Mulai Perawatan</span>
                            <span wire:loading wire:target="updateStatus">Loading...</span>
                        </button>
                        @endif

                        @if($appointment->status == 'IN_TREATMENT' || $appointment->status == 'CHECKED_IN')
                        <button wire:click="updateStatus('COMPLETED')"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Selesai
                        </button>
                        @endif

                        <button wire:click="updateStatus('CANCELLED')"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                            Batalkan
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Medical Record Form - SOAP Format (Only show when IN_TREATMENT or COMPLETED) -->
            @if($appointment->status == 'IN_TREATMENT' || $appointment->status == 'COMPLETED')
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📋 Rekam Medis - Format SOAP</h3>
                <p class="text-sm text-gray-600 mb-6">SOAP: Subjective, Objective, Assessment, Plan</p>

                <div>
                    <!-- S - Subjective: Keluhan Pasien -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="font-bold text-blue-600">S</span> - Subjective (Keluhan Pasien)
                        </label>
                        <textarea wire:model.defer="subjective"
                                  rows="3"
                                  placeholder="Keluhan yang disampaikan pasien..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('subjective') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- O - Objective: Hasil Pemeriksaan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="font-bold text-green-600">O</span> - Objective (Hasil Pemeriksaan)
                        </label>
                        <textarea wire:model.defer="objective"
                                  rows="4"
                                  placeholder="Hasil pemeriksaan fisik dan temuan klinis objektif..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('objective') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- A - Assessment: Diagnosa -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="font-bold text-purple-600">A</span> - Assessment (Diagnosa)
                        </label>
                        <textarea wire:model.defer="assessment"
                                  rows="3"
                                  placeholder="Diagnosa atau penilaian kondisi pasien..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('assessment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- P - Plan: Rencana Perawatan & Resep -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="font-bold text-orange-600">P</span> - Plan (Rencana Perawatan & Resep)
                        </label>
                        <textarea wire:model.defer="plan"
                                  rows="4"
                                  placeholder="Rencana tindakan, treatment, dan resep obat (nama obat, dosis, frekuensi, durasi)..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('plan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Follow-up Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kontrol (Follow-up)</label>
                        <input type="date" wire:model.defer="follow_up_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('follow_up_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes: Catatan Tambahan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan Dokter</label>
                        <textarea wire:model.defer="notes"
                                  rows="3"
                                  placeholder="Catatan atau instruksi tambahan dari dokter..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end">
                        <button type="button"
                                wire:click="saveMedicalRecord"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveMedicalRecord">💾 Simpan Data SOAP</span>
                            <span wire:loading wire:target="saveMedicalRecord">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Visit Details Form - Per-tooth Treatment (Optional) -->
            @if($visit)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">🦷 Detail Perawatan (Per Gigi)</h3>
                        <p class="text-sm text-gray-600 mt-1">Opsional - Untuk perawatan detail per gigi. Kosongkan jika hanya konsultasi.</p>
                    </div>
                    @if(!$showDetailForm)
                    <button wire:click="toggleDetailForm"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        + Tambah Detail
                    </button>
                    @endif
                </div>

                <!-- Form Input Detail Perawatan -->
                @if($showDetailForm)
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-3">Form Detail Perawatan Baru</h4>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Gigi</label>
                            <input type="text" wire:model.defer="tooth_codes"
                                   placeholder="Contoh: 11, 21, 36"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('tooth_codes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan/Service</label>
                            <select wire:model.defer="service_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($services as $service)
                                <option value="{{ $service->service_id }}">{{ $service->service_name }}</option>
                                @endforeach
                            </select>
                            @error('service_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa</label>
                            <input type="text" wire:model.defer="diagnosis_note"
                                   placeholder="Diagnosa untuk gigi ini..."
                                   maxlength="200"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('diagnosis_note') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perawatan</label>
                            <textarea wire:model.defer="treatment_note"
                                      rows="3"
                                      placeholder="Detail tindakan yang dilakukan..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('treatment_note') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                            <textarea wire:model.defer="detail_remarks"
                                      rows="2"
                                      placeholder="Catatan atau keterangan tambahan..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('detail_remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button"
                                wire:click="toggleDetailForm"
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="saveVisitDetail"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveVisitDetail">💾 Simpan Detail</span>
                            <span wire:loading wire:target="saveVisitDetail">Menyimpan...</span>
                        </button>
                    </div>
                </div>
                @endif

                <!-- Daftar Detail Perawatan -->
                @if($visit->details && $visit->details->count() > 0)
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Daftar Perawatan:</h4>
                    <div class="space-y-3">
                        @foreach($visit->details as $detail)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                @if($detail->tooth_codes)
                                <div>
                                    <span class="font-medium text-gray-600">Kode Gigi:</span>
                                    <span class="text-gray-900">{{ $detail->tooth_codes }}</span>
                                </div>
                                @endif
                                @if($detail->service)
                                <div>
                                    <span class="font-medium text-gray-600">Layanan:</span>
                                    <span class="text-gray-900">{{ $detail->service->service_name }}</span>
                                </div>
                                @endif
                                @if($detail->diagnosis_note)
                                <div class="col-span-2">
                                    <span class="font-medium text-gray-600">Diagnosa:</span>
                                    <p class="text-gray-900 mt-1">{{ $detail->diagnosis_note }}</p>
                                </div>
                                @endif
                                @if($detail->treatment_note)
                                <div class="col-span-2">
                                    <span class="font-medium text-gray-600">Catatan Perawatan:</span>
                                    <p class="text-gray-900 mt-1">{{ $detail->treatment_note }}</p>
                                </div>
                                @endif
                                @if($detail->remarks)
                                <div class="col-span-2">
                                    <span class="font-medium text-gray-600">Catatan Tambahan:</span>
                                    <p class="text-gray-900 mt-1">{{ $detail->remarks }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @elseif(!$showDetailForm)
                <div class="text-center py-8 text-gray-500">
                    <p class="mb-2">Belum ada detail perawatan yang ditambahkan.</p>
                    <p class="text-sm">Klik "Tambah Detail" untuk menambahkan detail perawatan per gigi.</p>
                </div>
                @endif
            </div>
            @endif
            @endif

            <!-- Appointment Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Janji Temu</h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-1/3 text-gray-600">Layanan:</div>
                        <div class="w-2/3 font-semibold text-gray-900">{{ $appointment->service->service_name }}</div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-1/3 text-gray-600">Tanggal:</div>
                        <div class="w-2/3 font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('l, d F Y') }}
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="w-1/3 text-gray-600">Waktu:</div>
                        <div class="w-2/3 font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }} WIB
                        </div>
                    </div>

                    @if($appointment->complaint)
                    <div class="flex items-start">
                        <div class="w-1/3 text-gray-600">Keluhan:</div>
                        <div class="w-2/3">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-gray-900">{{ $appointment->complaint }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($appointment->treatment_started_at)
                    <div class="flex items-start">
                        <div class="w-1/3 text-gray-600">Mulai Perawatan:</div>
                        <div class="w-2/3 text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->treatment_started_at)->format('d M Y, H:i') }}
                        </div>
                    </div>
                    @endif

                    @if($appointment->treatment_completed_at)
                    <div class="flex items-start">
                        <div class="w-1/3 text-gray-600">Selesai Perawatan:</div>
                        <div class="w-2/3 text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->treatment_completed_at)->format('d M Y, H:i') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Patient Info Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Pasien</h3>

                <!-- Patient Avatar -->
                <div class="flex flex-col items-center mb-4">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-3">
                        <span class="text-white font-bold text-3xl">
                            {{ strtoupper(substr($appointment->patient->full_name, 0, 1)) }}
                        </span>
                    </div>
                    <h4 class="font-bold text-gray-900 text-lg text-center">{{ $appointment->patient->full_name }}</h4>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">MRN:</span>
                        <span class="font-semibold text-gray-900">{{ $appointment->patient->medical_record_number }}</span>
                    </div>

                    @if($appointment->patient->date_of_birth)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Tanggal Lahir:</span>
                        <span class="font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->patient->date_of_birth)->format('d M Y') }}
                        </span>
                    </div>
                    @endif

                    @if($appointment->patient->gender)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Jenis Kelamin:</span>
                        <span class="font-semibold text-gray-900">
                            {{ $appointment->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>
                    @endif

                    @if($appointment->patient->phone)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Telepon:</span>
                        <a href="tel:{{ $appointment->patient->phone }}" class="font-semibold text-blue-600 hover:underline">
                            {{ $appointment->patient->phone }}
                        </a>
                    </div>
                    @endif

                    @if($appointment->patient->email)
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-600">Email:</span>
                        <a href="mailto:{{ $appointment->patient->email }}" class="font-semibold text-blue-600 hover:underline text-right break-all">
                            {{ $appointment->patient->email }}
                        </a>
                    </div>
                    @endif
                </div>

                <!-- View Full Patient Profile -->
                <a href="{{ route('doctor.patients.show', $appointment->patient->patient_id) }}"
                   class="mt-4 block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    Lihat Profil Lengkap
                </a>
            </div>

            <!-- Patient Media (Foto Gigi & X-Ray) -->
            @if($patientMedia && $patientMedia->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📸 Foto & X-Ray Pasien</h3>
                <p class="text-xs text-gray-500 mb-4">Foto yang diupload oleh admin</p>

                <div class="grid grid-cols-2 gap-3">
                    @foreach($patientMedia as $media)
                    <div class="relative group cursor-pointer">
                        <div class="aspect-square overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-500 transition">
                            @if($media->isPhoto() || $media->isXray())
                                <img src="{{ asset('storage/' . $media->path) }}"
                                     alt="{{ $media->media_type_label }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                     onclick="openImageModal('{{ asset('storage/' . $media->path) }}', '{{ $media->media_type_label }}', '{{ $media->description }}', '{{ $media->formatted_taken_at }}')">
                            @endif
                        </div>

                        <!-- Badge Type -->
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded
                                {{ $media->isPhoto() ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $media->isXray() ? 'bg-purple-100 text-purple-700' : '' }}">
                                {{ $media->isPhoto() ? '📷' : '🔬' }}
                            </span>
                        </div>

                        <!-- Info on hover -->
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2 opacity-0 group-hover:opacity-100 transition">
                            <p class="text-white text-xs font-semibold truncate">
                                {{ $media->media_type_label }}
                            </p>
                            @if($media->tooth_code)
                            <p class="text-white text-xs">Gigi: {{ $media->tooth_code }}</p>
                            @endif
                            <p class="text-white text-xs">{{ $media->formatted_taken_at }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($patientMedia->count() > 4)
                <a href="{{ route('doctor.patients.show', $appointment->patient->patient_id) }}#media"
                   class="mt-4 block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    Lihat Semua ({{ $patientMedia->count() }} foto)
                </a>
                @endif
            </div>
            @endif

        </div>

    </div>

    <!-- Image Modal for viewing photos -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl max-h-screen" onclick="event.stopPropagation()">
        <!-- Close Button -->
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold">
            ✕
        </button>

        <!-- Image -->
        <img id="modalImage" src="" alt="" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl">

        <!-- Info -->
        <div class="mt-4 bg-white rounded-lg p-4">
            <h3 id="modalTitle" class="font-bold text-gray-900 text-lg mb-2"></h3>
            <p id="modalDescription" class="text-gray-700 text-sm mb-1"></p>
            <p id="modalDate" class="text-gray-500 text-xs"></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized on appointment detail page');

        Livewire.hook('commit', ({ component, commit, respond }) => {
            console.log('Livewire commit hook fired:', {
                snapshot: commit.snapshot,
                calls: commit.calls,
                updates: commit.updates
            });
        });

        Livewire.hook('request', ({ uri, options, payload, respond }) => {
            console.log('Livewire request hook fired:', {
                uri,
                method: payload.components?.[0]?.calls?.[0]?.method,
                payload
            });
        });

        Livewire.hook('response', ({ response }) => {
            console.log('Livewire response received:', response);
        });

        Livewire.hook('commit.prepare', ({ component }) => {
            console.log('Livewire commit.prepare:', {
                id: component.id,
                name: component.name,
                data: component.canonical
            });
        });
    });

    // Catch any global errors
    window.addEventListener('error', (event) => {
        console.error('Global error caught:', {
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            error: event.error
        });
    });

    window.addEventListener('unhandledrejection', (event) => {
        console.error('Unhandled promise rejection:', {
            reason: event.reason,
            promise: event.promise
        });
    });

    // Image Modal Functions
    function openImageModal(imageUrl, title, description, date) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description || 'Tidak ada deskripsi';
        document.getElementById('modalDate').textContent = 'Diambil: ' + date;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
</div>
