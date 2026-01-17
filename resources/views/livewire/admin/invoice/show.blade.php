<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Invoice Detail</h1>
            <p class="text-gray-600 mt-1">{{ $invoice->number }}</p>
        </div>
        <div class="flex gap-2">
            @if($invoice->outstanding > 0)
                <button wire:click="openPaymentModal"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Proses Pembayaran
                </button>
            @endif
            
            <button onclick="window.print()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-print mr-2"></i>
                Print
            </button>

            @if($invoice->status === 'DRAFT')
                <button wire:click="deleteInvoice"
                        onclick="return confirm('Yakin ingin menghapus invoice ini?')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus
                </button>
            @endif

            <a href="{{ route('admin.invoices.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Invoice Info & Patient Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Invoice Info Card --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-between">
                        <span><i class="fas fa-file-invoice mr-2"></i> Informasi Invoice</span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($invoice->status === 'PAID') bg-green-100 text-green-800
                            @elseif($invoice->status === 'UNPAID') bg-red-100 text-red-800
                            @elseif($invoice->status === 'PARTIALLY_PAID') bg-yellow-100 text-yellow-800
                            @elseif($invoice->status === 'DRAFT') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $invoice->status }}
                        </span>
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-gray-600">Nomor Invoice</label>
                            <p class="font-semibold text-gray-900">{{ $invoice->number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Tanggal Invoice</label>
                            <p class="font-semibold text-gray-900">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
                        </div>
                        @if($invoice->due_date)
                            <div>
                                <label class="block text-sm text-gray-600">Jatuh Tempo</label>
                                <p class="font-semibold text-gray-900">{{ $invoice->due_date->format('d/m/Y') }}</p>
                            </div>
                        @endif
                        @if($invoice->paid_at)
                            <div>
                                <label class="block text-sm text-gray-600">Tanggal Lunas</label>
                                <p class="font-semibold text-green-600">{{ $invoice->paid_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Patient Info Card --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2"></i> Informasi Pasien
                    </h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm text-gray-600">Nama Lengkap</label>
                            <p class="font-semibold text-gray-900">{{ $invoice->visit->patient->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">No. Rekam Medis</label>
                            <p class="font-semibold text-gray-900">{{ $invoice->visit->patient->medical_record_number }}</p>
                        </div>
                        @if($invoice->visit->patient->phone)
                            <div>
                                <label class="block text-sm text-gray-600">No. Telepon</label>
                                <p class="font-semibold text-gray-900">{{ $invoice->visit->patient->phone }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm text-gray-600">Tanggal Kunjungan</label>
                            <p class="font-semibold text-gray-900">{{ $invoice->visit->visit_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600">Dokter</label>
                            <p class="font-semibold text-gray-900">{{ $invoice->visit->doctor->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list mr-2"></i> Detail Layanan
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Layanan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Diskon</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoice->items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                        @if($item->tooth_codes)
                                            <div class="text-xs text-gray-500">Gigi: {{ $item->tooth_codes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ number_format($item->qty, 0) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">
                                        Rp {{ number_format($item->discount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            @if($invoice->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">
                        <i class="fas fa-sticky-note mr-2"></i> Catatan
                    </h2>
                    <p class="text-gray-700">{{ $invoice->notes }}</p>
                </div>
            @endif

            {{-- Payment History --}}
            @if($payments && $payments->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-history mr-2"></i> Riwayat Pembayaran
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                                {{ $payment->method }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->ref_no ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 py-1 text-xs font-semibold rounded
                                                @if($payment->verify_status === 'VERIFIED') bg-green-100 text-green-800
                                                @elseif($payment->verify_status === 'PENDING') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $payment->verify_status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar - Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-calculator mr-2"></i> Ringkasan Pembayaran
                </h2>

                {{-- Summary Details --}}
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($invoice->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Diskon:</span>
                            <span class="font-semibold text-red-600">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($invoice->tax > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Pajak:</span>
                            <span class="font-semibold">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-base font-semibold">
                            <span class="text-gray-800">TOTAL:</span>
                            <span class="text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($invoice->paid_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Dibayar:</span>
                            <span class="font-semibold text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800">SISA:</span>
                            <span class="{{ $invoice->outstanding > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($invoice->outstanding, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Payment Progress Bar --}}
                @if($invoice->total > 0)
                    <div class="mb-6">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Progress Pembayaran</span>
                            <span>{{ number_format(($invoice->paid_amount / $invoice->total) * 100, 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ min(($invoice->paid_amount / $invoice->total) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @endif

                {{-- Status Info --}}
                <div class="p-4 rounded-lg
                    @if($invoice->status === 'PAID') bg-green-50 border border-green-200
                    @elseif($invoice->status === 'UNPAID') bg-red-50 border border-red-200
                    @elseif($invoice->status === 'PARTIALLY_PAID') bg-yellow-50 border border-yellow-200
                    @else bg-gray-50 border border-gray-200
                    @endif">
                    <p class="text-sm
                        @if($invoice->status === 'PAID') text-green-800
                        @elseif($invoice->status === 'UNPAID') text-red-800
                        @elseif($invoice->status === 'PARTIALLY_PAID') text-yellow-800
                        @else text-gray-800
                        @endif">
                        <i class="fas fa-info-circle mr-1"></i>
                        @if($invoice->status === 'PAID')
                            Invoice sudah lunas
                        @elseif($invoice->status === 'UNPAID')
                            Invoice belum dibayar
                        @elseif($invoice->status === 'PARTIALLY_PAID')
                            Pembayaran sebagian ({{ number_format(($invoice->paid_amount / $invoice->total) * 100, 0) }}%)
                        @else
                            Status: {{ $invoice->status }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    @if($showPaymentModal)
        @livewire('admin.invoice.process-payment', ['invoiceId' => $invoice->invoice_id], key: 'payment-modal-'.$invoice->invoice_id)
    @endif
</div>
