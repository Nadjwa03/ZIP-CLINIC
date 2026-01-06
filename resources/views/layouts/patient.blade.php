<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Patient Area' }} - Klinik ZIP</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for Modal -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Alpine.js for Modal -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Brown Theme Colors */
        :root {
            --brown-primary: #6B4423;
            --brown-dark: #5A3A1E;
            --brown-darker: #4A2F18;
        }
    </style>
</head>

<body class="bg-gray-50 pb-20">
    
    <!-- Sidebar Component -->
    @include('components.patient-sidebar')
    
    <!-- Main Content -->
    <div class="min-h-screen">
        
        <!-- Sticky Header -->
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <!-- Hamburger Menu -->
                    <button onclick="toggleSidebar()" class="text-gray-700 hover:text-[#6B4423]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Title -->
                    <h1 class="text-lg font-semibold text-gray-800">{{ $header ?? 'Dashboard' }}</h1>
                    
                    <!-- Notification Bell -->
                    <button class="relative text-gray-700 hover:text-[#6B4423]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <!-- Badge -->
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                    </button>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="px-4 py-6 pb-24">
            @yield('content')
        </main>
        
    </div>
    
    <!-- Bottom Navigation (Mobile) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="flex justify-around items-center h-16">
            <!-- Home -->
            <a href="{{ route('patient.dashboard') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('patient.dashboard') ? 'text-[#6B4423]' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('patient.dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-xs {{ request()->routeIs('patient.dashboard') ? 'font-bold' : '' }}">Home</span>
            </a>
            
            <!-- Appointments -->
            <a href="{{ route('patient.appointments.index') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('patient.appointments.*') ? 'text-[#6B4423]' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('patient.appointments.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs {{ request()->routeIs('patient.appointments.*') ? 'font-bold' : '' }}">Janji Temu</span>
            </a>
            
            <!-- Medical Records -->
            <a href="{{ route('patient.medical-records.index') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('patient.medical-records.*') ? 'text-[#6B4423]' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('patient.medical-records.*') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs {{ request()->routeIs('patient.medical-records.*') ? 'font-bold' : '' }}">Rekam Medis</span>
            </a>
            
            <!-- Profile -->
            <a href="{{ route('patient.settings') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('patient.settings') ? 'text-[#6B4423]' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="{{ request()->routeIs('patient.settings') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs {{ request()->routeIs('patient.settings') ? 'font-bold' : '' }}">Profil</span>
            </a>
        </div>
    </nav>
    
    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('patient-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('translate-x-0');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }
        
        // Close on overlay click
        document.getElementById('sidebar-overlay')?.addEventListener('click', toggleSidebar);
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('patient-sidebar');
                if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }
            }
        });
    </script>
    
    @stack('scripts')
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('patient-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    // Toggle sidebar
    sidebar.classList.toggle('-translate-x-full');
    
    // Toggle overlay
    overlay.classList.toggle('hidden');
}
</script>
</body>
</html>