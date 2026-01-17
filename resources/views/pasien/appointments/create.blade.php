@extends('layouts.patient')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4">

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('patient.appointments.index') }}"
                   class="mr-4 p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Make Appointment</h1>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

            <form action="{{ route('patient.appointments.store') }}" method="POST" id="appointmentForm" class="space-y-6">
                @csrf

                <!-- Step 1: Pasien -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Pasien
                    </label>
                    <div class="relative">
                        <select name="patient_id" id="patientSelect"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-lg
                                       appearance-none cursor-pointer
                                       focus:border-[#6B4423] focus:ring-2 focus:ring-[#6B4423] focus:ring-opacity-20
                                       transition-colors text-gray-900"
                                required>
                            <option value="">Pilih Pasien</option>
                            @foreach($patients as $patientOption)
                                <option value="{{ $patientOption->patient_id }}"
                                        {{ $patient->patient_id == $patientOption->patient_id ? 'selected' : '' }}>
                                    {{ $patientOption->full_name }}
                                    @if($patientOption->medical_record_number) (MRN: {{ $patientOption->medical_record_number }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <!-- Dropdown Arrow -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('patient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 2: Treatment/Service -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Treatment
                    </label>
                    <div class="relative">
                        <select name="service_id" id="serviceSelect"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-lg
                                       appearance-none cursor-pointer
                                       focus:border-[#6B4423] focus:ring-2 focus:ring-[#6B4423] focus:ring-opacity-20
                                       transition-colors text-gray-900"
                                required>
                            <option value="">Pilih Treatment</option>
                            @foreach($services as $service)
                                <option value="{{ $service->service_id }}" data-duration="{{ $service->duration_minutes }}">
                                    {{ $service->service_name }} - Rp {{ number_format($service->price, 0, ',', '.') }} ({{ $service->duration_minutes }} menit)
                                </option>
                            @endforeach
                        </select>
                        <!-- Dropdown Arrow -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('service_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 3: Pilih Dokter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Pilih Dokter
                    </label>

                    @if($doctors->count() > 0)
                        <div class="relative">
                            <select name="doctor_id" id="doctorSelect"
                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-lg
                                           appearance-none cursor-pointer
                                           focus:border-[#6B4423] focus:ring-2 focus:ring-[#6B4423] focus:ring-opacity-20
                                           transition-colors text-gray-900"
                                    required>
                                <option value="">Pilih Dokter</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->doctor_user_id }}">
                                        {{ $doctor->user->name }} - {{ $doctor->speciality->speciality_name ?? 'Dokter Umum' }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Dropdown Arrow -->
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    @else
                        <div class="px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-600">Tidak ada dokter tersedia</p>
                        </div>
                    @endif
                    @error('doctor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 4: Tanggal Perawatan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Tanggal Perawatan
                    </label>

                    <!-- Date Picker -->
                    <div class="relative">
                        <input type="date"
                               name="date"
                               id="dateInput"
                               min="{{ now()->format('Y-m-d') }}"
                               value="{{ old('date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-lg
                                      focus:border-[#6B4423] focus:ring-2 focus:ring-[#6B4423] focus:ring-opacity-20
                                      transition-colors text-gray-900 cursor-pointer"
                               required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>

                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 5: Waktu Perawatan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Waktu Perawatan
                    </label>

                    <div id="timeSlotsContainer">
                        <div class="p-4 bg-gray-50 border-2 border-gray-200 rounded-lg text-center">
                            <p class="text-sm text-gray-600">Pilih dokter dan tanggal terlebih dahulu</p>
                        </div>
                    </div>

                    <input type="hidden" name="start_time" id="startTimeInput" required>

                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Step 6: Keluhan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Keluhan <span class="text-gray-500 font-normal text-xs">(optional)</span>
                    </label>
                    <textarea name="complaint"
                              rows="4"
                              placeholder="Tulis keluhan atau catatan untuk dokter..."
                              maxlength="1000"
                              class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-lg
                                     focus:border-[#6B4423] focus:ring-2 focus:ring-[#6B4423] focus:ring-opacity-20
                                     transition-colors resize-none">{{ old('complaint') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Maksimal 1000 karakter</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                            id="submitButton"
                            class="w-full px-6 py-3 bg-[#6B4423] text-white rounded-lg font-semibold
                                   hover:bg-[#5A3A1E] transition-colors shadow-lg
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   flex items-center justify-center gap-2">
                        <span>Buat Jadwal</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>

            </form>

        </div>

        <!-- Help Text -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Butuh bantuan?
                <a href="{{ route('landing.contact') }}" class="text-[#6B4423] hover:text-[#5A3A1E] font-medium">
                    Hubungi kami
                </a>
            </p>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctorSelect');
    const serviceSelect = document.getElementById('serviceSelect');
    const dateInput = document.getElementById('dateInput');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const startTimeInput = document.getElementById('startTimeInput');
    const submitButton = document.getElementById('submitButton');

    let selectedTimeSlot = null;

    // Load time slots when doctor, service, or date changes
    function loadTimeSlots() {
        const doctorId = doctorSelect.value;
        const serviceId = serviceSelect.value;
        const date = dateInput.value;

        // Reset selected time
        selectedTimeSlot = null;
        startTimeInput.value = '';

        if (!doctorId || !serviceId || !date) {
            timeSlotsContainer.innerHTML = `
                <div class="p-4 bg-gray-50 border-2 border-gray-200 rounded-lg text-center">
                    <p class="text-sm text-gray-600">Pilih dokter, treatment, dan tanggal terlebih dahulu</p>
                </div>
            `;
            return;
        }

        // Show loading
        timeSlotsContainer.innerHTML = `
            <div class="p-4 bg-gray-50 border-2 border-gray-200 rounded-lg text-center">
                <div class="animate-spin w-6 h-6 border-2 border-[#6B4423] border-t-transparent rounded-full mx-auto mb-2"></div>
                <p class="text-sm text-gray-600">Memuat jadwal tersedia...</p>
            </div>
        `;

        // Fetch available slots
        fetch(`{{ route('patient.appointments.slots') }}?doctor_id=${doctorId}&service_id=${serviceId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    timeSlotsContainer.innerHTML = `
                        <div class="p-4 bg-red-50 border-2 border-red-200 rounded-lg text-center">
                            <p class="text-sm text-red-600">${data.error}</p>
                        </div>
                    `;
                    return;
                }

                if (data.slots.length === 0) {
                    timeSlotsContainer.innerHTML = `
                        <div class="p-4 bg-yellow-50 border-2 border-yellow-200 rounded-lg text-center">
                            <p class="text-sm text-yellow-800">Tidak ada waktu tersedia untuk tanggal ini</p>
                        </div>
                    `;
                    return;
                }

                // Render time slots
                let slotsHtml = '<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">';
                data.slots.forEach(slot => {
                    slotsHtml += `
                        <button type="button"
                                data-time="${slot.start_time}"
                                class="time-slot-btn px-3 py-2 border-2 border-gray-200 rounded-lg text-sm font-medium text-center transition-all
                                       text-gray-700 hover:border-[#6B4423] hover:bg-amber-50">
                            ${slot.start_time}
                        </button>
                    `;
                });
                slotsHtml += '</div>';

                timeSlotsContainer.innerHTML = slotsHtml;

                // Add click handlers to time slot buttons
                document.querySelectorAll('.time-slot-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Remove selection from all buttons
                        document.querySelectorAll('.time-slot-btn').forEach(b => {
                            b.classList.remove('border-[#6B4423]', 'bg-[#6B4423]', 'text-white');
                            b.classList.add('border-gray-200', 'text-gray-700');
                        });

                        // Add selection to clicked button
                        this.classList.remove('border-gray-200', 'text-gray-700');
                        this.classList.add('border-[#6B4423]', 'bg-[#6B4423]', 'text-white');

                        // Store selected time

                        
                        selectedTimeSlot = this.dataset.time;
                        startTimeInput.value = selectedTimeSlot;
                    });
                });
            })
            .catch(error => {
                console.error('Error loading slots:', error);
                timeSlotsContainer.innerHTML = `
                    <div class="p-4 bg-red-50 border-2 border-red-200 rounded-lg text-center">
                        <p class="text-sm text-red-600">Gagal memuat jadwal. Silakan coba lagi.</p>
                    </div>
                `;
            });
    }

    // Event listeners
    doctorSelect.addEventListener('change', loadTimeSlots);
    serviceSelect.addEventListener('change', loadTimeSlots);
    dateInput.addEventListener('change', loadTimeSlots);

    // Form validation before submit
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        if (!startTimeInput.value) {
            e.preventDefault();
            alert('Silakan pilih waktu perawatan');
            return false;
        }
    });
});
</script>
@endpush
@endsection
