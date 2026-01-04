@extends('layouts.landing')

@section('title', 'Find a Doctor - Klinik ZIP')

@section('content')



{{-- HEADER SECTION --}}
<section class="bg-gradient-to-br from-[#6B4423] to-[#5A3A1E] text-white py-16">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h1 class="text-5xl font-bold mb-4">Our Great Doctors</h1>
    <p class="text-xl text-gray-200 max-w-2xl mx-auto">World-class care for everyone. Our health system offers unmatched, expert health care.</p>
  </div>
</section>

{{-- DOCTORS GRID --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if($doctors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($doctors as $doctor)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow p-6 text-center">
                <div class="w-32 h-32 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-4xl font-bold text-blue-600">{{ strtoupper(substr($doctor->name, 0, 1)) }}</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $doctor->name }}</h3>
                <p class="text-blue-600 font-medium mb-2">{{ $doctor->speciality }}</p>
                @if($doctor->bio)
                <p class="text-gray-600 text-sm">{{ Str::limit($doctor->bio, 100) }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-gray-600">Informasi dokter akan segera tersedia.</p>
        </div>
        @endif
    </div>

{{-- CTA SECTION --}}
<section class="bg-[#6B4423] text-white py-16">
  <div class="max-w-4xl mx-auto px-6 text-center">
    <h2 class="text-4xl font-bold mb-4">Ready to Book an Appointment?</h2>
    <p class="text-xl text-gray-200 mb-8">Take the first step towards a healthier, brighter smile today.</p>
    <a href="{{ route('patient.login') }}" 
       class="inline-block bg-white text-[#6B4423] px-8 py-4 rounded-full font-bold text-lg hover:bg-amber-50 hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
      Book Your Appointment Now
    </a>
  </div>
</section>



@endsection