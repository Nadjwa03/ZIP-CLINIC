@extends('layouts.doctor')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Patient List</h2>
            <p class="text-sm text-gray-600 mt-1">
                Patients you have treated
            </p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('doctor.patients.index') }}" class="space-y-4">
            
            <!-- Search Bar -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Search by name, email, phone, or MRN..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <button type="submit" 
                        class="px-6 py-2 bg-[#6B4423] text-white rounded-lg hover:bg-[#5A3A1E] transition">
                    Search
                </button>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm text-gray-600">Status:</span>
                
                <button type="submit" 
                        name="status" 
                        value=""
                        class="px-4 py-1.5 text-sm rounded-full transition
                               {{ !$status ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All
                </button>
                
                <button type="submit" 
                        name="status" 
                        value="active"
                        class="px-4 py-1.5 text-sm rounded-full transition
                               {{ $status === 'active' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Active
                </button>
                
                <button type="submit" 
                        name="status" 
                        value="inactive"
                        class="px-4 py-1.5 text-sm rounded-full transition
                               {{ $status === 'inactive' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Inactive
                </button>

                @if($search || $status)
                    <a href="{{ route('doctor.patients.index') }}" 
                       class="ml-2 text-sm text-red-600 hover:text-red-700">
                        Clear filters
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Patient List -->
    @if($patients->count() > 0)
        <div class="space-y-3">
            @foreach($patients as $patient)
                @include('doctor.patients.partials.patient-card', ['patient' => $patient])
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $patients->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            
            @if($search || $status)
                <p class="text-gray-600 mb-4">No patients found matching your filters</p>
                <a href="{{ route('doctor.patients.index') }}" 
                   class="inline-block px-6 py-2 bg-[#6B4423] text-white rounded-lg hover:bg-[#5A3A1E] transition">
                    Clear Filters
                </a>
            @else
                <p class="text-gray-600 mb-2">You haven't treated any patients yet</p>
                <p class="text-sm text-gray-500">Patients will appear here after their appointments</p>
            @endif
        </div>
    @endif

</div>

@endsection
