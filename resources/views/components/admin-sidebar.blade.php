<div class="h-full w-full flex flex-col py-4">
  <!-- Logo & Title -->
  <a href="{{ route('admin.index') }}" class="px-5">
    <h1 class="text-2xl font-semibold text-emerald-600">Klinik ZIP</h1>
    <p class="text-sm text-neutral-500">Admin Panel</p>
  </a>
  
  <!-- Navigation Menu -->
  <nav class="flex-1 flex flex-col gap-y-2 px-3 py-3 mt-3 overflow-y-auto">
    
    <!-- Dashboard -->
    <a href="{{ route('admin.index') }}" class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.index') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.index') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M520-600v-240h320v240H520ZM120-440v-400h320v400H120Zm400 320v-400h320v400H520Zm-400 0v-240h320v240H120Zm80-400h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.index') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Dashboard</span>
    </a>

    <!-- Divider -->
    <div class="border-t border-neutral-200 my-2"></div>
    <p class="px-3 text-xs font-semibold text-neutral-500 uppercase">Landing Page</p>

    <!-- Services -->
    <a href="{{ route('admin.services.index') }}" class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.service.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.service.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M320-280q17 0 28.5-11.5T360-320q0-17-11.5-28.5T320-360q-17 0-28.5 11.5T280-320q0 17 11.5 28.5T320-280Zm0-160q17 0 28.5-11.5T360-480q0-17-11.5-28.5T320-520q-17 0-28.5 11.5T280-480q0 17 11.5 28.5T320-440Zm0-160q17 0 28.5-11.5T360-640q0-17-11.5-28.5T320-680q-17 0-28.5 11.5T280-640q0 17 11.5 28.5T320-600Zm120 320h240v-80H440v80Zm0-160h240v-80H440v80Zm0-160h240v-80H440v80ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.service.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Layanan</span>
    </a>


    <!-- Settings -->
    <a href="{{ route('admin.settings.index') }}" class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.setting.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.setting.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.setting.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Pengaturan</span>
    </a>

    <!-- Divider -->
    <div class="border-t border-neutral-200 my-2"></div>
    <p class="px-3 text-xs font-semibold text-neutral-500 uppercase">Klinik Management</p>

    <!-- Patients -->
    <a href="{{ route('admin.patients.index') }}" class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.patients.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.patients.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.patients.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Pasien</span>
    </a>

    <!-- Appointments -->
    <a href="{{ route('admin.appointments.index') }}" 
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.appointments.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.appointments.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.appointments.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Appointment</span>
    </a>

    <!-- ✨ CHECK-IN (BARU DITAMBAHKAN) -->
    <a href="{{ route('admin.checkin.index') }}" 
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.checkin.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.checkin.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.checkin.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Check-in</span>
    </a>

    <!-- Queue -->
    <a href="{{ route('admin.queue.index') }}" 
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.queue.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.queue.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h640v-480H160v480Zm80-80h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Zm-80 400v-480 480Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.queue.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Antrian</span>
    </a>

    <!-- Inventory -->
    <a href="{{ route('admin.inventory.index') }}" 
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.inventory.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.inventory.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.inventory.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Inventory</span>
    </a>

    <!-- Divider -->
    <div class="border-t border-neutral-200 my-2"></div>
    <p class="px-3 text-xs font-semibold text-neutral-500 uppercase">Master Data</p>

    <!-- Master Data Spesialisasi -->
    <a href="{{ route('admin.speciality.index') }}"
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.speciality.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.speciality.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M320-280q17 0 28.5-11.5T360-320q0-17-11.5-28.5T320-360q-17 0-28.5 11.5T280-320q0 17 11.5 28.5T320-280Zm0-160q17 0 28.5-11.5T360-480q0-17-11.5-28.5T320-520q-17 0-28.5 11.5T280-480q0 17 11.5 28.5T320-440Zm0-160q17 0 28.5-11.5T360-640q0-17-11.5-28.5T320-680q-17 0-28.5 11.5T280-640q0 17 11.5 28.5T320-600Zm120 320h240v-80H440v80Zm0-160h240v-80H440v80Zm0-160h240v-80H440v80ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.speciality.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Spesialisasi</span>
    </a>

    <!-- Master Data Layanan -->
    <a href="{{ route('admin.services.index') }}"
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.services.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.services.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M160-120q-33 0-56.5-23.5T80-200v-440q0-33 23.5-56.5T160-720h160v-80q0-33 23.5-56.5T400-880h160q33 0 56.5 23.5T640-800v80h160q33 0 56.5 23.5T880-640v440q0 33-23.5 56.5T800-120H160Zm0-80h640v-440H160v440Zm240-520h160v-80H400v80ZM160-200v-440 440Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.services.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Layanan Klinik</span>
    </a>

    <!-- Master Data Dokter -->
    <a href="{{ route('admin.doctors.index') }}"
       class="w-full flex gap-x-2 p-3 group hover:bg-emerald-50 rounded-md {{ request()->routeIs('admin.doctors.*') ? 'bg-emerald-50' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" class="fill-current {{ request()->routeIs('admin.doctors.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600 w-6 h-6" viewBox="0 -960 960 960">
        <path d="M0-240v-63q0-43 44-70t116-27q13 0 25 .5t23 2.5q-14 21-21 44t-7 48v65H0Zm240 0v-65q0-32 17.5-58.5T307-410q32-20 76.5-30t96.5-10q53 0 97.5 10t76.5 30q32 20 49 46.5t17 58.5v65H240Zm540 0v-65q0-26-6.5-49T754-397q11-2 22.5-2.5t23.5-.5q72 0 116 26.5t44 70.5v63H780Zm-455-80h311q-10-20-55.5-35T480-370q-55 0-100.5 15T325-320ZM160-440q-33 0-56.5-23.5T80-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T160-440Zm640 0q-33 0-56.5-23.5T720-520q0-34 23.5-57t56.5-23q34 0 57 23t23 57q0 33-23 56.5T800-440Zm-320-40q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-600q0 50-34.5 85T480-480Z"/>
      </svg>
      <span class="{{ request()->routeIs('admin.doctors.*') ? 'text-emerald-600' : 'text-neutral-700' }} group-hover:text-emerald-600">Dokter</span>
    </a>

  </nav>

  <!-- User Info & Logout (Bottom) -->
  <div class="px-9 pb-4 flex flex-col space-y-4 border-t border-neutral-200 pt-4">
    <div class="flex items-center gap-x-3">
      <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
        <span class="text-emerald-600 font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
      </div>
      <div>
        <p class="text-sm font-medium text-neutral-900">{{ Auth::user()->name }}</p>
        <p class="text-xs text-neutral-500">{{ Auth::user()->role }}</p>
      </div>
    </div>
    <a href="{{ route('admin.logout') }}" class="block w-full text-emerald-700 hover:text-white border border-emerald-700 hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
      Logout
    </a>
  </div>
</div>