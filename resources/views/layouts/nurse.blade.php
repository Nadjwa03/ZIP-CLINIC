<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nurse Station - Klinik ZIP')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
    @stack('styles')
</head>

<body class="bg-gray-100">

    <!-- Sidebar (FIXED) -->
    <aside class="fixed top-0 left-0 h-screen w-56 bg-white border-r border-neutral-200 z-40">
        @include('components.nurse-sidebar')
    </aside>

    <!-- Area kanan (offset dari sidebar) -->
    <div class="ml-56 min-h-screen">

        <!-- Navbar (STICKY) -->
        <div class="sticky top-0 z-30 bg-white border-b border-neutral-200">
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Treatment Room')</h1>
                    <p class="text-sm text-gray-500">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Current Time -->
                    <div class="text-right">
                        <p class="text-2xl font-bold text-emerald-600" id="current-time">--:--:--</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <main class="p-6">
            @if (session('message'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @livewireScripts
    
    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false 
            });
            document.getElementById('current-time').textContent = timeString;
        }
        
        updateClock();
        setInterval(updateClock, 1000);
    </script>

    @stack('scripts')
</body>
</html>