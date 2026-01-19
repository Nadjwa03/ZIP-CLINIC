<div class="h-full flex flex-col">
    {{-- Header --}}
    <div class="px-6 py-4 border-b bg-blue-50 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Input SOAP</h2>
                <p class="text-sm text-gray-600">{{ $visit->patient->name }} - Q-{{ $visit->queue->formatted_queue_number ?? 'N/A' }}</p>
            </div>
            <button wire:click="closePanel" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('soap-message'))
        <div class="mx-6 mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-sm">
            {{ session('soap-message') }}
        </div>
    @endif

    {{-- Body --}}
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        {{-- Patient Info Card --}}
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Pasien</p>
                    <p class="font-medium text-gray-900">{{ $visit->patient->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Dokter</p>
                    <p class="font-medium text-gray-900">{{ $visit->doctor->display_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Tanggal Kunjungan</p>
                    <p class="font-medium text-gray-900">{{ $visit->visit_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                        {{ $visit->status === 'IN_TREATMENT' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ $visit->status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- SOAP Form --}}
        <form wire:submit.prevent="saveSoap" class="space-y-4">
            {{-- Subjective --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-700 rounded mr-2 text-xs font-bold">S</span>
                    Subjective (Keluhan Pasien)
                </label>
                <textarea 
                    wire:model="subjective"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Keluhan yang disampaikan pasien..."
                ></textarea>
            </div>

            {{-- Objective --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 text-green-700 rounded mr-2 text-xs font-bold">O</span>
                    Objective (Pemeriksaan Fisik)
                </label>
                <textarea 
                    wire:model="objective"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Hasil pemeriksaan fisik..."
                ></textarea>
            </div>

            {{-- Assessment --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-100 text-yellow-700 rounded mr-2 text-xs font-bold">A</span>
                    Assessment (Diagnosis)
                </label>
                <textarea 
                    wire:model="assessment"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                    placeholder="Diagnosis atau penilaian..."
                ></textarea>
            </div>

            {{-- Plan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-purple-100 text-purple-700 rounded mr-2 text-xs font-bold">P</span>
                    Plan (Rencana Tindakan)
                </label>
                <textarea 
                    wire:model="plan"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Rencana pengobatan/tindakan..."
                ></textarea>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                <textarea 
                    wire:model="notes"
                    rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                    placeholder="Catatan tambahan (opsional)..."
                ></textarea>
            </div>

            {{-- Follow Up Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Kontrol</label>
                <input 
                    type="date" 
                    wire:model="follow_up_at"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- Save SOAP Button --}}
            <div class="pt-2">
                <button 
                    type="submit"
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                >
                    Simpan SOAP
                </button>
            </div>
        </form>

        {{-- Divider --}}
        <hr class="border-gray-200">

        {{-- Tindakan Section --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tindakan / Prosedur</h3>
                <button 
                    wire:click="toggleAddDetail"
                    class="px-3 py-1.5 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah
                </button>
            </div>

            {{-- Add Detail Form --}}
            @if($showAddDetail)
                <div class="bg-emerald-50 rounded-lg p-4 mb-4 border border-emerald-200">
                    <h4 class="font-medium text-emerald-800 mb-3">Tambah Tindakan</h4>
                    
                    <div class="space-y-3">
                        {{-- Service Select --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan/Tindakan</label>
                            <select 
                                wire:model.live="newDetail.service_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            >
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->service_id }}">
                                        {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('newDetail.service_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tooth Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Gigi (opsional)</label>
                            <input 
                                type="text" 
                                wire:model="newDetail.tooth_number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Contoh: 11, 21-23, 36"
                            >
                        </div>

                        {{-- Quantity & Price --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <input 
                                    type="number" 
                                    wire:model="newDetail.quantity"
                                    min="1"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                                <input 
                                    type="number" 
                                    wire:model="newDetail.unit_price"
                                    min="0"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                >
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
                            <input 
                                type="text" 
                                wire:model="newDetail.description"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Keterangan tambahan..."
                            >
                        </div>

                        {{-- Buttons --}}
                        <div class="flex gap-2 pt-2">
                            <button 
                                type="button"
                                wire:click="addDetail"
                                class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition"
                            >
                                Simpan Tindakan
                            </button>
                            <button 
                                type="button"
                                wire:click="toggleAddDetail"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Details List --}}
            @if(count($details) > 0)
                <div class="space-y-2">
                    @foreach($details as $detail)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $detail['description'] ?? $detail['service']['name'] ?? 'Tindakan' }}</p>
                                <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                    @if($detail['tooth_number'])
                                        <span>Gigi: {{ $detail['tooth_number'] }}</span>
                                    @endif
                                    <span>{{ $detail['quantity'] }} x Rp {{ number_format($detail['unit_price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}</p>
                                <button 
                                    wire:click="removeDetail({{ $detail['visit_detail_id'] }})"
                                    wire:confirm="Hapus tindakan ini?"
                                    class="text-red-500 hover:text-red-700 text-sm mt-1"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- Total --}}
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                        <span class="font-semibold text-emerald-800">Total</span>
                        <span class="font-bold text-emerald-800 text-lg">
                            Rp {{ number_format(collect($details)->sum('subtotal'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500">Belum ada tindakan.</p>
                    <p class="text-gray-400 text-sm">Klik tombol "Tambah" untuk menambahkan tindakan.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-6 py-4 border-t bg-gray-50 flex-shrink-0">
        <button 
            wire:click="closePanel"
            class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition"
        >
            Tutup
        </button>
    </div>
</div>