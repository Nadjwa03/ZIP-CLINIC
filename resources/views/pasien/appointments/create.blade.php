@extends('layouts.patient')

@section('content')

<!-- Back Button Header -->
<div class="flex items-center mb-6">
    <a href="{{ route('patient.dashboard') }}" class="mr-3 text-gray-600 hover:text-gray-800">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h2 class="text-xl font-bold text-gray-800">Buat Janji Temu</h2>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
    {{ session('error') }}
</div>
@endif

<!-- Booking Form -->
<form id="booking-form" method="POST" action="{{ route('patient.appointments.store') }}">
    @csrf

    <!-- Step 1: Select Service -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">1</div>
            <h3 class="font-bold text-gray-800">Pilih Layanan</h3>
        </div>

        <select name="service_id" id="service-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent @error('service_id') border-red-500 @enderror">
            <option value="">-- Pilih Layanan --</option>
            @foreach($services as $service)
            <option value="{{ $service->service_id }}"
                    data-duration="{{ $service->duration_minutes }}"
                    data-price="{{ $service->price }}"
                    {{ old('service_id') == $service->service_id ? 'selected' : '' }}>
                {{ $service->service_name }} - Rp {{ number_format($service->price, 0, ',', '.') }} ({{ $service->duration_minutes }} menit)
            </option>
            @endforeach
        </select>

        @error('service_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Step 2: Select Doctor (REQUIRED) -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">2</div>
            <h3 class="font-bold text-gray-800">Pilih Dokter</h3>
        </div>

        <div class="grid grid-cols-1 gap-3">
            @foreach($doctors as $doctor)
            <label class="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg hover:border-[#6B4423] cursor-pointer transition-all doctor-option">
                <input type="radio"
                       name="doctor_id"
                       value="{{ $doctor->doctor_user_id }}"
                       class="w-5 h-5 text-[#6B4423] focus:ring-[#6B4423] doctor-radio"
                       {{ old('doctor_id') == $doctor->doctor_user_id ? 'checked' : '' }}
                       required>
                <div class="flex items-center space-x-3 flex-1">
                    <div class="w-12 h-12 bg-[#6B4423] rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($doctor->user->name ?? 'D', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $doctor->user->name ?? 'Dokter' }}</p>
                        <p class="text-sm text-gray-500">{{ $doctor->specialization ?? 'Dokter Gigi' }}</p>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        @error('doctor_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Step 3: Select Date -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">3</div>
            <h3 class="font-bold text-gray-800">Pilih Tanggal</h3>
        </div>

        <input type="date"
               id="date-picker"
               name="date"
               min="{{ date('Y-m-d') }}"
               max="{{ date('Y-m-d', strtotime('+3 months')) }}"
               value="{{ old('date') }}"
               required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent @error('date') border-red-500 @enderror">

        @error('date')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Step 4: Select Time Slot -->
    <div id="time-slot-section" class="bg-white rounded-lg shadow-sm p-4 mb-4" style="display: none;">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">4</div>
            <h3 class="font-bold text-gray-800">Pilih Waktu</h3>
        </div>

        <div id="time-slots-loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#6B4423] mx-auto"></div>
            <p class="text-gray-500 mt-3">Memuat slot waktu...</p>
        </div>

        <div id="time-slots-grid" class="grid grid-cols-3 gap-2" style="display: none;"></div>

        <div id="time-slots-empty" class="text-center py-8" style="display: none;">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-500">Tidak ada slot tersedia untuk tanggal ini</p>
            <p class="text-sm text-gray-400 mt-1">Silakan pilih tanggal lain atau dokter lain</p>
        </div>

        <input type="hidden" name="start_time" id="start-time">

        @error('start_time')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Step 5: Complaint (Optional) -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">5</div>
            <h3 class="font-bold text-gray-800">Keluhan <span class="text-sm text-gray-500 font-normal">(Opsional)</span></h3>
        </div>

        <textarea name="complaint"
                  rows="4"
                  placeholder="Jelaskan keluhan Anda (opsional)..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent @error('complaint') border-red-500 @enderror">{{ old('complaint') }}</textarea>

        @error('complaint')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Summary -->
    <div id="booking-summary" class="bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] rounded-lg p-4 text-white mb-4" style="display: none;">
        <h3 class="font-bold mb-3">Ringkasan Booking:</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Layanan:</span>
                <span id="summary-service" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span>Dokter:</span>
                <span id="summary-doctor" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span id="summary-date" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu:</span>
                <span id="summary-time" class="font-semibold">-</span>
            </div>
            <div class="border-t border-white/30 my-2"></div>
            <div class="flex justify-between text-lg">
                <span>Estimasi Biaya:</span>
                <span id="summary-price" class="font-bold">-</span>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit"
            id="submit-btn"
            disabled
            class="w-full bg-gray-300 text-gray-500 py-4 rounded-lg font-bold cursor-not-allowed mb-20">
        Pilih semua informasi terlebih dahulu
    </button>
</form>

@endsection

@push('scripts')
<script>
let selectedService = null;
let selectedDoctor = null;
let selectedDate = null;
let selectedTime = null;

// Service select
document.getElementById('service-select').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value) {
        selectedService = {
            id: option.value,
            name: option.text.split(' - ')[0],
            price: option.getAttribute('data-price'),
            duration: option.getAttribute('data-duration')
        };
        updateSummary();
        loadTimeSlotsIfReady();
    }
});

