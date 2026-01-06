@extends('layouts.patient')

@section('content')

<!-- Page Title -->
<div class="mb-6">
    <a href="{{ route('patient.appointments.index') }}" class="inline-flex items-center text-[#6B4423] font-semibold mb-2 hover:underline">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Detail Janji Temu</h2>
</div>

<!-- Status Banner -->
<div class="bg-gradient-to-r 
    {{ $appointment->status == 'COMPLETED' ? 'from-green-500 to-green-600' : 
       ($appointment->status == 'CANCELLED' ? 'from-red-500 to-red-600' : 
       ($appointment->status == 'APPROVED' ? 'from-blue-500 to-blue-600' : 
       ($appointment->status == 'CHECKED_IN' ? 'from-purple-500 to-purple-600' : 
       ($appointment->status == 'IN_TREATMENT' ? 'from-amber-500 to-amber-600' : 'from-yellow-500 to-yellow-600')))) }}
    rounded-lg p-4 text-white mb-6">
    <div class="flex items-center space-x-3">
        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($appointment->status == 'COMPLETED')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @elseif($appointment->status == 'CANCELLED')
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            @else
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            @endif
        </svg>
        <div>
            <p class="font-bold text-lg">Status: {{ $appointment->status }}</p>
            <p class="text-sm opacity-90">
                @if($appointment->status == 'BOOKED')
                Menunggu konfirmasi dari klinik
                @elseif($appointment->status == 'APPROVED')
                Janji temu telah dikonfirmasi
                @elseif($appointment->status == 'CHECKED_IN')
                Anda telah check-in
                @elseif($appointment->status == 'IN_TREATMENT')
                Sedang dalam perawatan
                @elseif($appointment->status == 'COMPLETED')
                Perawatan telah selesai
                @elseif($appointment->status == 'CANCELLED')
                Janji temu dibatalkan
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Appointment Info Card -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Informasi Janji Temu
    </h3>
    
    <div class="space-y-4">
        <!-- Date & Time -->
        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
            <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                <p class="font-semibold text-gray-800">{{ $appointment->slot->slot_date->format('l, d F Y') }}</p>
                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->slot->end_time)->format('H:i') }} WIB</p>
            </div>
        </div>
        
        <!-- Service -->
        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
            <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Layanan</p>
                <p class="font-semibold text-gray-800">{{ $appointment->service->name }}</p>
                <p class="text-sm text-gray-600">Durasi: {{ $appointment->service->duration_minutes }} menit</p>
                <p class="text-sm font-semibold text-[#6B4423] mt-1">Rp {{ number_format($appointment->service->price, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <!-- Doctor -->
        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
            <div class="w-12 h-12 bg-[#6B4423] rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr($appointment->assignedDoctor->name ?? $appointment->preferredDoctor->name ?? 'D', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Dokter</p>
                <p class="font-semibold text-gray-800">{{ $appointment->assignedDoctor->name ?? $appointment->preferredDoctor->name ?? 'Belum ditentukan' }}</p>
                @if($appointment->assignedDoctor)
                <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded mt-1">Sudah Ditugaskan</span>
                @elseif($appointment->preferredDoctor)
                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded mt-1">Preferensi Anda</span>
                @else
                <span class="inline-block px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded mt-1">Akan Ditentukan Klinik</span>
                @endif
            </div>
        </div>
        
        <!-- Patient -->
        <div class="flex items-start space-x-3 pb-4 border-b border-gray-200">
            <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Pasien</p>
                <p class="font-semibold text-gray-800">{{ $appointment->patient->full_name }}</p>
                <p class="text-sm text-gray-600">MRN: {{ $appointment->patient->medical_record_number }}</p>
            </div>
        </div>
        
        <!-- Booking Source -->
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Sumber Booking</p>
                <p class="font-semibold text-gray-800">{{ $appointment->booking_source == 'WEB' ? 'Online (Website)' : 'Walk-in' }}</p>
                <p class="text-sm text-gray-600">{{ $appointment->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Complaint -->
@if($appointment->complaint)
<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
        </svg>
        Keluhan
    </h3>
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
        <p class="text-gray-700">{{ $appointment->complaint }}</p>
    </div>
</div>
@endif

<!-- Cancel Reason (if cancelled) -->
@if($appointment->status == 'CANCELLED' && $appointment->cancel_reason)
<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Alasan Pembatalan
    </h3>
    <div class="bg-red-50 border-l-4 border-red-500 p-4">
        <p class="text-gray-700">{{ $appointment->cancel_reason }}</p>
    </div>
</div>
@endif

<!-- Queue Number (if checked in) -->
@if($appointment->queue)
<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
        </svg>
        Nomor Antrian
    </h3>
    <div class="text-center bg-gradient-to-r from-[#6B4423] to-[#5A3A1E] rounded-lg p-6">
        <p class="text-white text-sm mb-2">Nomor Antrian Anda</p>
        <p class="text-white text-6xl font-bold">{{ str_pad($appointment->queue->queue_number, 3, '0', STR_PAD_LEFT) }}</p>
        <p class="text-white text-sm mt-2">Status: {{ $appointment->queue->status }}</p>
    </div>
</div>
@endif

<!-- Actions -->
<div class="space-y-3 mb-6">
    @if(in_array($appointment->status, ['BOOKED', 'APPROVED']))
    <!-- Cancel Button -->
    <button onclick="confirmCancel()" class="w-full bg-red-500 text-white py-3 rounded-lg font-bold hover:bg-red-600 flex items-center justify-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>Batalkan Janji Temu</span>
    </button>
    @endif
    
    @if($appointment->status == 'APPROVED')
    <!-- Reschedule Button -->
    <a href="{{ route('patient.appointments.create') }}" class="block w-full bg-blue-500 text-white text-center py-3 rounded-lg font-bold hover:bg-blue-600">
        Reschedule
    </a>
    @endif
    
    <!-- Back Button -->
    <a href="{{ route('patient.appointments.index') }}" class="block w-full bg-gray-200 text-gray-700 text-center py-3 rounded-lg font-bold hover:bg-gray-300">
        Kembali ke Daftar Janji Temu
    </a>
</div>

<!-- Help Section -->
<div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
    <div class="flex items-start space-x-3">
        <svg class="w-6 h-6 text-blue-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-blue-900 mb-1">Butuh bantuan?</p>
            <p class="text-sm text-blue-800 mb-2">Jika ada pertanyaan atau perlu mengubah jadwal, silakan hubungi kami:</p>
            <div class="flex flex-wrap gap-2">
                <a href="https://wa.me/6281234567890" class="inline-flex items-center px-3 py-2 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>
                <a href="tel:+6281234567890" class="inline-flex items-center px-3 py-2 bg-blue-500 text-white rounded-lg text-sm font-semibold hover:bg-blue-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    Telepon
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmCancel() {
    if (confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')) {
        const reason = prompt('Alasan pembatalan (opsional):');
        
        fetch(`/pasien/appointments/{{ $appointment->id }}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                reason: reason 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Janji temu berhasil dibatalkan');
                window.location.reload();
            } else {
                alert('Gagal membatalkan janji temu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}
</script>
@endpush