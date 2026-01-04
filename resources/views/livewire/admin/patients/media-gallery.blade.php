<div>
    @if(session()->has('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Header Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Category Filter -->
        <div class="flex items-center gap-2 overflow-x-auto">
            <button wire:click="$set('filterCategory', 'all')" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap {{ $filterCategory === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🗂️ Semua
            </button>
            <button wire:click="$set('filterCategory', 'photo')" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap {{ $filterCategory === 'photo' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                📷 Foto
            </button>
            <button wire:click="$set('filterCategory', 'xray')" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap {{ $filterCategory === 'xray' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🩻 X-Ray
            </button>
            <button wire:click="$set('filterCategory', 'document')" 
                    class="px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap {{ $filterCategory === 'document' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                📄 Dokumen
            </button>
        </div>

        <!-- Upload Button -->
        <button wire:click="openUploadModal" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors whitespace-nowrap">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Upload Media
        </button>
    </div>

    <!-- Media Grid -->
    @if($mediaList->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($mediaList as $media)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-shadow">
            <!-- Media Preview -->
            <div class="relative aspect-video bg-gray-100 cursor-pointer" wire:click="viewMedia({{ $media->id }})">
                @if($media->isPhoto() || $media->isXray())
                    <img src="{{ asset('storage/' . $media->path) }}" 
                         alt="{{ $media->media_type_label }}"
                         class="w-full h-full object-cover">
                @elseif($media->isDocument())
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                @endif
                
                <!-- Overlay on hover -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center">
                    <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>

                <!-- Type Badge -->
                <div class="absolute top-2 left-2">
                    @if($media->badge_color === 'blue')
                    <span class="px-2 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full">
                        📷 Foto
                    </span>
                    @elseif($media->badge_color === 'purple')
                    <span class="px-2 py-1 bg-purple-500 text-white text-xs font-semibold rounded-full">
                        🩻 X-Ray
                    </span>
                    @else
                    <span class="px-2 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full">
                        📄 Doc
                    </span>
                    @endif
                </div>

                <!-- Status Badge -->
                @if(!$media->is_active)
                <div class="absolute top-2 right-2">
                    <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                        ❌ Inactive
                    </span>
                </div>
                @endif
            </div>

            <!-- Info -->
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $media->media_type_label }}
                    </p>
                    <span class="text-xs text-gray-500">
                        {{ $media->formatted_file_size }}
                    </span>
                </div>

                <p class="text-xs text-gray-600 mb-2">
                    {{ $media->formatted_taken_at }}
                </p>

                @if($media->tooth_code)
                <div class="mb-2">
                    <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">
                        🦷 Gigi: {{ $media->tooth_code }}
                    </span>
                </div>
                @endif

                @if($media->description)
                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($media->description, 60) }}</p>
                @else
                <p class="text-sm text-gray-400 italic mb-3">Tidak ada deskripsi</p>
                @endif

                <div class="text-xs text-gray-500 mb-3">
                    Upload: {{ $media->uploader->name }}
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                    <button wire:click="viewMedia({{ $media->id }})"
                            class="flex-1 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        👁️ Lihat
                    </button>
                    <button wire:click="toggleActive({{ $media->id }})"
                            class="flex-1 px-3 py-2 text-sm {{ $media->is_active ? 'bg-green-100 hover:bg-green-200 text-green-700' : 'bg-red-100 hover:bg-red-200 text-red-700' }} rounded-lg transition-colors">
                        {{ $media->is_active ? '✅ Active' : '❌ Inactive' }}
                    </button>
                    <button wire:click="deleteMedia({{ $media->id }})"
                            onclick="return confirm('Yakin ingin menghapus media ini?')"
                            class="px-3 py-2 text-sm bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors">
                        🗑️
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        @if($filterCategory === 'all')
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada media</h3>
            <p class="text-gray-600 mb-4">Mulai dengan mengupload foto, x-ray, atau dokumen pertama</p>
        @else
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada {{ $filterCategory === 'photo' ? 'foto' : ($filterCategory === 'xray' ? 'x-ray' : 'dokumen') }}</h3>
            <p class="text-gray-600 mb-4">Filter tidak menemukan media dengan kategori ini</p>
        @endif
        <button wire:click="openUploadModal"
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Upload Media Pertama
        </button>
    </div>
    @endif

    <!-- Upload Modal -->
    @if($showUploadModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Upload Media Pasien</h3>
                <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                        <input type="file" 
                               wire:model="files" 
                               multiple 
                               accept="image/*,application/pdf"
                               class="hidden" 
                               id="fileUpload">
                        <label for="fileUpload" class="cursor-pointer">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm text-gray-600">
                                <span class="text-blue-600 font-medium">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG, PDF up to 10MB</p>
                        </label>
                    </div>
                    @error('files.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    
                    @if($files)
                    <div class="mt-3">
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ count($files) }} file dipilih</p>
                    </div>
                    @endif
                </div>

                <!-- Media Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Media <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="mediaType" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <optgroup label="📷 Foto">
                            @foreach($photoTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="🩻 X-Ray">
                            @foreach($xrayTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="📄 Dokumen">
                            @foreach($documentTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('mediaType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Tooth Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Gigi (Optional)</label>
                        <input type="text" 
                               wire:model="toothCode" 
                               placeholder="Contoh: 11, 21, 36"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Kode gigi yang terkait</p>
                    </div>

                    <!-- Taken At -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Diambil
                        </label>
                        <input type="date" 
                               wire:model="takenAt"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('takenAt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Visit Association -->
                @if($patient->visits->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kaitkan dengan Visit (Optional)</label>
                    <select wire:model="visitId" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tidak dikaitkan dengan visit tertentu</option>
                        @foreach($patient->visits->sortByDesc('visit_date') as $visit)
                        <option value="{{ $visit->id }}">
                            Visit {{ $visit->visit_date }} - {{ $visit->doctor->name ?? 'No Doctor' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Optional)</label>
                    <textarea wire:model="description" 
                              rows="3"
                              maxlength="200"
                              placeholder="Tambahkan deskripsi untuk media ini..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Maksimal 200 karakter</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200">
                <button wire:click="closeUploadModal"
                        class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button wire:click="uploadFiles"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="uploadFiles">Upload Media</span>
                    <span wire:loading wire:target="uploadFiles">Uploading...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- View Modal -->
    @if($showViewModal && $selectedMedia)
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" 
         wire:click="closeViewModal">
        <div class="max-w-4xl w-full" wire:click.stop>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <!-- Media Display -->
                <div class="bg-black flex items-center justify-center" style="max-height: 70vh;">
                    @if($selectedMedia->isPhoto() || $selectedMedia->isXray())
                        <img src="{{ asset('storage/' . $selectedMedia->path) }}" 
                             alt="{{ $selectedMedia->media_type_label }}"
                             class="max-w-full max-h-full object-contain">
                    @elseif($selectedMedia->isDocument())
                        <div class="p-12 text-center">
                            <svg class="w-32 h-32 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-white text-lg mb-4">{{ $selectedMedia->original_name }}</p>
                            <a href="{{ asset('storage/' . $selectedMedia->path) }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-white text-gray-900 rounded-lg hover:bg-gray-100 transition-colors">
                                📥 Download Dokumen
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $selectedMedia->media_type_label }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $selectedMedia->formatted_taken_at }} • {{ $selectedMedia->formatted_file_size }}
                            </p>
                            @if($selectedMedia->tooth_code)
                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded mt-2">
                                🦷 Gigi: {{ $selectedMedia->tooth_code }}
                            </span>
                            @endif
                            @if($selectedMedia->visit)
                            <p class="text-sm text-gray-600 mt-2">
                                📅 Visit: {{ $selectedMedia->visit->visit_date }}
                            </p>
                            @endif
                        </div>
                        <button wire:click="closeViewModal"
                                class="text-gray-400 hover:text-gray-600 ml-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    @if($selectedMedia->description)
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-700">{{ $selectedMedia->description }}</p>
                    </div>
                    @endif

                    <div class="text-sm text-gray-600 mb-4">
                        Upload oleh: {{ $selectedMedia->uploader->name }} • {{ $selectedMedia->created_at->format('d M Y H:i') }}
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="toggleActive({{ $selectedMedia->id }})"
                                class="px-4 py-2 {{ $selectedMedia->is_active ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg transition-colors">
                            {{ $selectedMedia->is_active ? '✅ Media Aktif' : '❌ Media Tidak Aktif' }}
                        </button>
                        <a href="{{ asset('storage/' . $selectedMedia->path) }}" 
                           download="{{ $selectedMedia->original_name }}"
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            📥 Download
                        </a>
                        <button wire:click="deleteMedia({{ $selectedMedia->id }})"
                                onclick="return confirm('Yakin ingin menghapus media ini?')"
                                class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors">
                            🗑️ Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
