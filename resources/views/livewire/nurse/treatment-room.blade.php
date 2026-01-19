<div wire:poll.5s>
    {{-- Doctor Panels Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($doctors as $item)
            @php
                $doctor = $item['doctor'];
                $currentQueue = $item['current_queue'];
                $hasPatient = $item['has_patient'];
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Doctor Header --}}
                <div class="px-4 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            @if($doctor->photo_path)
                                <img src="{{ asset('storage/' . $doctor->photo_path) }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <span class="text-white text-lg font-bold">{{ substr($doctor->display_name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold truncate">{{ $doctor->display_name }}</h3>
                            <p class="text-emerald-100 text-sm">{{ $doctor->speciality->name ?? 'Umum' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Status Indicator --}}
                <div class="px-4 py-2 border-b {{ $hasPatient ? 'bg-purple-50' : 'bg-gray-50' }}">
                    @if($hasPatient)
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></span>
                            <span class="text-purple-700 font-medium text-sm">SEDANG MENANGANI PASIEN</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-green-700 font-medium text-sm">TERSEDIA</span>
                        </div>
                    @endif
                </div>

                {{-- Current Patient / Empty State --}}
                <div class="p-4">
                    @if($hasPatient)
                        {{-- Current Patient Info --}}
                        <div class="bg-purple-50 rounded-lg p-4 mb-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs text-purple-600 font-medium">PASIEN SAAT INI</p>
                                    <h4 class="text-lg font-bold text-gray-900 mt-1">{{ $currentQueue->patient->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $currentQueue->patient->patient_id_formatted ?? 'ID: ' . $currentQueue->patient_id }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 bg-purple-600 text-white rounded-full text-lg font-bold">
                                        Q-{{ $currentQueue->formatted_queue_number }}
                                    </span>
                                    @if($currentQueue->priority !== 'NORMAL')
                                        <span class="block mt-1 text-xs px-2 py-0.5 rounded {{ $currentQueue->priority === 'VIP' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $currentQueue->priority }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($currentQueue->complaint)
                                <div class="mt-3 pt-3 border-t border-purple-200">
                                    <p class="text-xs text-purple-600 font-medium">KELUHAN</p>
                                    <p class="text-sm text-gray-700 mt-1">{{ $currentQueue->complaint }}</p>
                                </div>
                            @endif

                            @if($currentQueue->appointment && $currentQueue->appointment->service)
                                <div class="mt-3 pt-3 border-t border-purple-200">
                                    <p class="text-xs text-purple-600 font-medium">LAYANAN</p>
                                    <p class="text-sm text-gray-700 mt-1">{{ $currentQueue->appointment->service->name }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons for Current Patient --}}
                        <div class="flex gap-2">
                            @if($currentQueue->visit)
                                <button 
                                    wire:click="openSoapPanel({{ $currentQueue->visit->visit_id }})"
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Input SOAP
                                </button>
                                <button 
                                    wire:click="completeVisit({{ $currentQueue->queue_id }})"
                                    wire:confirm="Selesaikan treatment untuk pasien ini?"
                                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition flex items-center justify-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Selesai
                                </button>
                            @endif
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-sm">Tidak ada pasien</p>
                        </div>

                        {{-- Call Next Patient Button --}}
                        @if($item['waiting_count'] > 0)
                            <button 
                                wire:click="callNextPatient({{ $doctor->doctor_user_id }})"
                                class="w-full mt-4 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                                Panggil Pasien Berikutnya
                            </button>
                        @endif
                    @endif
                </div>

                {{-- Footer Stats --}}
                <div class="px-4 py-3 bg-gray-50 border-t flex items-center justify-between">
                    <button 
                        wire:click="openQueueModal({{ $doctor->doctor_user_id }})"
                        class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Lihat Antrian
                    </button>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-yellow-600">
                            <span class="font-bold">{{ $item['waiting_count'] }}</span> menunggu
                        </span>
                        <span class="text-green-600">
                            <span class="font-bold">{{ $item['done_count'] }}</span> selesai
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada dokter aktif</h3>
                    <p class="text-gray-500">Belum ada dokter yang terdaftar atau aktif hari ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Queue Modal --}}
    @if($showQueueModal && $selectedDoctor)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeQueueModal"></div>

                {{-- Modal Content --}}
                <div class="relative bg-white rounded-xl shadow-xl transform transition-all sm:max-w-2xl sm:w-full mx-auto">
                    {{-- Header --}}
                    <div class="px-6 py-4 border-b bg-emerald-50 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Daftar Antrian</h3>
                                <p class="text-sm text-gray-600">{{ $selectedDoctor->display_name }}</p>
                            </div>
                            <button wire:click="closeQueueModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-4 max-h-96 overflow-y-auto">
                        @if($this->waitingQueues->count() > 0)
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="pb-3">No. Antrian</th>
                                        <th class="pb-3">Pasien</th>
                                        <th class="pb-3">Layanan</th>
                                        <th class="pb-3">Prioritas</th>
                                        <th class="pb-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($this->waitingQueues as $queue)
                                        <tr>
                                            <td class="py-3">
                                                <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded font-mono font-bold">
                                                    Q-{{ $queue->formatted_queue_number }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <p class="font-medium text-gray-900">{{ $queue->patient->name }}</p>
                                                @if($queue->complaint)
                                                    <p class="text-xs text-gray-500 truncate max-w-xs">{{ $queue->complaint }}</p>
                                                @endif
                                            </td>
                                            <td class="py-3 text-sm text-gray-600">
                                                {{ $queue->appointment->service->name ?? '-' }}
                                            </td>
                                            <td class="py-3">
                                                @if($queue->priority === 'VIP')
                                                    <span class="px-2 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded">VIP</span>
                                                @elseif($queue->priority === 'URGENT')
                                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded">URGENT</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded">Normal</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <button 
                                                    wire:click="callNextPatient({{ $selectedDoctor->doctor_user_id }})"
                                                    class="text-emerald-600 hover:text-emerald-700 font-medium text-sm"
                                                >
                                                    Panggil
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500">Tidak ada pasien yang menunggu.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t bg-gray-50 rounded-b-xl flex justify-end">
                        <button 
                            wire:click="closeQueueModal"
                            class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SOAP Panel (Slide Over) --}}
    @if($showSoapPanel && $currentVisitId)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeSoapPanel"></div>

            {{-- Slide Panel --}}
            <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="w-screen max-w-2xl">
                    <div class="h-full bg-white shadow-xl flex flex-col">
                        <livewire:nurse.soap-input :visit-id="$currentVisitId" :key="$currentVisitId" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TTS Script --}}
    @script
    <script>
        $wire.on('patientCalled', (data) => {
            // Text-to-Speech untuk memanggil pasien
            if ('speechSynthesis' in window) {
                const text = `Nomor antrian Q ${data[0].queue_number}, atas nama ${data[0].patient_name}, silakan menuju ruang perawatan ${data[0].doctor_name}`;
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                speechSynthesis.speak(utterance);
            }
        });
    </script>
    @endscript
</div>