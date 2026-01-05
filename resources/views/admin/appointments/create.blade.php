@extends('layouts.admin')

@section('title', 'Tambah Appointment Manual - Klinik ZIP')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Appointment Manual</h1>
            <p class="text-gray-600 mt-1">Buat appointment baru untuk pasien</p>
        </div>
        <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            ← Kembali
        </a>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.appointments.store') }}" method="POST" id="appointmentForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Patient Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pasien <span class="text-red-500">*</span>
                    </label>
                    <select name="patient_id" id="patient_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Pilih Pasien --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->patient_id }}" {{ old('patient_id', $selectedPatientId) == $patient->patient_id ? 'selected' : '' }}>
                                {{ $patient->full_name }} - {{ $patient->medical_record_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Service Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Layanan <span class="text-red-500">*</span>
                    </label>
                    <select name="service_id" id="service_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->service_id }}" 
                                    data-duration="{{ $service->duration_minutes ?? 30 }}"
                                    {{ old('service_id') == $service->service_id ? 'selected' : '' }}>
                                {{ $service->service_name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dokter <span class="text-red-500">*</span>
                    </label>
                    <select name="doctor_user_id" id="doctor_user_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Pilih Dokter --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->doctor_user_id }}" {{ old('doctor_user_id') == $doctor->doctor_user_id ? 'selected' : '' }}>
                                {{ $doctor->name }} - {{ $doctor->speciality }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Scheduled Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="scheduled_date" id="scheduled_date" required
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('scheduled_date') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Scheduled Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu <span class="text-red-500">*</span>
                    </label>
                    <select name="scheduled_time" id="scheduled_time" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Pilih tanggal dan dokter dulu --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Slot yang tersedia akan muncul setelah memilih tanggal dan dokter</p>
                </div>

                <!-- Complaint -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Keluhan Pasien
                    </label>
                    <textarea name="complaint" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Keluhan atau gejala yang dialami pasien...">{{ old('complaint') }}</textarea>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Admin
                    </label>
                    <textarea name="notes" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                              placeholder="Catatan internal untuk staff...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <a href="{{ route('admin.appointments.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                    💾 Simpan Appointment
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-blue-800 font-medium mb-1">Tips Membuat Appointment</p>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Pastikan pasien sudah terdaftar di sistem</li>
                    <li>• Pilih tanggal dan dokter untuk melihat slot waktu yang tersedia</li>
                    <li>• Appointment yang dibuat admin langsung berstatus BOOKED</li>
                    <li>• Pasien akan menerima konfirmasi appointment</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('scheduled_date');
    const doctorSelect = document.getElementById('doctor_user_id');
    const timeSelect = document.getElementById('scheduled_time');
    
    // Load available slots when date or doctor changes
    function loadAvailableSlots() {
        const date = dateInput.value;
        const doctorId = doctorSelect.value;
        
        if (!date || !doctorId) {
            timeSelect.innerHTML = '<option value="">-- Pilih tanggal dan dokter dulu --</option>';
            return;
        }
        
        // Show loading
        timeSelect.innerHTML = '<option value="">⏳ Memuat slot...</option>';
        timeSelect.disabled = true;
        
        // Fetch available slots
        fetch(`/admin/appointments/available-slots?date=${date}&doctor_id=${doctorId}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = '';
                
                if (data.slots.length === 0) {
                    timeSelect.innerHTML = '<option value="">Tidak ada slot tersedia</option>';
                } else {
                    data.slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.time;
                        option.textContent = slot.display + (slot.available ? '' : ' (Sudah dipesan)');
                        option.disabled = !slot.available;
                        timeSelect.appendChild(option);
                    });
                }
                
                timeSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading slots:', error);
                timeSelect.innerHTML = '<option value="">Error memuat slot</option>';
                timeSelect.disabled = false;
            });
    }
    
    dateInput.addEventListener('change', loadAvailableSlots);
    doctorSelect.addEventListener('change', loadAvailableSlots);
});
</script>
@endsection
