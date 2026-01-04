{{-- Navbar Component with Active State Detection --}}
<nav class="bg-[#6B4423] text-white sticky top-0 z-50 shadow-lg">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    
    {{-- Logo --}}
    <a href="/" class="flex items-center hover:opacity-80 transition-opacity">
      <div class="flex items-center space-x-3">
        <div class="text-3xl">🦷</div>
        <span class="font-bold text-xl">Klinik ZIP</span>
      </div>
    </a>
    
    {{-- Desktop Menu --}}
    <div class="hidden md:flex space-x-8">
      <a href="/" 
         class="font-medium transition-colors duration-300 {{ request()->is('/') ? 'text-amber-200 font-bold border-b-2 border-amber-200' : 'hover:text-amber-200' }}">
        Home
      </a>
      <a href="{{ route('landing.doctors') }}" 
         class="font-medium transition-colors duration-300 {{ request()->routeIs('landing.doctors') ? 'text-amber-200 font-bold border-b-2 border-amber-200' : 'hover:text-amber-200' }}">
        Find a Doctor
      </a>
      <a href="{{ route('landing.services') }}" 
         class="font-medium transition-colors duration-300 {{ request()->routeIs('landing.services*') || request()->routeIs('landing.service.*') ? 'text-amber-200 font-bold border-b-2 border-amber-200' : 'hover:text-amber-200' }}">
        Service
      </a>
      <a href="{{ route('landing.contact') }}" 
         class="font-medium transition-colors duration-300 {{ request()->routeIs('landing.contact*') ? 'text-amber-200 font-bold border-b-2 border-amber-200' : 'hover:text-amber-200' }}">
        Contact Us
      </a>
    </div>
    
    {{-- Booking Button --}}
    <a href="{{ route('patient.login') }}" 
       class="bg-white text-[#6B4423] px-6 py-2 rounded-full font-semibold hover:bg-amber-50 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
      Booking Appointment
    </a>
    
    {{-- Mobile Menu Button (Optional - untuk future enhancement) --}}
    <button class="md:hidden text-white" onclick="toggleMobileMenu()">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>
    
  </div>
  
  {{-- Mobile Menu (Hidden by default) --}}
  <div id="mobileMenu" class="hidden md:hidden bg-[#5A3A1E] px-6 py-4">
    <a href="/" class="block py-2 hover:text-amber-200 transition-colors {{ request()->is('/') ? 'text-amber-200 font-bold' : '' }}">Home</a>
    <a href="{{ route('landing.doctors') }}" class="block py-2 hover:text-amber-200 transition-colors {{ request()->routeIs('landing.doctors') ? 'text-amber-200 font-bold' : '' }}">Find a Doctor</a>
    <a href="{{ route('landing.services') }}" class="block py-2 hover:text-amber-200 transition-colors {{ request()->routeIs('landing.services*') ? 'text-amber-200 font-bold' : '' }}">Service</a>
    <a href="{{ route('landing.contact') }}" class="block py-2 hover:text-amber-200 transition-colors {{ request()->routeIs('landing.contact*') ? 'text-amber-200 font-bold' : '' }}">Contact Us</a>
    <a href="{{ route('patient.login') }}" class="block py-3 mt-3 bg-white text-[#6B4423] text-center rounded-full font-semibold">Booking Appointment</a>
  </div>
</nav>

<script>
function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  menu.classList.toggle('hidden');
}
</script>