@extends('layouts.patient')

@section('content')

<!-- Page Title -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Buat Janji Temu</h2>
    <p class="text-gray-600">Pilih waktu yang sesuai untuk Anda</p>
</div>

<!-- Booking Form -->
<form id="booking-form" method="POST" action="{{ route('pasien.appointments.store') }}">
    @csrf
    
    <!-- Step 1: Select Service -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">1</div>
            <h3 class="font-bold text-gray-800">Pilih Layanan</h3>
        </div>
        
        <select name="service_id" id="service-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent">
            <option value="">-- Pilih Layanan --</option>
            @foreach($services as $service)
            <option value="{{ $service->id }}" data-duration="{{ $service->duration_minutes }}" data-price="{{ $service->price }}">
                {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }} ({{ $service->duration_minutes }} menit)
            </option>
            @endforeach
        </select>
        
        @error('service_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Step 2: Select Doctor (Optional) -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">2</div>
            <h3 class="font-bold text-gray-800">Pilih Dokter <span class="text-sm text-gray-500 font-normal">(Opsional)</span></h3>
        </div>
        
        <div class="grid grid-cols-1 gap-3">
            @foreach($doctors as $doctor)
            <label class="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg hover:border-[#6B4423] cursor-pointer transition-all">
                <input type="radio" name="preferred_doctor_user_id" value="{{ $doctor->id }}" class="w-5 h-5 text-[#6B4423] focus:ring-[#6B4423]">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="w-12 h-12 bg-[#6B4423] rounded-full flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($doctor->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $doctor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $doctor->specialization ?? 'Dokter Gigi' }}</p>
                    </div>
                </div>
            </label>
            @endforeach
            
            <!-- No preference -->
            <label class="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg hover:border-[#6B4423] cursor-pointer transition-all">
                <input type="radio" name="preferred_doctor_user_id" value="" checked class="w-5 h-5 text-[#6B4423] focus:ring-[#6B4423]">
                <div>
                    <p class="font-semibold text-gray-800">Tidak ada preferensi</p>
                    <p class="text-sm text-gray-500">Dokter akan ditentukan oleh klinik</p>
                </div>
            </label>
        </div>
    </div>
    
    <!-- Step 3: Select Date & Time -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">3</div>
            <h3 class="font-bold text-gray-800">Pilih Tanggal & Waktu</h3>
        </div>
        
        <!-- Date Picker -->
        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal:</label>
        <input type="date" id="date-picker" name="date" 
               min="{{ date('Y-m-d') }}" 
               max="{{ date('Y-m-d', strtotime('+3 months')) }}"
               required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent mb-4">
        
        @error('date')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
        
        <!-- Time Slots -->
        <div id="time-slots-container" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Waktu:</label>
            <div id="time-slots-loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#6B4423] mx-auto"></div>
                <p class="text-gray-500 mt-3">Memuat slot waktu...</p>
            </div>
            <div id="time-slots-grid" class="grid grid-cols-3 gap-2"></div>
            <div id="time-slots-empty" class="hidden text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500">Tidak ada slot tersedia untuk tanggal ini</p>
            </div>
        </div>
        
        <input type="hidden" name="slot_id" id="slot-id" required>
        
        @error('slot_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Step 4: Complaint (Optional) -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center space-x-2 mb-3">
            <div class="w-8 h-8 bg-[#6B4423] text-white rounded-full flex items-center justify-center font-bold">4</div>
            <h3 class="font-bold text-gray-800">Keluhan <span class="text-sm text-gray-500 font-normal">(Opsional)</span></h3>
        </div>
        
        <textarea name="complaint" rows="4" placeholder="Jelaskan keluhan Anda (opsional)..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#6B4423] focus:border-transparent"></textarea>
        
        @error('complaint')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Summary (Hidden until slot selected) -->
    <div id="booking-summary" class="bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] rounded-lg p-4 text-white mb-4 hidden">
        <h3 class="font-bold mb-3">Ringkasan Booking:</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Layanan:</span>
                <span id="summary-service" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span id="summary-date" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu:</span>
                <span id="summary-time" class="font-semibold">-</span>
            </div>
            <div class="flex justify-between">
                <span>Dokter:</span>
                <span id="summary-doctor" class="font-semibold">-</span>
            </div>
            <div class="border-t border-white/30 my-2"></div>
            <div class="flex justify-between text-lg">
                <span>Estimasi Biaya:</span>
                <span id="summary-price" class="font-bold">-</span>
            </div>
        </div>
    </div>
    
    <!-- Submit Button -->
    <button type="submit" id="submit-btn" disabled class="w-full bg-gray-300 text-gray-500 py-4 rounded-lg font-bold cursor-not-allowed">
        Pilih Slot Waktu Terlebih Dahulu
    </button>
</form>

@endsection

@push('scripts')
<script>
let selectedSlot = null;
let selectedService = null;

// Date picker change
document.getElementById('date-picker').addEventListener('change', function() {
    const date = this.value;
    if (date) {
        loadTimeSlots(date);
    }
});

// Service select change
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
    }
});

