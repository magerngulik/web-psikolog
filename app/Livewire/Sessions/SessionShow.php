<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use App\Models\SessionNote;
use App\Models\SystemReference;
use App\Services\PpdgjCatalog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class SessionShow extends Component
{
    public Session $session;
    public ?SessionNote $sessionNote = null;

    // Form Properties - Subjective (Keluhan & Masalah)
    public $subjective_complaint = '';
    public $subjective_problem = '';

    // Form Properties - Objective (Dinamika Psikologis)
    public $objective = '';

    // Form Properties - Assessment (4 Checkbox Methods + Free Text)
    public array $availableAssessments = [
        'Paper & Pencil Test',
        'Inventory',
        'Observasi',
        'Wawancara',
    ];
    public array $selected_assessments = [];
    public $assessment = '';

    // Plan & Diagnosis (PPDGJ-III / ICD-10 Searchable & Free Text Diagnosis)
    public $plan = '';
    public $icd10_code = '';
    public $icd10_description = '';
    public $diagnosis_notes = '';
    public $icd_search = '';
    public bool $show_icd_dropdown = false;

    // Intervensi Psikologis (Daftar Pilihan Utama & Catatan Bebas)
    public array $availableInterventions = [];
    public array $selected_interventions = [];
    public $intervention_notes = '';

    // Follow Up, Pesan Klien, & Sesi
    public $follow_up_status = 'Selesai';
    public $client_message = '';
    public $duration_minutes = 60;

    // Administrative / Sesi Properties
    public $status;
    public $payment_status;
    public $payment_method;
    public $fee;
    public $service_modality = 'individual_direct';
    public bool $is_high_risk = false;
    public bool $generates_report = true;
    public $is_locked = false;

    // Backward-compatibility properties
    public $summary = '';
    public $dynamic_notes = '';
    public $recommendation = '';

    public function mount($id)
    {
        $this->session = Session::with(['medicalCase.client', 'psychologist', 'sessionNote'])->findOrFail($id);

        $this->status = $this->session->status;
        $this->payment_status = $this->session->payment_status ?? 'unpaid';
        $this->payment_method = $this->session->payment_method ?? 'cash';
        $this->fee = $this->session->fee ?? 0;
        $this->duration_minutes = $this->session->duration_minutes ?? 60;
        $this->service_modality = $this->session->service_modality ?? 'individual_direct';
        $this->is_high_risk = (bool) ($this->session->is_high_risk ?? false);
        $this->generates_report = (bool) ($this->session->generates_report ?? true);
        $this->is_locked = (bool) $this->session->is_locked;

        // Load or initialize SessionNote
        $note = SessionNote::firstOrNew(['appointment_id' => $this->session->id]);

        if ($note->exists) {
            $this->sessionNote = $note;
            $this->subjective_complaint = $note->subjective_complaint ?? $note->subjective ?? '';
            $this->subjective_problem = $note->subjective_problem ?? '';
            $this->objective = $note->objective ?? '';
            $this->selected_assessments = $note->assessment_methods ?? [];
            $this->assessment = $note->assessment ?? '';
            $this->plan = $note->plan ?? '';
            $this->icd10_code = $note->icd10_code ?? '';
            $this->icd10_description = $note->icd10_description ?? '';
            $this->diagnosis_notes = $note->diagnosis_notes ?? '';
            $this->selected_interventions = $note->intervention_ids ?? [];
            $this->intervention_notes = $note->intervention_notes ?? $this->session->intervention_notes ?? '';
            $this->follow_up_status = $note->follow_up_status ?? 'Selesai';
            $this->client_message = $note->client_message ?? '';
            $this->duration_minutes = $note->duration_minutes ?? $this->session->duration_minutes ?? 60;
            $this->is_locked = (bool) ($note->is_locked || $this->session->is_locked);

            if ($this->icd10_code) {
                $this->icd_search = $this->icd10_code . ($this->icd10_description ? ' - ' . $this->icd10_description : '');
            }
        } else {
            // Pre-populate from session if available, or fallback to medical case initial subjective notes
            $this->subjective_complaint = $this->session->subjective_complaint 
                ?? $this->session->complaint 
                ?? $this->session->medicalCase?->subjective_complaint 
                ?? $this->session->medicalCase?->complaint 
                ?? '';
            $this->subjective_problem = $this->session->subjective_problem 
                ?? $this->session->medicalCase?->subjective_problem 
                ?? '';
            $this->objective = $this->session->dynamic_notes ?? '';
            $this->intervention_notes = $this->session->intervention_notes ?? '';
            $this->client_message = $this->session->recommendation ?? $this->session->message ?? '';
        }

        // Ringkasan Sesi diselaraskan dengan Dinamika Psikologis (Objective). Jika awal kosong, beri string kosong.
        $this->dynamic_notes = $this->objective ?: ($this->session->dynamic_notes ?? '');
        $this->summary = $this->dynamic_notes ?: '';
        $this->recommendation = $this->client_message ?: ($this->session->recommendation ?? '');

        $this->loadInterventions();
    }

    public static function defaultInterventions(): array
    {
        return [
            1 => 'Psikoedukasi',
            2 => 'Konseling Psikologis',
            3 => 'Cognitive Behavioral Therapy (CBT)',
            4 => 'Acceptance & Commitment Therapy (ACT)',
            5 => 'Behavioral Activation',
            6 => 'Mindfulness & Relaxation Therapy',
            7 => 'Solution-Focused Brief Therapy (SFBT)',
            8 => 'Client-Centered Therapy / Humanistic',
            9 => 'Interpersonal Psychotherapy (IPT)',
            10 => 'Psychodynamic / Psychoanalytic',
            11 => 'Family / Systemic Therapy',
            12 => 'Couples / Marital Therapy',
            13 => 'Expressive / Art / Play Therapy',
            14 => 'Crisis Intervention & Safety Planning',
            15 => 'Motivational Interviewing (MI)',
            16 => 'Biofeedback / Neurofeedback',
        ];
    }

    public function loadInterventions(): void
    {
        try {
            $refs = SystemReference::where('group_key', 'psychological_intervention')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            if ($refs->isNotEmpty()) {
                $interventions = [];
                foreach ($refs as $ref) {
                    $key = is_numeric($ref->value) ? (int) $ref->value : $ref->value;
                    $interventions[$key] = $ref->label;
                }
                $this->availableInterventions = $interventions;
                return;
            }
        } catch (\Throwable $e) {
            // fallback
        }

        $this->availableInterventions = self::defaultInterventions();
    }

    public function updatedIcdSearch()
    {
        $this->show_icd_dropdown = true;
    }

    public function selectDiagnosis(string $code, string $name)
    {
        $this->icd10_code = $code;
        $this->icd10_description = $name;
        $this->icd_search = "{$code} - {$name}";
        $this->show_icd_dropdown = false;
    }

    public function toggleIcdDropdown()
    {
        $this->show_icd_dropdown = !$this->show_icd_dropdown;
    }

    public function closeIcdDropdown()
    {
        $this->show_icd_dropdown = false;
    }

    public function getFilteredDiagnosesProperty(): array
    {
        return PpdgjCatalog::search($this->icd_search);
    }

    public function saveNotes()
    {
        if ($this->session->is_locked || $this->is_locked) {
            session()->flash('error', '⚠️ Rekam medis sesi ini sudah TERKUNCI dan tidak dapat diubah lagi.');
            return;
        }

        // Sync backward-compatible fields if modified directly
        if (empty($this->objective) && !empty($this->dynamic_notes)) {
            $this->objective = $this->dynamic_notes;
        }
        if (empty($this->objective) && !empty($this->summary)) {
            $this->objective = $this->summary;
        }
        if (empty($this->client_message) && !empty($this->recommendation)) {
            $this->client_message = $this->recommendation;
        }

        // Auto fallback for diagnosis if custom notes filled
        if (empty($this->icd10_code) && !empty($this->diagnosis_notes)) {
            $this->icd10_code = 'F99';
            if (empty($this->icd10_description)) {
                $this->icd10_description = 'Diagnosis Kustom (Non-PPDGJ / Belum Terklasifikasi)';
            }
        }

        $combinedSubjective = trim(
            ($this->subjective_complaint ? "Keluhan:\n" . $this->subjective_complaint : '') .
            ($this->subjective_problem ? "\n\nMasalah:\n" . $this->subjective_problem : '')
        );

        $token = $this->sessionNote?->qr_code_token ?? Str::random(32);

        DB::transaction(function () use ($combinedSubjective, $token) {
            // Update Session / PatientSession: summary diselaraskan dengan Dinamika Psikologis (Objective)
            $dynamicSummary = $this->objective ?: ($this->dynamic_notes ?: '');

            $this->session->update([
                'status' => $this->status,
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
                'fee' => $this->fee,
                'duration_minutes' => $this->duration_minutes,
                'service_modality' => $this->service_modality,
                'is_high_risk' => $this->is_high_risk,
                'generates_report' => $this->generates_report,
                'summary' => $dynamicSummary,
                'dynamic_notes' => $dynamicSummary,
                'intervention_notes' => $this->intervention_notes,
                'recommendation' => $this->client_message ?: $this->recommendation,
                'subjective_complaint' => $this->subjective_complaint,
                'subjective_problem' => $this->subjective_problem,
                'diagnosis_notes' => $this->diagnosis_notes,
            ]);

            // Update or Create SessionNote
            $this->sessionNote = SessionNote::updateOrCreate(
                ['appointment_id' => $this->session->id],
                [
                    'patient_session_id' => $this->session->id,
                    'subjective' => $combinedSubjective ?: $this->subjective_complaint,
                    'subjective_complaint' => $this->subjective_complaint,
                    'subjective_problem' => $this->subjective_problem,
                    'objective' => $this->objective,
                    'assessment' => $this->assessment,
                    'plan' => $this->plan ?: 'Dilanjutkan sesuai target intervensi yang disepakati.',
                    'icd10_code' => $this->icd10_code,
                    'icd10_description' => $this->icd10_description,
                    'diagnosis_notes' => $this->diagnosis_notes,
                    'assessment_methods' => $this->selected_assessments,
                    'intervention_ids' => $this->selected_interventions,
                    'intervention_notes' => $this->intervention_notes,
                    'follow_up_status' => $this->follow_up_status ?? 'Selesai',
                    'client_message' => $this->client_message,
                    'duration_minutes' => $this->duration_minutes ?? 60,
                    'qr_code_token' => $token,
                    'is_locked' => false,
                ]
            );
        });

        session()->flash('message', 'Catatan rekam medis sesi berhasil diperbarui!');
    }

    public function saveRpp()
    {
        $this->saveNotes();
    }

    public function lockSession()
    {
        if ($this->session->is_locked || $this->is_locked) {
            return;
        }

        // Save current changes first
        $this->saveNotes();

        DB::transaction(function () {
            $this->session->update([
                'is_locked' => true,
                'locked_at' => now(),
                'status' => 'done',
            ]);

            if ($this->sessionNote) {
                $this->sessionNote->update([
                    'is_locked' => true,
                ]);
            }
        });

        $this->is_locked = true;
        $this->status = 'done';

        session()->flash('message', '🔒 Sesi konsultasi & Catatan Rekam Medis RESMI DIKUNCI!');
    }

    public function exportPdf()
    {
        if (!$this->sessionNote || !$this->sessionNote->exists) {
            $this->saveNotes();
        }

        $note = SessionNote::where('appointment_id', $this->session->id)->first();
        if (!$note) {
            $this->saveNotes();
            $note = SessionNote::where('appointment_id', $this->session->id)->firstOrFail();
        }

        $session = Session::with(['patient', 'psychologist', 'medicalCase.client'])->findOrFail($this->session->id);

        $pdf = Pdf::loadView('pdf.rpp-document', [
            'appointment' => $session,
            'note' => $note,
            'interventions' => $this->availableInterventions,
        ])->setPaper('a4', 'portrait');

        $patientName = $session->patient?->name ?? 'Klien';
        $sessionDate = $session->session_date ?? date('Y-m-d');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "RPP_Konseling_{$patientName}_{$sessionDate}.pdf"
        );
    }

    public function render()
    {
        return view('livewire.sessions.session-show', [
            'filteredDiagnoses' => $this->filteredDiagnoses,
        ])->layout('components.layouts.app');
    }
}
