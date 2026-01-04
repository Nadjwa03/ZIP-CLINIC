@extends('layouts.landing')

@section('title', 'Our Services - Klinik ZIP')

@section('content')

{{-- NAVBAR COMPONENT --}}
<!-- <x-landing-navbar /> -->

{{-- HEADER SECTION --}}
<section class="bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] text-white py-16">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h1 class="text-5xl font-bold mb-4">Our Medical Services</h1>
    <p class="text-xl text-gray-200 max-w-2xl mx-auto">Comprehensive dental care services provided by experienced specialists with state-of-the-art technology.</p>
  </div>
</section>

{{-- SERVICES GRID --}}
 <!-- Services Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow p-6">
                <!-- Icon/Image -->
                <div class="mb-4">
                    @if($service->image_path)
                        <img src="{{ asset('storage/' . $service->image_path) }}" 
                             alt="{{ $service->name }}"
                             class="w-full h-48 object-cover rounded-lg">
                    @elseif($service->icon)
                        <div class="text-6xl text-center">{{ $service->icon }}</div>
                    @else
                        <div class="w-full h-48 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Name -->
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $service->name }}</h3>

                <!-- Description -->
                @if($service->description)
                <p class="text-gray-600 mb-4">{{ $service->description }}</p>
                @endif

                <!-- Price -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <span class="text-2xl font-bold text-blue-600">
                        {{ $service->formatted_price }}
                    </span>
                    <a href="/contact" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                        Hubungi Kami
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Belum Ada Layanan</h3>
            <p class="text-gray-600">Layanan akan segera tersedia.</p>
        </div>
        @endif
    </div>

{{-- WHY CHOOSE US SECTION --}}
<section class="bg-gray-50 py-16">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Why Choose Our Services?</h2>
    
    <div class="grid md:grid-cols-4 gap-8">
      <div class="text-center group">
        <div class="text-5xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">👨‍⚕️</div>
        <h3 class="font-bold text-lg mb-2">Expert Specialists</h3>
        <p class="text-gray-600 text-sm">Experienced professionals in every field</p>
      </div>
      
      <div class="text-center group">
        <div class="text-5xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">🏥</div>
        <h3 class="font-bold text-lg mb-2">Modern Equipment</h3>
        <p class="text-gray-600 text-sm">State-of-the-art dental technology</p>
      </div>
      
      <div class="text-center group">
        <div class="text-5xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">💎</div>
        <h3 class="font-bold text-lg mb-2">Quality Care</h3>
        <p class="text-gray-600 text-sm">Premium service at affordable prices</p>
      </div>
      
      <div class="text-center group">
        <div class="text-5xl mb-4 transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300">😊</div>
        <h3 class="font-bold text-lg mb-2">Patient Comfort</h3>
        <p class="text-gray-600 text-sm">Comfortable and friendly environment</p>
      </div>
    </div>
  </div>
</section>

{{-- CTA SECTION --}}
<section class="bg-[#6B4423] text-white py-16">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h2 class="text-4xl font-bold mb-4">Ready to Get Started?</h2>
    <p class="text-xl text-gray-200 mb-8">Book your appointment today and experience world-class dental care.</p>
    <a href="{{ route('patient.login') }}" 
       class="inline-block bg-white text-[#6B4423] px-8 py-4 rounded-full font-bold text-lg hover:bg-amber-50 hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
      Book Appointment Now
    </a>
  </div>
</section>



@endsection