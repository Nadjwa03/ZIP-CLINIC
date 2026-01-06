<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-brown-600 text-white rounded-full flex items-center justify-center font-bold text-xl mr-4">
            1
        </div>
        <div>
            <h3 class="text-xl font-semibold text-gray-900">Pilih Pasien</h3>
            <p class="text-sm text-gray-600">Siapa yang akan berobat?</p>
        </div>
    </div>

    {{-- Patient List --}}
    <div class="space-y-3">
        @forelse($patients as $patient)
            <button 
                wire:click="selectPatient({{ $patient->patient_id }})"
                type="button"
                class="w-full flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-brown-50 hover:border-brown-600 transition
                    {{ $patientId == $patient->patient_id ? 'border-brown-600 bg-brown-50' : 'border-gray-200' }}">
                
                {{-- Avatar --}}
                <div class="w-14 h-14 rounded-full bg-brown-100 flex items-center justify-center font-semibold text-brown-600 text-xl mr-4">
                    {{ strtoupper(substr($patient->full_name, 0, 1)) }}
                </div>
                
                {{-- Info --}}
                <div class="flex-1 text-left">
                    <p class="font-semibold text-gray-900">{{ $patient->full_name }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $patient->relationship_label }} • 
                        {{ $patient->medical_record_number }} •
                        {{ $patient->age }} tahun
                    </p>
                    @if($patient->phone)
                        <p class="text-xs text-gray-500">{{ $patient->phone }}</p>
                    @endif
                </div>

                {{-- Selected Indicator --}}
                @if($patientId == $patient->patient_id)
                    <div class="text-brown-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif
            </button>
        @empty
            <div class="text-center py-12">
                <p class="text-gray-500 mb-4">Belum ada data pasien</p>
                <a href="{{ route('patient.patients.create') }}" 
                   class="inline-block px-6 py-2 bg-brown-600 text-white rounded-lg hover:bg-brown-700">
                    Tambah Pasien Baru
                </a>
            </div>
        @endforelse
    </div>

    {{-- Help Text --}}
    @if(count($patients) > 0)
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">
                <strong>💡 Tips:</strong> Anda bisa booking untuk anggota keluarga. Pilih pasien yang akan berobat.
            </p>
        </div>
    @endif
</div>
