<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Queue;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Component
{
    public $chartViewType = 'daily';
    public $calendarMonth;
    public $calendarYear;
    public $calendarDates = [];

    public function mount()
    {
        $this->calendarMonth = now()->month;
        $this->calendarYear = now()->year;
        $this->generateCalendar();
    }

    public function setChartView($type)
    {
        $this->chartViewType = $type;
        
        // Dispatch event to update chart
        $chartData = $this->getChartData();
        $this->dispatchBrowserEvent('chartUpdated', ['chartData' => $chartData]);
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
        $this->generateCalendar();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarMonth = $date->month;
        $this->calendarYear = $date->year;
        $this->generateCalendar();
    }

    public function goToCurrentMonth()
    {
        $this->calendarMonth = now()->month;
        $this->calendarYear = now()->year;
        $this->generateCalendar();
    }

    private function generateCalendar()
    {
        $firstDay = Carbon::create($this->calendarYear, $this->calendarMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        
        // Get day of week (1 = Monday, 7 = Sunday)
        $startDayOfWeek = $firstDay->dayOfWeekIso;
        
        // Get appointments count per day for this month
        $appointmentCounts = Appointment::whereBetween('scheduled_start_at', [
            $firstDay->format('Y-m-d'),
            $lastDay->format('Y-m-d')
        ])
        ->select(DB::raw('DATE(scheduled_start_at) as date'), DB::raw('COUNT(*) as count'))
        ->groupBy('date')
        ->pluck('count', 'date')
        ->toArray();
        
        $dates = [];
        
        // Add previous month's days
        $prevMonthDays = $startDayOfWeek - 1;
        $prevMonth = $firstDay->copy()->subMonth();
        $prevMonthLastDay = $prevMonth->endOfMonth()->day;
        
        for ($i = $prevMonthDays; $i > 0; $i--) {
            $date = $prevMonthLastDay - $i + 1;
            $dates[] = [
                'date' => $date,
                'is_current_month' => false,
                'is_today' => false,
                'has_appointments' => false,
                'appointment_count' => 0,
            ];
        }
        
        // Add current month's days
        for ($day = 1; $day <= $lastDay->day; $day++) {
            $currentDate = Carbon::create($this->calendarYear, $this->calendarMonth, $day);
            $dateString = $currentDate->format('Y-m-d');
            
            $dates[] = [
                'date' => $day,
                'is_current_month' => true,
                'is_today' => $currentDate->isToday(),
                'has_appointments' => isset($appointmentCounts[$dateString]),
                'appointment_count' => $appointmentCounts[$dateString] ?? 0,
            ];
        }
        
        // Add next month's days to fill the grid
        $remainingDays = 42 - count($dates); // 6 weeks * 7 days
        for ($day = 1; $day <= $remainingDays; $day++) {
            $dates[] = [
                'date' => $day,
                'is_current_month' => false,
                'is_today' => false,
                'has_appointments' => false,
                'appointment_count' => 0,
            ];
        }
        
        $this->calendarDates = $dates;
    }

    private function getChartData()
    {
        $labels = [];
        $data = [];

        switch ($this->chartViewType) {
            case 'daily':
                // Last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $labels[] = $date->format('D');
                    $data[] = Appointment::whereDate('scheduled_start_at', $date->format('Y-m-d'))->count();
                }
                break;

            case 'weekly':
                // Last 4 weeks
                for ($i = 3; $i >= 0; $i--) {
                    $startOfWeek = now()->subWeeks($i)->startOfWeek();
                    $endOfWeek = now()->subWeeks($i)->endOfWeek();
                    $labels[] = 'Week ' . $startOfWeek->weekOfYear;
                    $data[] = Appointment::whereBetween('scheduled_start_at', [
                        $startOfWeek->format('Y-m-d'),
                        $endOfWeek->format('Y-m-d')
                    ])->count();
                }
                break;

            case 'monthly':
                // Last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    $labels[] = $month->format('M');
                    $data[] = Appointment::whereYear('scheduled_start_at', $month->year)
                        ->whereMonth('scheduled_start_at', $month->month)
                        ->count();
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function render()
    {
        // Stats
        $stats = [
            'total_patients' => Patient::count(),
            'queues_today' => Queue::whereDate('created_at', today())->count(),
            'appointments_today' => Appointment::whereDate('scheduled_start_at', today())->count(),
            'revenue_this_month' => Invoice::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
        ];

        // Additional Stats
        $additionalStats = [
            'queues_waiting' => Queue::whereDate('created_at', today())
                ->where('status', 'WAITING')
                ->count(),
            'unpaid_invoices' => Invoice::where('status', 'UNPAID')->count(),
            'unpaid_amount' => Invoice::where('status', 'UNPAID')->sum('total'),
        ];

        // Today's Appointments
        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('scheduled_start_at', today())
            ->orderBy('scheduled_start_at')
            ->take(5)
            ->get();

        // Recent Activities
        $activities = $this->getRecentActivities();

        // Chart Data
        $chartData = $this->getChartData();

        return view('livewire.admin-dashboard', [
            'stats' => $stats,
            'additionalStats' => $additionalStats,
            'todayAppointments' => $todayAppointments,
            'activities' => $activities,
            'chartData' => $chartData,
        ]);
    }

    private function getRecentActivities()
    {
        $activities = [];

        // Get recent patients (last 5)
        $recentPatients = Patient::orderBy('created_at', 'desc')->take(3)->get();
        foreach ($recentPatients as $patient) {
            $activities[] = [
                'icon' => '👤',
                'color' => 'blue',
                'message' => "Pasien baru <strong>{$patient->full_name}</strong> terdaftar",
                'time' => $patient->created_at,
            ];
        }

        // Get recent appointments (last 3)
        $recentAppointments = Appointment::with('patient')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
        foreach ($recentAppointments as $appointment) {
            $activities[] = [
                'icon' => '📅',
                'color' => 'green',
                'message' => "Appointment baru untuk <strong>{$appointment->patient->full_name}</strong>",
                'time' => $appointment->created_at,
            ];
        }

        // Get recent invoices (last 2)
        $recentInvoices = Invoice::with('visit.patient')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
        foreach ($recentInvoices as $invoice) {
            $activities[] = [
                'icon' => '💰',
                'color' => 'purple',
                'message' => "Invoice baru untuk <strong>{$invoice->visit->patient->full_name}</strong> - Rp " . number_format($invoice->total, 0, ',', '.'),
                'time' => $invoice->created_at,
            ];
        }

        // Sort by time desc
        usort($activities, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        return collect($activities)->take(10);
    }
}