// Doctor selection
document.querySelectorAll('.doctor-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            const label = this.closest('label');
            const doctorName = label.querySelector('p.font-semibold').textContent;
            selectedDoctor = {
                id: this.value,
                name: doctorName
            };

            // Highlight selected
            document.querySelectorAll('.doctor-option').forEach(opt => {
                opt.classList.remove('border-[#6B4423]', 'bg-blue-50');
            });
            label.classList.add('border-[#6B4423]', 'bg-blue-50');

            updateSummary();
            loadTimeSlotsIfReady();
        }
    });
});

// Date picker
document.getElementById('date-picker').addEventListener('change', function() {
    selectedDate = this.value;
    updateSummary();
    loadTimeSlotsIfReady();
});

// Load time slots when all required info is available
function loadTimeSlotsIfReady() {
    if (selectedService && selectedDoctor && selectedDate) {
        loadTimeSlots();
    }
}

// Load time slots from server
function loadTimeSlots() {
    const section = document.getElementById('time-slot-section');
    const loading = document.getElementById('time-slots-loading');
    const grid = document.getElementById('time-slots-grid');
    const empty = document.getElementById('time-slots-empty');

    section.style.display = 'block';
    loading.style.display = 'block';
    grid.style.display = 'none';
    grid.innerHTML = '';
    empty.style.display = 'none';

    // Fetch available slots
    fetch(`/pasien/appointments/slots?date=${selectedDate}&doctor_id=${selectedDoctor.id}&service_id=${selectedService.id}`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';

            if (data.slots && data.slots.length > 0) {
                grid.style.display = 'grid';
                data.slots.forEach(slot => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'py-3 px-2 border-2 border-gray-300 rounded-lg text-sm font-semibold hover:border-[#6B4423] hover:bg-[#6B4423] hover:text-white transition-all time-slot-btn';
                    button.textContent = slot.display;
                    button.dataset.startTime = slot.start_time;
                    button.dataset.endTime = slot.end_time;
                    button.onclick = () => selectTimeSlot(slot, button);
                    grid.appendChild(button);
                });
            } else {
                empty.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loading.style.display = 'none';
            empty.style.display = 'block';
        });
}

// Select time slot
function selectTimeSlot(slot, button) {
    // Remove previous selection
    document.querySelectorAll('.time-slot-btn').forEach(btn => {
        btn.classList.remove('border-[#6B4423]', 'bg-[#6B4423]', 'text-white');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });

    // Add selection
    button.classList.remove('border-gray-300', 'text-gray-700');
    button.classList.add('border-[#6B4423]', 'bg-[#6B4423]', 'text-white');

    selectedTime = slot;
    document.getElementById('start-time').value = slot.start_time;

    updateSummary();
    enableSubmit();
}

// Update summary
function updateSummary() {
    if (selectedService) {
        document.getElementById('summary-service').textContent = selectedService.name;
        document.getElementById('summary-price').textContent = 'Rp ' + parseInt(selectedService.price).toLocaleString('id-ID');
    }

    if (selectedDoctor) {
        document.getElementById('summary-doctor').textContent = selectedDoctor.name;
    }

    if (selectedDate) {
        document.getElementById('summary-date').textContent = new Date(selectedDate + 'T00:00:00').toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    if (selectedTime) {
        document.getElementById('summary-time').textContent = selectedTime.display;
    }

    if (selectedService && selectedDoctor && selectedDate && selectedTime) {
        document.getElementById('booking-summary').style.display = 'block';
    }
}

// Enable submit button
function enableSubmit() {
    const submitBtn = document.getElementById('submit-btn');

    if (selectedService && selectedDoctor && selectedDate && selectedTime) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        submitBtn.classList.add('bg-[#6B4423]', 'text-white', 'hover:bg-[#5A3A1E]', 'cursor-pointer');
        submitBtn.textContent = 'Konfirmasi Booking';
    }
}
</script>
@endpush
