{{-- Hamburger Menu Button Component --}}
{{-- Usage: <x-menu-button /> --}}
{{-- Place in header to trigger sidebar --}}

<button onclick="toggleSidebar()" 
        class="p-2 hover:bg-white/10 rounded-lg transition-colors"
        aria-label="Open menu">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>