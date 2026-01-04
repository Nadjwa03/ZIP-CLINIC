<!--resources/views/pasien/patients/index.blade.php-->
@extends('layouts.patient')

@section('content')

<!-- Success Notification -->
@if(session('success'))
<div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 animate-slide-down">
    <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
</div>
@endif


<!-- Page Header with Back Button -->
<div class="mb-6">
    <!-- Back Button -->
    <!-- <a href="{{ route('pasien.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-4">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        <span class="text-sm font-medium">Kembali</span>
    </a> -->
    
    <!-- Title & Add Button -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('pasien.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
            <h2 class="text-xl font-bold text-gray-800">List Pasien</h2>
        </a>
        <!-- Add Patient Icon (Mobile) -->
        <!-- <a href="{{ route('pasien.patients.create') }}" class="w-12 h-12 bg-[#6B4423] text-white rounded-full flex items-center justify-center hover:bg-[#5A3A1E]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </a> -->
    </div>
</div>



<!-- Patient Cards -->
@if($patients->isNotEmpty())
<div class="space-y-4 mb-6">
    @foreach($patients as $patient)
    <div class="bg-[#F5F5DC] rounded-lg p-4 border border-gray-200">
        <div class="flex items-start justify-between">

            <!-- Patient Info -->
            <div class="flex-1">
                <!-- Name & Medical Record Number -->
                <div class="flex items-center space-x-2 mb-2">
                    <h3 class="font-bold text-gray-800">{{ $patient->full_name }}</h3>
                    @if($patient->medical_record_number)
                    <span class="text-xs bg-[#6B4423] text-white px-2 py-0.5 rounded">
                        {{ $patient->medical_record_number }}
                    </span>
                    @endif
                </div>

                <!-- Email (auto from user if empty) -->
                    <!-- <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $patient->email ?? Auth::user()->email }}</span>
                    </div> -->
                
                <!-- Contact Info -->
                <div class="space-y-1 text-sm text-gray-600">
                    @if($patient->email)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $patient->email }}</span>
                    </div>
                    @endif
                    
                    @if($patient->phone)
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>{{ $patient->phone }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Additional Info Row -->
                <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-600">
                    @if($patient->date_of_birth)
                    <div class="flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $patient->date_of_birth->format('d/m/Y') }}</span>
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->date_of_birth->age }} tahun</span>
                    </div>
                    @endif
                    
                    @if($patient->gender)
                    <div class="flex items-center space-x-1">
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    @endif
                    
                    @if($patient->blood_type)
                    <div class="flex items-center space-x-1">
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->blood_type }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Emergency Contact (if exists) -->
                @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
                <div class="mt-3 pt-3 border-t border-gray-300">
                    <p class="text-xs text-gray-500 mb-1">Kontak Darurat:</p>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ $patient->emergency_contact_name ?? '-' }}</span>
                        @if($patient->emergency_contact_phone)
                        <span class="text-gray-400">•</span>
                        <span>{{ $patient->emergency_contact_phone }}</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Edit Button -->
            <a href="{{ route('pasien.patients.edit', $patient->id) }}" 
               class="ml-4 flex-shrink-0 w-8 h-8 bg-white border border-gray-300 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#6B4423] hover:border-[#6B4423]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        </div>
    </div>
    @endforeach
</div>

<!-- Add New Patient Button with Modal -->
<div class="mb-6">
    @include('components.patient-add-modal')
</div>

@else
<!-- Empty State -->
<div class="text-center py-12">
    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Pasien</h3>
    <p class="text-gray-600 mb-6">Tambahkan profil pasien untuk mulai membuat janji temu</p>
    
    <!-- Add Patient Modal Component -->
    @include('components.patient-add-modal')
</div>
@endif

<!-- Contact Admin Button -->
<a href="https://wa.me/6281234567890?text=Halo,%20saya%20butuh%20bantuan%20untuk%20mengelola%20profil%20pasien" 
   target="_blank"
   class="block w-full bg-white border-2 border-gray-300 text-gray-700 text-center py-4 rounded-lg font-bold hover:border-[#6B4423] hover:text-[#6B4423] mb-6">
    <div class="flex items-center justify-center space-x-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <span>Hubungi Admin</span>
    </div>
</a>

<!-- Extra Space for Bottom Nav -->
<div class="h-20"></div>

@endsection

@push('scripts')
<script>
// Auto-hide success notification after 3 seconds
setTimeout(function() {
    const notification = document.querySelector('.animate-slide-down');
    if (notification) {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => notification.remove(), 500);
    }
}, 3000);
</script>

<!-- Alpine.js for Modal (if not already in layout) -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v3.x.x/dist/cdn.min.js" defer></script>
@endpush

<style>
@keyframes slide-down {
    from {
        transform: translate(-50%, -100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

.animate-slide-down {
    animation: slide-down 0.3s ease-out;
}
</style>