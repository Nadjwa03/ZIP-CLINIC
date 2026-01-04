@extends('layouts.landing')
@section('content')


  
  <section class="bg-[#6B4423] text-white py-20">
  <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
    <!-- Left: Text & Stats -->
    <div>
      <h1 class="text-5xl font-bold mb-6">
        Wujudkan Senyum Sehat dan Indah Bersama Kami
      </h1>
      <p class="text-lg mb-8 text-gray-200">
        {{ $settings['clinic_description'] ?? 'Klinik ZIP Orthodontic & Dental Specialist hadir untuk memberikan perawatan gigi terbaik...' }}
      </p>
      
      <!-- Stats -->
      <div class="grid grid-cols-3 gap-6">
        <div>
          <div class="text-4xl font-bold">{{ $stats['years_experience'] }}+</div>
          <div class="text-sm">Year of Experience</div>
        </div>
        <div>
          <div class="text-4xl font-bold">{{ $stats['clinic_locations'] }}+</div>
          <div class="text-sm">Clinic Location</div>
        </div>
        <div>
          <div class="text-4xl font-bold">{{ $stats['patient_satisfaction'] }}%</div>
          <div class="text-sm">Patient Satisfaction</div>
        </div>
      </div>
    </div>
    
   <!-- Right: Doctor Images -->
<div class="relative w-full max-w-xl mx-auto md:mx-0 h-[520px] overflow-visible">
  <!-- belakang -->
  <!-- <img
    src="/images/card_drgzilal.png"
    alt="Doctor 2"
    class="absolute right-0 w-[260px] top-28 rounded-3xl shadow-2xl z-10 object-cover opacity-90"
  > -->

  <!-- depan -->
  <img
    src="/images/card_drgzilal.png"
    alt="Doctor 1"
    class="absolute right-16 top-0 w-[340px] rounded-3xl shadow-2xl z-20 object-cover"
  >
</div>


  </div>

</section>

<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center mb-4">Providing the best medical services</h2>
    <p class="text-center text-gray-600 mb-12">World-class care for everyone. Our health system offers unmatched, expert health care.</p>
    
    <div class="grid md:grid-cols-3 gap-8">
      <!-- Find a Doctor -->
      <div class="text-center p-8 hover:shadow-xl transition rounded-lg">
        <div class="w-20 h-20 mx-auto mb-4">
          <img src="/images/icon_findDoctor.png" alt="Find Doctor">
        </div>
        <h3 class="text-xl font-semibold mb-3">Find a Doctor</h3>
        <p class="text-gray-600 mb-4">World-class care for everyone. Our health system offers unmatched, expert health care. From the lab to the clinic.</p>
        <a href="{{ route('landing.doctors') }}" class="text-[#6B4423] font-semibold hover:underline">Find a Doctor →</a>
      </div>
      
      <!-- Find a Location -->
      <div class="text-center p-8 hover:shadow-xl transition rounded-lg">
        <div class="w-20 h-20 mx-auto mb-4">
          <img src="/images/icon_findLocation.png" alt="Location">
        </div>
        <h3 class="text-xl font-semibold mb-3">Find a Location</h3>
        <p class="text-gray-600 mb-4">World-class care for everyone. Our health system offers unmatched, expert health care. From the lab to the clinic.</p>
        <a href="{{ $settings['maps_url'] ?? '#' }}" target="_blank" class="text-[#6B4423] font-semibold hover:underline">Find Location →</a>
      </div>
      
      <!-- Book Appointment -->
      <div class="text-center p-8 hover:shadow-xl transition rounded-lg">
        <div class="w-20 h-20 mx-auto mb-4">
          <img src="/images/icon_bookAppointment.png" alt="Booking">
        </div>
        <h3 class="text-xl font-semibold mb-3">Book Appointment</h3>
        <p class="text-gray-600 mb-4">World-class care for everyone. Our health system offers unmatched, expert health care. From the lab to the clinic.</p>
        <a href="{{ route('patient.login') }}" class="text-[#6B4423] font-semibold hover:underline">Book Now →</a>
      </div>
    </div>
  </div>
</section>
<section class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center mb-12">Our medical services</h2>
    
    <!-- Services Grid (Dynamic from Database) -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
      @foreach($services as $service)
      <a href="#" 
         class="bg-white p-6 rounded-lg shadow hover:shadow-xl transition">
        <!-- Icon or Image -->
        @if($service->icon)
          <div class="text-5xl mb-4">{{ $service->icon }}</div>
        @elseif($service->image_path)
          <img src="{{ asset('storage/' . $service->image_path) }}" alt="{{ $service->name }}" class="w-full h-48 object-cover rounded-lg mb-4">
        @endif
        
        <!-- Service Info -->
        <h3 class="text-xl font-semibold mb-2">{{ $service->name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($service->description, 100) }}</p>
        <div class="flex justify-between items-center">
          <span class="text-[#6B4423] font-semibold">{{ $service->formatted_price }}</span>
          <span class="text-sm text-gray-500">{{ $service->duration_minutes }} min</span>
        </div>
      </a>
      @endforeach
    </div>
    
    <!-- See All Button -->
    <div class="text-center">
      <a href="{{ route('landing.services') }}" 
         class="bg-[#6B4423] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#5A3A1E] transition">
        See all services →
      </a>
    </div>
  </div>
</section>
<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center mb-4">Our great doctors</h2>
    <p class="text-center text-gray-600 mb-12">World-class care for everyone. Our health system offers unmatched, expert health care.</p>
    
    <!-- Doctors Grid -->
    <div class="grid md:grid-cols-3 gap-8">
      @foreach($doctors as $doctor)
      <div class="text-center">
        <!-- Doctor Photo -->
        <img src="{{ $doctor->photo_url }}" 
             alt="{{ $doctor->display_name }}" 
             class="w-48 h-64 object-cover rounded-2xl mx-auto mb-4 shadow-lg">
        
        <!-- Doctor Info -->
        <h3 class="text-xl font-semibold mb-2">{{ $doctor->display_name }}</h3>
        <p class="text-gray-600 mb-4">{{ $doctor->speciality }}</p>
        
        <!-- Book Button -->
        <a href="{{ route('patient.login') }}" 
           class="inline-block bg-[#6B4423] text-white px-6 py-2 rounded-full text-sm hover:bg-[#5A3A1E] transition">
          Request an Appointment
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>
<section class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center mb-4">What our patient say</h2>
    <p class="text-center text-gray-600 mb-12">World-class care for everyone. Our health system offers unmatched, expert health care.</p>
    
    <!-- Testimonials (Static for now) -->
    <div class="grid md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center mb-4">
          <img src="/images/patient-1.jpg" class="w-12 h-12 rounded-full mr-3">
          <div>
            <h4 class="font-semibold">Nail Faroozi</h4>
            <div class="text-yellow-400">⭐⭐⭐⭐⭐</div>
          </div>
        </div>
        <p class="text-gray-600 text-sm">"I have taken medical services from them. They treat so well and they are providing the best medical services."</p>
      </div>
      
      <!-- More testimonials... -->
    </div>
  </div>
</section>

@endsection