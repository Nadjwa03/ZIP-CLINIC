<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Klinik ZIP')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
    @stack('styles')
</head>

<body class="bg-gray-50">

    <!-- Sidebar (FIXED) -->
    <aside class="fixed top-0 left-0 h-screen w-56 bg-white border-r border-neutral-200 z-40">
        @include('components.admin-sidebar')
    </aside>

    <!-- Area kanan (offset dari sidebar) -->
    <div class="ml-56 min-h-screen">

        <!-- Navbar (STICKY) -->
        <div class="sticky top-0 z-30">
            @include('components.navbar')
        </div>

        <!-- Content -->
        <main class="">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
