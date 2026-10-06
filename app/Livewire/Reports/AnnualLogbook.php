<?php

namespace App\Livewire\Reports;

use App\Models\AnnualLogbookAdjustment;
use App\Models\PatientSession;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class AnnualLogbook extends Component
{
    public int $selectedYear;
    public array $availableYears = [];

    // Modal state for manual adjustments
    public bool $showAdjustmentModal = false;
    public ?string $editing_adjustment_id = null;
    public string $adj_activity_key = 'legal_visum';
    public int $adj_month = 1;
    public int $adj_count = 1;
    public string $adj_notes = '';

    public function mount()
    {
        $currentYear = (int) date('Y');
        $this->selectedYear = (int) request()->query('year', $currentYear);

        // Generate years list (e.g. 5 years span)
        $this->availableYears = range($currentYear - 3, $currentYear + 1);
    }

    public static function activityDefinitions(): array
    {
        return [
            'individual' => [
                'number' => '1',
                'title' => 'Tatap muka klien individual secara langsung / komunikasi virtual (Asesmen, Diagnosis, Intervensi)',
                'skp_weight' => 0.05,
                'is_sub' => false,
            ],
            'group' => [
                'number' => '2',
                'title' => 'Tatap muka kelompok/komunitas',
                'skp_weight' => 0.10,
                'is_sub' => false,
            ],
            'phone' => [
                'number' => '3',
                'title' => 'Konsultasi telepon suara > 15 menit',
                'skp_weight' => 0.02,
                'is_sub' => false,
            ],
            'chat_text' => [
                'number' => '4',
                'title' => 'Konsultasi tulisan > 100 kata',
                'skp_weight' => 0.02,
                'is_sub' => false,
            ],
            'legal_visum' => [
                'number' => '5.a',
                'title' => 'Ranah Hukum: Tim visum et repertum psikiatrikum',
                'skp_weight' => 0.02,
                'is_sub' => true,
            ],
            'legal_witness' => [
                'number' => '5.b',
                'title' => 'Ranah Hukum: Saksi ahli',
                'skp_weight' => 0.05,
                'is_sub' => true,
            ],
            'legal_court_report' => [
                'number' => '5.c',
                'title' => 'Ranah Hukum: Laporan pemeriksaan bukti pengadilan',
                'skp_weight' => 0.01,
                'is_sub' => true,
            ],
            'high_risk' => [
                'number' => '6',
                'title' => 'Pelaksanaan tugas di tempat risiko tinggi',
                'skp_weight' => 0.05,
                'is_sub' => false,
            ],
            'report' => [
                'number' => '7',
                'title' => 'Menyusun laporan pemeriksaan psikologis',
                'skp_weight' => 0.01,
                'is_sub' => false,
            ],
        ];
    }

    public function getLogbookDataProperty(): array
    {
        return self::calculateLogbookData($this->selectedYear);
    }

    public static function calculateLogbookData(int $year): array
    {
        $activities = self::activityDefinitions();
        $user = User::first();

        // 1. Initialize matrix
        $matrix = [];
        foreach ($activities as $key => $def) {
            $matrix[$key] = [
                'key' => $key,
                'number' => $def['number'],
                'title' => $def['title'],
                'skp_weight' => $def['skp_weight'],
                'is_sub' => $def['is_sub'],
                'session_months' => array_fill(1, 12, 0),
                'adj_months' => array_fill(1, 12, 0),
                'months' => array_fill(1, 12, 0),
                'total_count' => 0,
                'total_skp' => 0.0,
            ];
        }

        // 2. Query completed sessions for the year
        $sessions = PatientSession::whereYear('session_date', $year)
            ->where('status', 'done')
            ->get();

        foreach ($sessions as $session) {
            $month = (int) \Carbon\Carbon::parse($session->session_date)->format('n');
            if ($month < 1 || $month > 12) {
                continue;
            }

            $modality = $session->service_modality ?: 'individual_direct';

            if (in_array($modality, ['individual_direct', 'individual_virtual'])) {
                $matrix['individual']['session_months'][$month]++;
            } elseif ($modality === 'group') {
                $matrix['group']['session_months'][$month]++;
            } elseif ($modality === 'phone') {
                $matrix['phone']['session_months'][$month]++;
            } elseif ($modality === 'chat_text') {
                $matrix['chat_text']['session_months'][$month]++;
            } elseif ($modality === 'legal_visum') {
                $matrix['legal_visum']['session_months'][$month]++;
            } elseif ($modality === 'legal_witness') {
                $matrix['legal_witness']['session_months'][$month]++;
            } elseif ($modality === 'legal_court_report') {
                $matrix['legal_court_report']['session_months'][$month]++;
            }

            if ($session->is_high_risk) {
                $matrix['high_risk']['session_months'][$month]++;
            }

            if ($session->generates_report) {
                $matrix['report']['session_months'][$month]++;
            }
        }

        // 3. Query manual adjustments
        $adjustments = AnnualLogbookAdjustment::where('year', $year)->get();
        foreach ($adjustments as $adj) {
            if (isset($matrix[$adj->activity_key]) && $adj->month >= 1 && $adj->month <= 12) {
                $matrix[$adj->activity_key]['adj_months'][$adj->month] += (int) $adj->adjustment_count;
            }
        }

        // 4. Calculate sums
        $monthlyTotals = array_fill(1, 12, 0);
        $grandTotalCount = 0;
        $grandTotalSkp = 0.0;

        foreach ($matrix as $key => &$row) {
            $rowTotal = 0;
            for ($m = 1; $m <= 12; $m++) {
                $totalForMonth = $row['session_months'][$m] + $row['adj_months'][$m];
                $row['months'][$m] = $totalForMonth;
                $rowTotal += $totalForMonth;
                $monthlyTotals[$m] += $totalForMonth;
            }
            $row['total_count'] = $rowTotal;
            $row['total_skp'] = round($rowTotal * $row['skp_weight'], 2);
            $grandTotalCount += $rowTotal;
            $grandTotalSkp += $row['total_skp'];
        }

        return [
            'year' => $year,
            'matrix' => $matrix,
            'monthlyTotals' => $monthlyTotals,
            'grandTotalCount' => $grandTotalCount,
            'grandTotalSkp' => round($grandTotalSkp, 2),
            'user' => $user,
            'adjustments' => $adjustments,
        ];
    }

    public function openAdjustmentModal(?string $adjustmentId = null)
    {
        $this->resetValidation();
        $this->editing_adjustment_id = $adjustmentId;

        if ($adjustmentId) {
            $adj = AnnualLogbookAdjustment::findOrFail($adjustmentId);
            $this->adj_activity_key = $adj->activity_key;
            $this->adj_month = (int) $adj->month;
            $this->adj_count = (int) $adj->adjustment_count;
            $this->adj_notes = $adj->notes ?? '';
        } else {
            $this->adj_activity_key = 'legal_visum';
            $this->adj_month = (int) date('n');
            $this->adj_count = 1;
            $this->adj_notes = '';
        }

        $this->showAdjustmentModal = true;
    }

    public function closeAdjustmentModal()
    {
        $this->showAdjustmentModal = false;
        $this->editing_adjustment_id = null;
    }

    public function saveAdjustment()
    {
        $this->validate([
            'adj_activity_key' => 'required|string|in:' . implode(',', array_keys(self::activityDefinitions())),
            'adj_month' => 'required|integer|min:1|max:12',
            'adj_count' => 'required|integer|min:0',
            'adj_notes' => 'nullable|string|max:500',
        ]);

        AnnualLogbookAdjustment::updateOrCreate(
            [
                'year' => $this->selectedYear,
                'activity_key' => $this->adj_activity_key,
                'month' => $this->adj_month,
            ],
            [
                'adjustment_count' => $this->adj_count,
                'notes' => $this->adj_notes,
            ]
        );

        $this->showAdjustmentModal = false;
        session()->flash('message', 'Penyesuaian logbook manual berhasil disimpan!');
    }

    public function deleteAdjustment(string $id)
    {
        AnnualLogbookAdjustment::where('id', $id)->delete();
        session()->flash('message', 'Data penyesuaian manual berhasil dihapus!');
    }

    public function exportPdf()
    {
        $data = self::calculateLogbookData($this->selectedYear);
        $user = $data['user'];
        $psychologistName = $user?->name ?: 'Psikolog';

        $pdf = Pdf::loadView('pdf.annual-logbook', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "LOGBOOK_SKP_{$this->selectedYear}_{$psychologistName}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public static function downloadPdf($year = null)
    {
        $selectedYear = (int) ($year ?: date('Y'));
        $data = self::calculateLogbookData($selectedYear);
        $user = $data['user'];
        $psychologistName = $user?->name ?: 'Psikolog';

        $pdf = Pdf::loadView('pdf.annual-logbook', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "LOGBOOK_SKP_{$selectedYear}_{$psychologistName}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public function render()
    {
        return view('livewire.reports.annual-logbook', [
            'logbookData' => $this->logbookData,
            'activities' => self::activityDefinitions(),
        ])->layout('components.layouts.app');
    }
}
