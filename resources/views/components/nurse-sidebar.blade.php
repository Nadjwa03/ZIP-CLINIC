<div class="h-full w-full flex flex-col py-4">
  <!-- Logo & Title -->
  <a href="{{ route('nurse.index') }}" class="px-5">
    <h1 class="text-2xl font-semibold text-emerald-600">Klinik ZIP</h1>
    <p class="text-sm text-neutral-500">Nurse Station</p>
  </a>
  
  <!-- Navigation Menu -->
  <nav class="flex-1 flex flex-col gap-y-2 px-3 py-3 mt-3 overflow-y-auto">
    
    <!-- Treatment Room (Dashboard) -->
    <a href="{{ route('nurse.index') }}" class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('nurse.index') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('nurse.index') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm40-80h200v-80H240v80Zm0-160h200v-80H240v80Zm0-160h200v-80H240v80Zm280 320h160v-320H520v320Zm80-240v160-160Z"/>
      </svg>
      <span class="{{ request()->routeIs('nurse.index') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Treatment Room</span>
    </a>

    <!-- Divider -->
    <div class="border-t border-neutral-200 my-2"></div>
    <p class="px-3 text-xs font-semibold text-neutral-500 uppercase">Antrian</p>

    <!-- Queue Management -->
    <a href="{{ route('nurse.queue.index') }}" class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('nurse.queue.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('nurse.queue.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h640v-480H160v480Zm80-80h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Zm-80 400v-480 480Z"/>
      </svg>
      <span class="{{ request()->routeIs('nurse.queue.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Daftar Antrian</span>
    </a>

    <!-- Divider -->
    <div class="border-t border-neutral-200 my-2"></div>
    <p class="px-3 text-xs font-semibold text-neutral-500 uppercase">Quick Stats</p>

    <!-- Quick Stats Panel -->
    <div class="px-3 py-2 space-y-2">
      @php
        $todayStats = [
          'waiting' => \App\Models\Queue::whereDate('queue_date', today())->where('status', 'WAITING')->count(),
          'in_treatment' => \App\Models\Queue::whereDate('queue_date', today())->where('status', 'IN_TREATMENT')->count(),
          'done' => \App\Models\Queue::whereDate('queue_date', today())->where('status', 'DONE')->count(),
        ];
      @endphp
      
      <div class="flex justify-between items-center text-sm">
        <span class="text-neutral-600">Menunggu</span>
        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">{{ $todayStats['waiting'] }}</span>
      </div>
      <div class="flex justify-between items-center text-sm">
        <span class="text-neutral-600">Sedang Ditangani</span>
        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full font-medium">{{ $todayStats['in_treatment'] }}</span>
      </div>
      <div class="flex justify-between items-center text-sm">
        <span class="text-neutral-600">Selesai</span>
        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-medium">{{ $todayStats['done'] }}</span>
      </div>
    </div>

  </nav>

  <!-- User Info & Logout (Bottom) -->
  <div class="px-5 pb-4 flex flex-col space-y-4 border-t border-neutral-200 pt-4">
    <div class="flex items-center gap-x-3">
      <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center">
        <span class="text-pink-600 font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
      </div>
      <div>
        <p class="text-sm font-medium text-neutral-900">{{ Auth::user()->name }}</p>
        <p class="text-xs text-neutral-500">Perawat</p>
      </div>
    </div>
    <a href="{{ route('nurse.logout') }}" class="block w-full text-emerald-700 hover:text-white border border-emerald-700 hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
      Logout
    </a>
  </div>
</div>