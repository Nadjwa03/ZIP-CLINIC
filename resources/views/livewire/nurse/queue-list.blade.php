<div>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-sm text-gray-500">Total Antrian</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl shadow-sm border border-yellow-200 p-4">
            <p class="text-sm text-yellow-600">Menunggu</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $stats['waiting'] }}</p>
        </div>
        <div class="bg-purple-50 rounded-xl shadow-sm border border-purple-200 p-4">
            <p class="text-sm text-purple-600">Sedang Ditangani</p>
            <p class="text-2xl font-bold text-purple-700">{{ $stats['in_treatment'] }}</p>
        </div>
        <div class="bg-green-50 rounded-xl shadow-sm border border-green-200 p-4">
            <p class="text-sm text-green-600">Selesai</p>
            <p class="text-2xl font-bold text-green-700">{{ $stats['done'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Date --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                <input 
                    type="date" 
                    wire:model.live="queueDate"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
            </div>

            {{-- Doctor --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dokter</label>
                <select 
                    wire:model.live="filterDoctor"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                    <option value="">Semua Dokter</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->doctor_user_id }}">{{ $doctor->display_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select 
                    wire:model.live="filterStatus"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                    <option value="">Semua Status</option>
                    <option value="WAITING">Menunggu</option>
                    <option value="IN_TREATMENT">Sedang Ditangani</option>
                    <option value="DONE">Selesai</option>
                    <option value="SKIPPED">Dilewati</option>
                    <option value="CANCELLED">Dibatalkan</option>
                </select>
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Prioritas</label>
                <select 
                    wire:model.live="filterPriority"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                >
                    <option value="">Semua Prioritas</option>
                    <option value="NORMAL">Normal</option>
                    <option value="VIP">VIP</option>
                    <option value="URGENT">Urgent</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="flex items-end">
                <button 
                    wire:click="resetFilters"
                    class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition"
                >
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Queue Table --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Antrian</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioritas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($queues as $queue)
                    <tr class="{{ $queue->status === 'IN_TREATMENT' ? 'bg-purple-50' : '' }}">
                        {{-- Queue Number --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center justify-center px-3 py-1 bg-gray-800 text-white rounded font-mono font-bold">
                                Q-{{ $queue->formatted_queue_number }}
                            </span>
                        </td>

                        {{-- Patient --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $queue->patient->name }}</p>
                            @if($queue->complaint)
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($queue->complaint, 30) }}</p>
                            @endif
                        </td>

                        {{-- Doctor --}}
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900">{{ $queue->doctor->display_name }}</p>
                            <p class="text-xs text-gray-500">{{ $queue->doctor->speciality->name ?? '' }}</p>
                        </td>

                        {{-- Service --}}
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $queue->appointment->service->name ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'WAITING' => 'bg-yellow-100 text-yellow-800',
                                    'CALLED' => 'bg-blue-100 text-blue-800',
                                    'IN_TREATMENT' => 'bg-purple-100 text-purple-800',
                                    'DONE' => 'bg-green-100 text-green-800',
                                    'CANCELLED' => 'bg-red-100 text-red-800',
                                    'SKIPPED' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded {{ $statusColors[$queue->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $queue->status_label }}
                            </span>
                        </td>

                        {{-- Priority --}}
                        <td class="px-4 py-3">
                            @if($queue->priority === 'VIP')
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded">VIP</span>
                            @elseif($queue->priority === 'URGENT')
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded animate-pulse">URGENT</span>
                            @else
                                <span class="text-xs text-gray-500">Normal</span>
                            @endif
                        </td>

                        {{-- Time --}}
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($queue->called_at)
                                <p class="text-xs">Dipanggil: {{ $queue->called_at->format('H:i') }}</p>
                            @endif
                            @if($queue->completed_at)
                                <p class="text-xs text-green-600">Selesai: {{ $queue->completed_at->format('H:i') }}</p>
                            @endif
                            @if(!$queue->called_at && !$queue->completed_at)
                                <p class="text-xs text-gray-400">Dibuat: {{ $queue->created_at->format('H:i') }}</p>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($queue->status === 'WAITING')
                                    <button 
                                        wire:click="callPatient({{ $queue->queue_id }})"
                                        class="px-3 py-1 text-xs bg-emerald-600 hover:bg-emerald-700 text-white rounded font-medium transition"
                                    >
                                        Panggil
                                    </button>
                                    <button 
                                        wire:click="skipPatient({{ $queue->queue_id }})"
                                        wire:confirm="Lewati pasien ini?"
                                        class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded font-medium transition"
                                    >
                                        Skip
                                    </button>
                                @elseif($queue->status === 'SKIPPED')
                                    <button 
                                        wire:click="restorePatient({{ $queue->queue_id }})"
                                        class="px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded font-medium transition"
                                    >
                                        Kembalikan
                                    </button>
                                @elseif($queue->status === 'IN_TREATMENT')
                                    <span class="text-xs text-purple-600 font-medium">Sedang ditangani</span>
                                @elseif($queue->status === 'DONE')
                                    <span class="text-xs text-green-600">✓ Selesai</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-500">Tidak ada antrian ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($queues->hasPages())
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $queues->links() }}
            </div>
        @endif
    </div>

    {{-- TTS Script --}}
    @script
    <script>
        $wire.on('patientCalled', (data) => {
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