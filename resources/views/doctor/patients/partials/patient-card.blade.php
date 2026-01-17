<a href="{{ route('doctor.patients.show', $patient->patient_id) }}" 
   class="block bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition">
    
    <div class="flex items-start gap-4">
        
        <!-- Avatar -->
        <div class="flex-shrink-0">
            <div class="w-14 h-14 rounded-full bg-[#6B4423] flex items-center justify-center">
                <span class="text-white font-semibold text-lg">
                    {{ strtoupper(substr($patient->full_name, 0, 2)) }}
                </span>
            </div>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
            <!-- Name & Status -->
            <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="text-lg font-semibold text-gray-900 truncate">
                    {{ $patient->full_name }}
                </h3>
                
                @if($patient->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-1"></span>
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-600 mr-1"></span>
                        Inactive
                    </span>
                @endif
            </div>

            <!-- Contact Info -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 mb-2">
                @if($patient->email)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $patient->email }}
                    </span>
                @endif
                
                @if($patient->phone)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $patient->phone }}
                    </span>
                @endif
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                @if($patient->last_visit)
                    <span class="text-gray-600">
                        Last visit: <span class="font-medium text-gray-900">{{ $patient->last_visit->diffForHumans() }}</span>
                    </span>
                @endif
                
                <span class="text-gray-600">
                    <span class="font-medium text-gray-900">{{ $patient->appointments_count }}</span> appointments
                </span>

                @if($patient->medical_record_number)
                    <span class="text-gray-500 text-xs">
                        MRN: {{ $patient->medical_record_number }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Arrow -->
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </div>

    </div>
</a>
