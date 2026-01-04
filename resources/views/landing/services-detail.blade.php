@extends('layouts.landing')

@section('title', $service->name . ' - Klinik ZIP')

@section('content')



{{-- BREADCRUMB --}}
<div class="bg-gray-100 py-4">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center text-sm text-gray-600">
      <a href="/" class="hover:text-[#6B4423] transition-colors">Home</a>
      <span class="mx-2">›</span>
      <a href="{{ route('landing.services') }}" class="hover:text-[#6B4423] transition-colors">Services</a>
      <span class="mx-2">›</span>
      <span class="text-[#6B4423] font-semibold">{{ $service->name }}</span>
    </div>
  </div>
</div>

{{-- SERVICE DETAIL --}}
<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid md:grid-cols-2 gap-12">
      
      {{-- Left: Image/Icon --}}
      <div class="group">
        <div class="relative overflow-hidden rounded-3xl shadow-2xl bg-gradient-to-br from-[#6B4423] to-[#5A3A1E]">
          @if($service->image_path)
            <img src="{{ asset('storage/' . $service->image_path) }}" 
                 alt="{{ $service->name }}" 
                 class="w-full h-[500px] object-cover transform group-hover:scale-110 transition-transform duration-500">
          @elseif($service->icon)
            <div class="h-[500px] flex items-center justify-center">
              <div class="text-[200px] transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
                {{ $service->icon }}
              </div>
            </div>
          @else
            <div class="h-[500px] flex items-center justify-center">
              <div class="text-white text-[200px] transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
                🦷
              </div>
            </div>
          @endif
        </div>
      </div>
      
      {{-- Right: Details --}}
      <div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ $service->name }}</h1>
        
        {{-- Price & Duration --}}
        <div class="flex items-center gap-6 mb-6">
          <div class="bg-[#6B4423] text-white px-6 py-3 rounded-full font-bold text-xl">
            {{ $service->formatted_price }}
          </div>
          <div class="flex items-center text-gray-600">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">{{ $service->duration_minutes }} minutes</span>
          </div>
        </div>
        
        {{-- Description --}}
        <div class="prose max-w-none mb-8">
          <h3 class="text-2xl font-bold text-gray-900 mb-4">Service Description</h3>
          <p class="text-gray-600 leading-relaxed text-lg">
            {{ $service->full_description ?? $service->description ?? 'Professional dental service provided by experienced specialists with state-of-the-art equipment.' }}
          </p>
        </div>
        
        {{-- Features --}}
        <div class="mb-8">
          <h3 class="text-2xl font-bold text-gray-900 mb-4">What's Included</h3>
          <ul class="space-y-3">
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Comprehensive dental examination</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Treatment by certified specialists</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Modern equipment and facilities</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Post-treatment care guidance</span>
            </li>
          </ul>
        </div>
        
        {{-- CTA Buttons --}}
        <div class="flex gap-4">
          <a href="{{ route('patient.login') }}" 
             class="flex-1 bg-[#6B4423] text-white text-center px-8 py-4 rounded-full font-bold text-lg hover:bg-[#5A3A1E] hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
            Book This Service
          </a>
          <a href="{{ route('landing.contact') }}" 
             class="flex-1 bg-white border-2 border-[#6B4423] text-[#6B4423] text-center px-8 py-4 rounded-full font-bold text-lg hover:bg-[#6B4423] hover:text-white hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
            Ask Questions
          </a>
        </div>
      </div>
      
    </div>
  </div>
</section>

{{-- RELATED SERVICES --}}
@if($relatedServices->count() > 0)
<section class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Related Services</h2>
    
    <div class="grid md:grid-cols-3 gap-8">
      @foreach($relatedServices as $related)
      <a href="{{ route('landing.service.detail', $related->id) }}" 
         class="block bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden group border border-gray-100 hover:border-[#6B4423]">
        
        <div class="relative overflow-hidden bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] h-40 flex items-center justify-center">
          @if($related->icon)
            <div class="text-7xl transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
              {{ $related->icon }}
            </div>
          @else
            <div class="text-white text-7xl transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-500">
              🦷
            </div>
          @endif
          
          <div class="absolute top-3 right-3 bg-white text-[#6B4423] px-3 py-1 rounded-full text-sm font-bold shadow-lg">
            {{ $related->formatted_price }}
          </div>
        </div>
        
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-[#6B4423] transition-colors duration-300">
            {{ $related->name }}
          </h3>
          <p class="text-gray-600 text-sm mb-3 line-clamp-2">
            {{ $related->description }}
          </p>
          <span class="text-[#6B4423] font-semibold text-sm group-hover:underline">
            View Details →
          </span>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif



@endsection