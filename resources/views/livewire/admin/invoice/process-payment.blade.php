<div>
    {{-- Modal Overlay --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 z-40" wire:click="closeModal"></div>

    {{-- Modal Content --}}
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full" @click.stop>
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                        Proses Pembayaran
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6">
                    {{-- Invoice Info --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Invoice:</span>
                                <span class="font-semibold ml-2">{{ $invoice->number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Pasien:</span>
                                <span class="font-semibold ml-2">{{ $invoice->visit->patient->full_name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Invoice:</span>
                                <span class="font-semibold ml-2">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Sudah Dibayar:</span>
                                <span class="font-semibold ml-2 text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <span class="text-gray-600">Sisa Tagihan:</span>
                            <span class="font-bold text-lg ml-2 text-red-600">
                                Rp {{ number_format($invoice->outstanding, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session()->has('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            {{ session('warning') }}
                        </div>
                    @endif

                    {{-- Payment Form --}}
                    <form wire:submit.prevent="savePayment">
                        <div class="space-y-4">
                            {{-- Payment Amount --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Bayar <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" 
                                           wire:model.live="paymentAmount"
                                           min="1"
                                           max="{{ $maxAmount }}"
                                           step="1"
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                                           placeholder="0">
                                </div>
                                @error('paymentAmount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Maksimal: Rp {{ number_format($maxAmount, 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Payment Method --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Metode Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="paymentMethod"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="CASH">Tunai (Cash)</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="TRANSFER">Transfer Bank</option>
                                    <option value="DEBIT">Kartu Debit (EDC)</option>
                                    <option value="CREDIT">Kartu Kredit (EDC)</option>
                                </select>
                                @error('paymentMethod')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Reference Number (Required for TRANSFER) --}}
                            @if($paymentMethod === 'TRANSFER' || $paymentMethod === 'DEBIT' || $paymentMethod === 'CREDIT')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        @if($paymentMethod === 'TRANSFER')
                                            Nomor Referensi Transfer <span class="text-red-500">*</span>
                                        @else
                                            Nomor Approval / Referensi
                                        @endif
                                    </label>
                                    <input type="text" 
                                           wire:model="referenceNumber"
                                           maxlength="80"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Masukkan nomor referensi...">
                                    @error('referenceNumber')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            {{-- Notes --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan (Opsional)
                                </label>
                                <textarea wire:model="notes"
                                          rows="3"
                                          maxlength="500"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Catatan tambahan..."></textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <div class="mt-4 bg-red-50 border border-red-200 rounded p-3">
                                <p class="text-sm font-semibold text-red-800 mb-2">Terdapat kesalahan:</p>
                                <ul class="text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Info Box --}}
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                @if($paymentMethod === 'CASH')
                                    Pembayaran tunai akan langsung terverifikasi.
                                @elseif($paymentMethod === 'TRANSFER')
                                    Pastikan nomor referensi transfer sudah benar.
                                @elseif($paymentMethod === 'QRIS')
                                    Pastikan pembayaran QRIS sudah berhasil sebelum menyimpan.
                                @else
                                    Masukkan nomor approval dari mesin EDC.
                                @endif
                            </p>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="button"
                                    wire:click="closeModal"
                                    class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-check mr-2"></i>
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Close modal when payment processed
    Livewire.on('closePaymentModal', () => {
        // Modal will be closed by parent component
    });
</script>
@endpush
