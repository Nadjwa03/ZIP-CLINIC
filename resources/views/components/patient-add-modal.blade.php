<!-- Modal: Tambah Pasien (New or Claim) -->
<div x-data="{ open: false }" x-cloak>
    <!-- Trigger Button -->
    @if(isset($iconOnly) && $iconOnly)
        <!-- Icon Only Button (Small +) -->
        <button @click="open = true" class="w-10 h-10 bg-[#6B4423] text-white rounded-full flex items-center justify-center hover:bg-[#5A3A1E] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </button>
    @else
        <!-- Full Button with Text -->
        <button @click="open = true" class="flex items-center justify-center space-x-2 w-full bg-[#6B4423] text-white py-4 rounded-lg font-bold hover:bg-[#5A3A1E] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <span>Tambah Pasien</span>
        </button>
    @endif

    <!-- Modal Overlay -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     @click.self="open = false"
     style="display: none;">
        
        <!-- Modal Content -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 relative"
             @click.away="open = false"
             @keydown.escape.window="open = false">
            
            <!-- Close Button -->
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <!-- Modal Header -->
            <h3 class="text-xl font-bold text-gray-800 mb-2">Tambah Pasien</h3>
            <p class="text-sm text-gray-600 mb-6">Pilih salah satu opsi di bawah</p>
            
            <!-- Option Buttons -->
            <div class="space-y-3">
                
                <!-- Pasien Baru -->
                <a href="{{ route('patient.patients.create') }}" 
                   class="block w-full bg-[#6B4423] text-white py-4 rounded-lg font-bold hover:bg-[#5A3A1E] transition-colors">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Pasien baru</span>
                    </div>
                </a>
                
                <!-- Pasien Lama -->
                <a href="{{ route('patient.patients.claim') }}" 
                   class="block w-full bg-white border-2 border-gray-300 text-gray-700 py-4 rounded-lg font-bold hover:border-[#6B4423] hover:text-[#6B4423] transition-colors">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Pasien Lama</span>
                    </div>
                </a>
                
            </div>
            
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>

