<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Buat Invoice</h1>
        <p class="text-gray-600 mt-1">Buat invoice untuk visit yang sudah selesai</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Patient Info Card --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user mr-2"></i> Informasi Pasien
                </h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600">Nama Lengkap</label>
                        <p class="font-semibold text-gray-900">{{ $patient->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">No. Rekam Medis</label>
                        <p class="font-semibold text-gray-900">{{ $patient->medical_record_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Tanggal Kunjungan</label>
                        <p class="font-semibold text-gray-900">{{ $visit->visit_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Dokter</label>
                        <p class="font-semibold text-gray-900">{{ $visit->doctor->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list mr-2"></i> Item Layanan
                </h2>

                @if(count($items) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layanan</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Diskon</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $item['name'] }}</div>
                                            @if(isset($item['tooth_codes']) && $item['tooth_codes'])
                                                <div class="text-xs text-gray-500">Gigi: {{ $item['tooth_codes'] }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" 
                                                   wire:model.live="items.{{ $index }}.qty"
                                                   wire:change="updateItemQty({{ $index }})"
                                                   min="1"
                                                   class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-900">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   wire:model.live="items.{{ $index }}.discount"
                                                   wire:change="updateItemDiscount({{ $index }})"
                                                   min="0"
                                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-right"
                                                   placeholder="0">
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                            Rp {{ number_format(($item['qty'] * $item['unit_price']) - ($item['discount'] ?? 0), 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button wire:click="removeItem({{ $index }})"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Tidak ada item layanan</p>
                        <p class="text-sm">Item akan otomatis diisi dari visit details</p>
                    </div>
                @endif
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-sticky-note mr-2"></i> Catatan
                </h2>
                <textarea wire:model="notes"
                          rows="3"
                          placeholder="Catatan tambahan untuk invoice..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>
        </div>

        {{-- Sidebar - Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-calculator mr-2"></i> Ringkasan
                </h2>

                {{-- Summary Details --}}
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    {{-- Discount Input --}}
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Diskon Total:</span>
                        <input type="number" 
                               wire:model.live="discount"
                               min="0"
                               max="{{ $subtotal }}"
                               class="w-32 px-2 py-1 border border-gray-300 rounded text-right"
                               placeholder="0">
                    </div>

                    {{-- Tax Input (Optional) --}}
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Pajak (PPN):</span>
                        <input type="number" 
                               wire:model.live="tax"
                               min="0"
                               class="w-32 px-2 py-1 border border-gray-300 rounded text-right"
                               placeholder="0">
                    </div>

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800">TOTAL:</span>
                            <span class="text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded p-3 mb-4">
                        <p class="text-sm font-semibold text-red-800 mb-2">Error:</p>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="space-y-3">
                    <button wire:click="save"
                            class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i> Simpan Invoice
                    </button>

                    <a href="{{ route('admin.invoices.index') }}"
                       class="block w-full px-6 py-3 bg-gray-200 text-gray-700 font-semibold text-center rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>

                {{-- Info --}}
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Invoice akan dibuat dengan status <strong>UNPAID</strong>. 
                        Anda bisa proses pembayaran setelah invoice dibuat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
