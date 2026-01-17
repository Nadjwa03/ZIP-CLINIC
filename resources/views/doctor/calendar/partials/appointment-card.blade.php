@php
    // Determine color based on status
    $colors = [
        'BOOKED' => 'bg-blue-500 border-blue-600',
        'CHECKED_IN' => 'bg-yellow-500 border-yellow-600',
        'IN_TREATMENT' => 'bg-purple-500 border-purple-600',
        'COMPLETED' => 'bg-green-500 border-green-600',
        'CANCELLED' => 'bg-red-500 border-red-600',
        'NO_SHOW' => 'bg-gray-500 border-gray-600',
    ];
    
    $color = $colors[$appointment->status] ?? 'bg-gray-500 border-gray-600';
    
    // Calculate duration height MORE ACCURATELY
    $duration = $appointment->service->duration_minutes ?? 30;
    
    // Each slot is 80px tall (h-20 class = 5rem = 80px)
    // 30 minutes = 80px
    // So: height = (duration / 30) * 80
    $heightPx = ($duration / 30) * 80;
    
    // Subtract a bit for padding/margin
    $finalHeight = $heightPx - 4; // Small gap
@endphp

<div class="appointment-card absolute inset-x-1 rounded-md shadow-sm border-l-4 cursor-pointer hover:shadow-lg hover:z-30 transition-all
            {{ $color }} text-white"
     style="height: {{ $finalHeight }}px; top: 2px; z-index: 10;"
     onclick="showAppointmentDetail({{ $appointment->appointment_id }})"
     x-data="{ showTooltip: false }"
     @mouseenter="showTooltip = true"
     @mouseleave="showTooltip = false">
    
    <div class="px-2 py-1.5 h-full overflow-hidden flex flex-col justify-between">
        <!-- Patient Name -->
        <p class="text-xs font-bold truncate leading-tight">
            {{ $appointment->patient->full_name }}
        </p>
        
        <!-- Service Name (if enough space) -->
        @if($duration >= 30)
            <p class="text-xs opacity-90 truncate leading-tight">
                {{ Str::limit($appointment->service->service_name, 18) }}
            </p>
        @endif
        
        <!-- Time -->
        <p class="text-xs opacity-80 leading-tight mt-auto">
            {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }}
        </p>
    </div>

    <!-- Tooltip (on hover) -->
    <div x-show="showTooltip"
         x-transition
         class="fixed bg-gray-900 text-white text-xs rounded-lg shadow-xl p-3 w-64 z-50"
         style="display: none; margin-left: 250px; margin-top: -20px;">
        <div class="space-y-2">
            <div>
                <p class="font-semibold text-sm">{{ $appointment->patient->full_name }}</p>
                <p class="text-gray-300">{{ $appointment->service->service_name }}</p>
            </div>
            
            <div class="border-t border-gray-700 pt-2">
                <p>
                    <span class="text-gray-400">Time:</span>
                    {{ \Carbon\Carbon::parse($appointment->scheduled_start_at)->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($appointment->scheduled_end_at)->format('H:i') }}
                </p>
                <p>
                    <span class="text-gray-400">Duration:</span>
                    {{ $appointment->service->duration_minutes }} min
                </p>
                <p>
                    <span class="text-gray-400">Status:</span>
                    <span class="font-semibold">{{ ucwords(str_replace('_', ' ', strtolower($appointment->status))) }}</span>
                </p>
            </div>
            
            @if($appointment->complaint)
                <div class="border-t border-gray-700 pt-2">
                    <p class="text-gray-400 font-semibold">Complaint:</p>
                    <p class="text-yellow-300">{{ Str::limit($appointment->complaint, 100) }}</p>
                </div>
            @endif
            
            <div class="border-t border-gray-700 pt-2">
                <p class="text-xs text-gray-400 italic">Click to view full details</p>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function showAppointmentDetail(appointmentId) {
    // Redirect to appointment detail page
    window.location.href = `/doctor/appointments/${appointmentId}`;
}
</script>
@endpush
@endonce
