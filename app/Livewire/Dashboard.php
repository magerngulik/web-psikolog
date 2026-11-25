<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Activity;
use App\Models\PatientSession;
use App\Models\SessionNote;
use App\Models\Payment;
use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalSessionsMonth;
    public $unpaidSessionsCount = 0;
    public $unpaidSessionsTotal = 0;
    public $monthlyRevenue;
    public $annualRevenue;
    public $monthlySessionRevenue = 0;
    public $monthlyActivityRevenue = 0;
    public $upcomingActivities = [];
    public $icd10Distribution = [];
    public $workloadData = [];
    public $recentAppointments = [];
    public $totalActiveClients = 0;
    public $totalActiveCases = 0;

    // Interactive Schedule Calendar Widget State
    public string $selectedDate = '';
    public string $weekStartDate = '';

    public function mount()
    {
        $this->selectedDate = now()->toDateString();
        $this->weekStartDate = now()->startOfWeek()->toDateString();

        // Rentang tanggal (sargable) agar index database terpakai, bukan whereMonth/whereYear
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->startOfMonth()->addMonth()->toDateString();
        $yearStart = now()->startOfYear()->toDateString();
        $yearEnd = now()->startOfYear()->addYear()->toDateString();

        // 1. Ringkasan Sesi & Pembayaran Belum Lunas
        $this->totalSessionsMonth = PatientSession::where('session_date', '>=', $monthStart)
            ->where('session_date', '<', $monthEnd)
            ->count();

        $unpaidQuery = PatientSession::where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where('fee', '>', 0);

        $this->unpaidSessionsCount = (clone $unpaidQuery)->count();
        $this->unpaidSessionsTotal = (float) (clone $unpaidQuery)->sum('fee');

        // 2. Keuangan (Sum payment or session fee + Activity seminar fee)
        $paymentSumMonth = Payment::where('created_at', '>=', $monthStart)
            ->where('created_at', '<', $monthEnd)
            ->where('status', 'Paid')
            ->sum('amount');

        $sessionFeeSumMonth = PatientSession::where('session_date', '>=', $monthStart)
            ->where('session_date', '<', $monthEnd)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $activityFeeSumMonth = Activity::where('start_date', '>=', $monthStart)
            ->where('start_date', '<', $monthEnd)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $clinicalRevenueMonth = max((float)$paymentSumMonth, (float)$sessionFeeSumMonth);
        $this->monthlySessionRevenue = $clinicalRevenueMonth;
        $this->monthlyActivityRevenue = (float)$activityFeeSumMonth;
        $this->monthlyRevenue = $clinicalRevenueMonth + (float)$activityFeeSumMonth;

        $paymentSumYear = Payment::where('created_at', '>=', $yearStart)
            ->where('created_at', '<', $yearEnd)
            ->where('status', 'Paid')
            ->sum('amount');

        $sessionFeeSumYear = PatientSession::where('session_date', '>=', $yearStart)
            ->where('session_date', '<', $yearEnd)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $activityFeeSumYear = Activity::where('start_date', '>=', $yearStart)
            ->where('start_date', '<', $yearEnd)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $clinicalRevenueYear = max((float)$paymentSumYear, (float)$sessionFeeSumYear);
        $this->annualRevenue = $clinicalRevenueYear + (float)$activityFeeSumYear;

        // Upcoming Activities / Seminars
        $this->upcomingActivities = Activity::with('organization')
            ->where('start_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(4)
            ->get();

        // 3. Distribusi Diagnosa ICD-10 Top 5
        $this->icd10Distribution = SessionNote::select('icd10_code', 'icd10_description', DB::raw('count(*) as total'))
            ->whereNotNull('icd10_code')
            ->where('icd10_code', '!=', '')
            ->groupBy('icd10_code', 'icd10_description')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($this->icd10Distribution->isEmpty()) {
            $this->icd10Distribution = MedicalCase::select('icd10_code', DB::raw('count(*) as total'))
                ->whereNotNull('icd10_code')
                ->where('icd10_code', '!=', '')
                ->groupBy('icd10_code')
                ->orderByDesc('total')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $item->icd10_description = 'Diagnosa Utama';
                    return $item;
                });
        }

        // 4. Workload Chart (Beban Kerja per Hari Minggu Ini)
        $this->workloadData = PatientSession::select(DB::raw('DATE(session_date) as day'), DB::raw('count(*) as count'))
            ->whereBetween('session_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();

        // 5. Statistik Tambahan & Sesi Terbaru
        $this->totalActiveClients = Client::count();
        $this->totalActiveCases = MedicalCase::where('status', 'active')->count();

        $this->recentAppointments = PatientSession::with(['medicalCase.client', 'sessionNote'])
            ->orderBy('updated_at', 'desc')
            ->orderBy('session_date', 'desc')
            ->limit(8)
            ->get();
    }

    public function selectDate(string $date): void
    {
        $parsed = Carbon::parse($date);
        $this->selectedDate = $parsed->toDateString();
        $this->weekStartDate = $parsed->copy()->startOfWeek()->toDateString();
    }

    public function previousWeek(): void
    {
        $newStart = Carbon::parse($this->weekStartDate)->subWeek();
        $this->weekStartDate = $newStart->toDateString();
        $this->selectedDate = $newStart->toDateString();
    }

    public function nextWeek(): void
    {
        $newStart = Carbon::parse($this->weekStartDate)->addWeek();
        $this->weekStartDate = $newStart->toDateString();
        $this->selectedDate = $newStart->toDateString();
    }

    public function goToToday(): void
    {
        $this->selectedDate = now()->toDateString();
        $this->weekStartDate = now()->startOfWeek()->toDateString();
    }

    public function render()
    {
        $weekStart = Carbon::parse($this->weekStartDate ?: now()->startOfWeek()->toDateString());
        $weekEnd = $weekStart->copy()->addDays(6);

        // Count sessions & activities per day in the displayed 7-day strip
        $weekSessionCounts = PatientSession::select(DB::raw('DATE(session_date) as day'), DB::raw('count(*) as total'))
            ->whereBetween('session_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $weekActivityCounts = Activity::select(DB::raw('DATE(start_date) as day'), DB::raw('count(*) as total'))
            ->whereBetween('start_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $dayShortNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $todayStr = now()->toDateString();
        $calendarDays = [];

        for ($i = 0; $i < 7; $i++) {
            $d = $weekStart->copy()->addDays($i);
            $dStr = $d->toDateString();
            $calendarDays[] = [
                'date' => $dStr,
                'dayShort' => $dayShortNames[$i],
                'dayNum' => $d->format('d'),
                'isToday' => $dStr === $todayStr,
                'isSelected' => $dStr === $this->selectedDate,
                'eventCount' => (int) ($weekSessionCounts[$dStr] ?? 0) + (int) ($weekActivityCounts[$dStr] ?? 0),
            ];
        }

        // Load timeline items for the selected date (Sessions + Activities)
        $daySessions = PatientSession::with(['medicalCase.client'])
            ->where('session_date', '>=', $this->selectedDate)
            ->where('session_date', '<', Carbon::parse($this->selectedDate)->addDay()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($s) {
                $clientName = $s->medicalCase?->client?->full_name ?? 'Klien';
                $clientCode = $s->medicalCase?->client?->client_code ?? 'Terlindung';
                $caseTitle = $s->medicalCase?->title ?? 'Sesi Konseling';
                return [
                    'time' => $s->start_time ? Carbon::parse($s->start_time)->format('H:i') : '--:--',
                    'title' => "Sesi #{$s->session_number} — {$clientName}",
                    'maskedTitle' => "Sesi #{$s->session_number} — {$clientCode}",
                    'subtitle' => $caseTitle,
                    'maskedSubtitle' => 'Detail kasus disembunyikan (Mode Privasi)',
                    'type' => 'session',
                    'dotClass' => $s->status === 'done' ? 'bg-emerald-500' : 'bg-blush-500',
                    'url' => route('sessions.show', $s->id),
                ];
            });

        $dayActivities = Activity::where('start_date', '>=', $this->selectedDate)
            ->where('start_date', '<', Carbon::parse($this->selectedDate)->addDay()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($a) {
                return [
                    'time' => $a->start_time ? Carbon::parse($a->start_time)->format('H:i') : '09:00',
                    'title' => $a->title,
                    'maskedTitle' => $a->title,
                    'subtitle' => $a->event_type_label ?? 'Kegiatan / Seminar',
                    'maskedSubtitle' => $a->event_type_label ?? 'Kegiatan / Seminar',
                    'type' => 'activity',
                    'dotClass' => 'bg-violet-500',
                    'url' => route('activities.show', $a->id),
                ];
            });

        $selectedDateSchedule = $daySessions->concat($dayActivities)->sortBy('time')->values();

        return view('livewire.dashboard', [
            'psychologist' => auth()->user() ?? User::first(),
            'calendarDays' => $calendarDays,
            'calendarMonthLabel' => Carbon::parse($this->selectedDate)->translatedFormat('F Y'),
            'selectedDateFormatted' => Carbon::parse($this->selectedDate)->translatedFormat('l, d F'),
            'selectedDateSchedule' => $selectedDateSchedule,
        ])->layout('components.layouts.app');
    }
}
