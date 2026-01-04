<div>
    <!-- Main Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Pasien -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Pasien</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_patients']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antrian Hari Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">Antrian Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['queues_today'] }}</p>
                        @if($additionalStats['queues_waiting'] > 0)
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="text-yellow-600 font-semibold">{{ $additionalStats['queues_waiting'] }}</span> menunggu
                            </p>
                        @endif
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Hari Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Appointment Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['appointments_today'] }}</p>
                    </div>
                    <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pendapatan Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Cards -->
    @if($additionalStats['unpaid_invoices'] > 0)
    <div class="mb-6">
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm p-4 hover:shadow-md transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-red-800">Invoice Belum Lunas</h3>
                    <p class="text-sm text-red-700 mt-1">
                        <span class="font-bold">{{ $additionalStats['unpaid_invoices'] }} invoice</span> 
                        belum dibayar dengan total 
                        <span class="font-bold">Rp {{ number_format($additionalStats['unpaid_amount'], 0, ',', '.') }}</span>
                    </p>
                    <a href="#" class="text-sm text-red-800 hover:text-red-900 font-semibold mt-2 inline-block">
                        Lihat Invoice →
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Chart & Calendar Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Appointments Overview Chart (2 kolom) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Appointments Overview</h3>
                <div class="flex gap-2">
                    <button 
                        wire:click="setChartView('daily')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $chartViewType === 'daily' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Daily
                    </button>
                    <button 
                        wire:click="setChartView('weekly')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $chartViewType === 'weekly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Weekly
                    </button>
                    <button 
                        wire:click="setChartView('monthly')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $chartViewType === 'monthly' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                        Monthly
                    </button>
                </div>
            </div>
            <div class="p-6" wire:ignore>
                <canvas id="appointmentsChart" height="280" data-chart='@json($chartData)'></canvas>
            </div>
        </div>

        <!-- Calendar (1 kolom) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <button wire:click="prevMonth" class="p-2 rounded-lg hover:bg-gray-100 transition" title="Bulan sebelumnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->format('F Y') }}
                        </h3>
                        <button wire:click="goToCurrentMonth" class="text-xs text-blue-600 hover:underline">
                            Bulan ini
                        </button>
                    </div>

                    <button wire:click="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 transition" title="Bulan berikutnya">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-4">
                <!-- Calendar Header (Days of Week) -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                        <div class="text-center text-xs font-medium text-gray-500 py-1">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Dates -->
                <div class="grid grid-cols-7 gap-1">
                    @foreach($calendarDates as $dateInfo)
                        <div class="aspect-square">
                            <button class="w-full h-full flex flex-col items-center justify-center text-sm rounded-lg transition
                                {{ $dateInfo['is_today'] ? 'bg-blue-600 text-white font-bold' : '' }}
                                {{ !$dateInfo['is_current_month'] ? 'text-gray-300' : 'text-gray-700 hover:bg-gray-100' }}
                                {{ $dateInfo['has_appointments'] && !$dateInfo['is_today'] ? 'bg-blue-50 font-semibold' : '' }}">
                                {{ $dateInfo['date'] }}
                                @if($dateInfo['has_appointments'])
                                    <span class="text-[8px] {{ $dateInfo['is_today'] ? 'text-white' : 'text-blue-600' }}">
                                        {{ $dateInfo['appointment_count'] }}
                                    </span>
                                @endif
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Appointment Hari Ini</h3>
            <a href="#" class="text-sm text-gray-600 hover:text-gray-900">Latest Appoint →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($todayAppointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($appointment->patient->full_name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $appointment->patient->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $appointment->patient->medical_record_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->doctor->display_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <div class="flex items-center justify-center gap-2">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada appointment hari ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6">
            @if($activities->count() > 0)
                <div class="space-y-4">
                    @foreach($activities as $activity)
                        <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full 
                                    @if($activity['color'] === 'blue') bg-blue-100 @endif
                                    @if($activity['color'] === 'green') bg-green-100 @endif
                                    @if($activity['color'] === 'purple') bg-purple-100 @endif
                                    @if($activity['color'] === 'yellow') bg-yellow-100 @endif
                                    @if($activity['color'] === 'red') bg-red-100 @endif
                                    flex items-center justify-center text-xl">
                                    {{ $activity['icon'] }}
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">
                                    {!! $activity['message'] !!}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="mt-4 text-sm text-gray-500">Belum ada aktivitas hari ini</p>
                    <p class="text-xs text-gray-400 mt-1">Aktivitas akan muncul di sini</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
(function () {
  'use strict';

  let chartInstance = null;

  function getCanvas() {
    return document.getElementById('appointmentsChart');
  }

  function readChartDataFromCanvas() {
    const canvas = getCanvas();
    if (!canvas) return { labels: [], data: [] };

    try {
      const raw = canvas.dataset.chart || '{}';
      const parsed = JSON.parse(raw);
      return {
        labels: parsed.labels || [],
        data: parsed.data || []
      };
    } catch (e) {
      console.error('❌ Failed parse chart data:', e);
      return { labels: [], data: [] };
    }
  }

  function renderChart(chartData) {
    const canvas = getCanvas();
    if (!canvas) {
      console.error('❌ Canvas not found');
      return;
    }

    const ctx = canvas.getContext('2d');

    if (chartInstance) {
      chartInstance.destroy();
      chartInstance = null;
    }

    chartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Appointments',
          data: chartData.data,
          backgroundColor: 'rgb(59, 130, 246)',
          borderRadius: 8,
          barThickness: 40,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  function boot() {
    const data = readChartDataFromCanvas();
    console.log('📊 Boot chart with:', data);
    renderChart(data);
  }

  // load pertama kali
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setTimeout(boot, 200));
  } else {
    setTimeout(boot, 200);
  }

  // ✅ ini yang bikin weekly/monthly langsung redraw
  window.addEventListener('chartUpdated', function (event) {
    const detail = event.detail || {};
    const chartData = detail.chartData || detail;

    const canvas = getCanvas();
    if (canvas && chartData) {
      canvas.dataset.chart = JSON.stringify(chartData);
      console.log('🔄 Chart updated:', chartData);
      setTimeout(() => renderChart(chartData), 50);
    }
  });

})();
</script>
@endpush
