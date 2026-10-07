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
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalSessionsMonth;
    public $unclosedNotesCount;
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

    public function mount()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Ringkasan Sesi & Unclosed Notes
        $this->totalSessionsMonth = PatientSession::whereMonth('session_date', $currentMonth)
            ->whereYear('session_date', $currentYear)
            ->count();

        $this->unclosedNotesCount = PatientSession::where('is_locked', false)
            ->whereIn('status', ['done', 'Completed', 'finished'])
            ->count();

        // 2. Keuangan (Sum payment or session fee + Activity seminar fee)
        $paymentSumMonth = Payment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'Paid')
            ->sum('amount');

        $sessionFeeSumMonth = PatientSession::whereMonth('session_date', $currentMonth)
            ->whereYear('session_date', $currentYear)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $activityFeeSumMonth = Activity::whereMonth('start_date', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $clinicalRevenueMonth = max((float)$paymentSumMonth, (float)$sessionFeeSumMonth);
        $this->monthlySessionRevenue = $clinicalRevenueMonth;
        $this->monthlyActivityRevenue = (float)$activityFeeSumMonth;
        $this->monthlyRevenue = $clinicalRevenueMonth + (float)$activityFeeSumMonth;

        $paymentSumYear = Payment::whereYear('created_at', $currentYear)
            ->where('status', 'Paid')
            ->sum('amount');

        $sessionFeeSumYear = PatientSession::whereYear('session_date', $currentYear)
            ->where('payment_status', 'paid')
            ->sum('fee');

        $activityFeeSumYear = Activity::whereYear('start_date', $currentYear)
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

        $this->recentAppointments = PatientSession::with(['medicalCase.client'])
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'psychologist' => auth()->user() ?? User::first(),
        ])->layout('components.layouts.app');
    }
}
