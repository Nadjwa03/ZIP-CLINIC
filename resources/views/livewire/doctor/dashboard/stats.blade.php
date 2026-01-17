<div class="grid grid-cols-2 md:grid-cols-4 gap-4" wire:poll.5s>
    <!-- Today Total -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['today_total'] }}</p>
                <p class="text-xs text-gray-500">Hari Ini</p>
            </div>
        </div>
    </div>

    <!-- Waiting -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['today_waiting'] }}</p>
                <p class="text-xs text-gray-500">Menunggu</p>
            </div>
        </div>
    </div>

    <!-- In Treatment -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['today_in_treatment'] }}</p>
                <p class="text-xs text-gray-500">Perawatan</p>
            </div>
        </div>
    </div>

    <!-- Completed -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['today_completed'] }}</p>
                <p class="text-xs text-gray-500">Selesai</p>
            </div>
        </div>
    </div>
</div>