// Load time slots
function loadTimeSlots(date) {
    const container = document.getElementById('time-slots-container');
    const loading = document.getElementById('time-slots-loading');
    const grid = document.getElementById('time-slots-grid');
    const empty = document.getElementById('time-slots-empty');
    
    container.classList.remove('hidden');
    loading.classList.remove('hidden');
    grid.innerHTML = '';
    empty.classList.add('hidden');
    
    // Fetch available slots
    fetch(`/pasien/appointments/slots?date=${date}`)
        .then(response => response.json())
        .then(data => {
            loading.classList.add('hidden');
            
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `py-3 px-2 border-2 rounded-lg text-sm font-semibold transition-all ${
                        slot.remaining_capacity > 0 
                        ? 'border-gray-300 text-gray-700 hover:border-[#6B4423] hover:bg-[#6B4423] hover:text-white' 
                        : 'border-gray-200 text-gray-400 cursor-not-allowed bg-gray-50'
                    }`;
                    button.textContent = slot.start_time;
                    button.disabled = slot.remaining_capacity === 0;
                    
                    if (slot.remaining_capacity > 0) {
                        button.onclick = () => selectSlot(slot, button);
                    }
                    
                    // Add capacity indicator
                    const capacitySpan = document.createElement('span');
                    capacitySpan.className = 'block text-xs mt-1';
                    capacitySpan.textContent = `(${slot.remaining_capacity} slot)`;
                    button.appendChild(capacitySpan);
                    
                    grid.appendChild(button);
                });
            } else {
                empty.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        });
}

// Select slot
function selectSlot(slot, button) {
    // Remove previous selection
    document.querySelectorAll('#time-slots-grid button').forEach(btn => {
        btn.classList.remove('border-[#6B4423]', 'bg-[#6B4423]', 'text-white');
        if (!btn.disabled) {
            btn.classList.add('border-gray-300', 'text-gray-700');
        }
    });
    
    // Add selection
    button.classList.remove('border-gray-300', 'text-gray-700');
    button.classList.add('border-[#6B4423]', 'bg-[#6B4423]', 'text-white');
    
    selectedSlot = slot;
    document.getElementById('slot-id').value = slot.id;
    
    updateSummary();
    enableSubmit();
}

// Update summary
function updateSummary() {
    if (selectedService) {
        document.getElementById('summary-service').textContent = selectedService.name;
        document.getElementById('summary-price').textContent = 'Rp ' + parseInt(selectedService.price).toLocaleString('id-ID');
    }
    
    if (selectedSlot) {
        const date = document.getElementById('date-picker').value;
        document.getElementById('summary-date').textContent = new Date(date).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('summary-time').textContent = selectedSlot.start_time;
    }
    
    const doctorRadio = document.querySelector('input[name="preferred_doctor_user_id"]:checked');
    if (doctorRadio) {
        const doctorLabel = doctorRadio.closest('label');
        const doctorName = doctorLabel.querySelector('p.font-semibold')?.textContent || 'Tidak ada preferensi';
        document.getElementById('summary-doctor').textContent = doctorName;
    }
    
    if (selectedService && selectedSlot) {
        document.getElementById('booking-summary').classList.remove('hidden');
    }
}

// Enable submit
function enableSubmit() {
    const submitBtn = document.getElementById('submit-btn');
    const serviceSelected = document.getElementById('service-select').value;
    
    if (serviceSelected && selectedSlot) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
        submitBtn.classList.add('bg-[#6B4423]', 'text-white', 'hover:bg-[#5A3A1E]', 'cursor-pointer');
        submitBtn.textContent = 'Konfirmasi Booking';
    }
}

// Update doctor in summary when changed
document.querySelectorAll('input[name="preferred_doctor_user_id"]').forEach(radio => {
    radio.addEventListener('change', updateSummary);
});
</script>
@endpush