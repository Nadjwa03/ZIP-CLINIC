<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-brown-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
            2
        </div>
        <div class="flex-1">
            <h3 class="text-xl font-semibold text-gray-900">Pilih Layanan</h3>
            <p class="text-sm text-gray-600">Pilih jenis perawatan yang dibutuhkan</p>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <input 
            type="text" 
            wire:model.debounce.300ms="searchTerm"
            placeholder="Cari layanan... (contoh: scaling, tambal)" 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brown-600 focus:border-transparent">
    </div>

    {{-- Category Filter --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <button 
            wire:click="clearCategoryFilter"
            class="px-4 py-2 rounded-full text-sm font-medium transition
                {{ !$selectedCategory ? 'bg-brown-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Semua
        </button>
        
        @foreach($categories as $category)
            <button 
                wire:click="filterByCategory('{{ $category }}')"
                class="px-4 py-2 rounded-full text-sm font-medium transition
                    {{ $selectedCategory === $category ? 'bg-brown-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                {{ $category }}
            </button>
        @endforeach
    </div>

    {{-- Services by Category --}}
    <div class="space-y-6 max-h-[500px] overflow-y-auto">
        @forelse($servicesByCategory as $category => $services)
            <div>
                <h4 class="font-semibold text-lg text-gray-800 mb-3 flex items-center">
                    <span class="text-2xl mr-2">{{ $services->first()->category_icon }}</span>
                    {{ $category }}
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $services->count() }})</span>
                </h4>
                
                <div class="space-y-2">
                    @foreach($services as $service)
                        <button 
                            wire:click="selectService({{ $service->service_id }})"
                            type="button"
                            class="w-full flex items-start justify-between p-4 border-2 rounded-lg cursor-pointer hover:bg-brown-50 hover:border-brown-600 transition text-left
                                {{ $serviceId == $service->service_id ? 'border-brown-600 bg-brown-50' : 'border-gray-200' }}">
                            
                            <div class="flex-1 mr-4">
                                <p class="font-semibold text-gray-900 mb-1">{{ $service->service_name }}</p>
                                @if($service->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($service->description, 100) }}</p>
                                @endif
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $service->formatted_duration }}
                                    </span>
                                    @if($service->speciality)
                                        <span>• {{ $service->speciality->speciality_name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-bold text-brown-600 text-lg">{{ $service->formatted_price }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-brown-100 text-brown-700">
                                    {{ $service->price_range }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <p class="text-gray-500">Tidak ada layanan ditemukan</p>
                @if($searchTerm)
                    <button wire:click="$set('searchTerm', '')" class="mt-2 text-brown-600 hover:underline">
                        Hapus pencarian
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Help Text --}}
    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-sm text-blue-800">
            <strong>💡 Tips:</strong> Harga yang tertera adalah estimasi. Harga final akan ditentukan setelah pemeriksaan dokter.
        </p>
    </div>
</div>
