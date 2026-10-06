<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\SecurityKey;
use App\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SessionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $client;
    protected $case;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Klien Sesi Test',
            'gender' => 'female',
        ]);

        $this->case = MedicalCase::create([
            'case_code' => 'CAS-202610-001',
            'client_id' => $this->client->id,
            'title' => 'Kasus Sesi Test',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
    }

    public function test_session_index_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('sessions.index'));

        $response->assertStatus(200);
    }

    public function test_session_create_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('sessions.create'));

        $response->assertStatus(200);
    }

    public function test_can_schedule_new_session(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('sessions.session-create')
            ->set('medical_case_id', $this->case->id)
            ->set('session_number', 1)
            ->set('session_date', '2026-10-10')
            ->set('start_time', '09:00')
            ->set('end_time', '10:00')
            ->set('fee', 250000)
            ->set('status', 'scheduled')
            ->set('payment_status', 'unpaid')
            ->set('payment_method', 'cash')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('patient_sessions', [
            'case_id' => $this->case->id,
            'session_number' => 1,
            'session_date' => '2026-10-10',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);
    }

    public function test_anti_overlap_validation_prevents_conflicting_schedules(): void
    {
        Session::create([
            'case_id' => $this->case->id,
            'medical_case_id' => $this->case->id,
            'session_number' => 1,
            'session_date' => '2026-10-10',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'fee' => 250000,
            'status' => 'scheduled',
        ]);

        Livewire::test('sessions.session-create')
            ->set('medical_case_id', $this->case->id)
            ->set('session_number', 2)
            ->set('session_date', '2026-10-10')
            ->set('start_time', '09:30')
            ->set('end_time', '10:30')
            ->set('fee', 250000)
            ->call('save')
            ->assertHasErrors(['start_time']);
    }

    public function test_can_save_clinical_notes_and_lock_session(): void
    {
        $session = Session::create([
            'case_id' => $this->case->id,
            'medical_case_id' => $this->case->id,
            'session_number' => 1,
            'session_date' => '2026-10-10',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'fee' => 250000,
            'status' => 'scheduled',
            'is_locked' => false,
        ]);

        Livewire::test('sessions.session-show', ['id' => $session->id])
            ->set('summary', 'Ringkasan sesi 1')
            ->set('dynamic_notes', 'Dinamika emosi membaik')
            ->set('intervention_notes', 'Teknik CBT')
            ->set('recommendation', 'PR jurnal harian')
            ->call('saveNotes')
            ->assertHasNoErrors()
            ->call('lockSession')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('patient_sessions', [
            'id' => $session->id,
            'summary' => 'Dinamika emosi membaik',
            'is_locked' => true,
            'status' => 'done',
        ]);

        $this->assertDatabaseHas('session_notes', [
            'appointment_id' => $session->id,
            'is_locked' => true,
        ]);
    }

    public function test_session_show_pre_populates_subjective_notes_from_medical_case(): void
    {
        $caseWithNotes = MedicalCase::create([
            'case_code' => 'CAS-202610-099',
            'client_id' => $this->client->id,
            'title' => 'Kasus Kecemasan Akut',
            'category' => 'Anxiety',
            'subjective_complaint' => 'Kaki bergetar setiap presentasi',
            'subjective_problem' => 'Takut dinilai buruk oleh atasan',
            'status' => 'active',
        ]);

        $session = Session::create([
            'case_id' => $caseWithNotes->id,
            'medical_case_id' => $caseWithNotes->id,
            'session_number' => 1,
            'session_date' => '2026-10-12',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 200000,
            'status' => 'scheduled',
            'is_locked' => false,
        ]);

        Livewire::test('sessions.session-show', ['id' => $session->id])
            ->assertSet('subjective_complaint', 'Kaki bergetar setiap presentasi')
            ->assertSet('subjective_problem', 'Takut dinilai buruk oleh atasan')
            ->assertSet('summary', '')
            ->assertSet('objective', '')
            ->assertSet('dynamic_notes', '');
    }

    public function test_can_export_pdf_with_clinical_notes_and_custom_interventions(): void
    {
        $session = Session::create([
            'case_id' => $this->case->id,
            'medical_case_id' => $this->case->id,
            'session_number' => 1,
            'session_date' => '2026-10-12',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 200000,
            'status' => 'scheduled',
            'is_locked' => false,
        ]);

        $response = Livewire::test('sessions.session-show', ['id' => $session->id])
            ->set('subjective_complaint', 'Keluhan uji coba PDF')
            ->set('objective', 'Dinamika psikologis uji coba PDF')
            ->set('selected_interventions', [1, 3])
            ->call('saveNotes')
            ->call('exportPdf');

        $response->assertFileDownloaded();
    }

    public function test_pdf_report_view_renders_all_client_demographic_fields(): void
    {
        $this->client->update([
            'nik' => '3201019998880001',
            'last_education' => 'Sarjana (S1)',
            'birth_order' => 1,
            'total_siblings' => 3,
            'is_disabled' => true,
            'disability_description' => 'Tunarungu',
        ]);

        $session = Session::create([
            'case_id' => $this->case->id,
            'medical_case_id' => $this->case->id,
            'session_number' => 1,
            'session_date' => '2026-10-12',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 200000,
            'status' => 'scheduled',
            'is_locked' => false,
        ]);

        $note = \App\Models\SessionNote::create([
            'appointment_id' => $session->id,
            'patient_session_id' => $session->id,
            'subjective_complaint' => 'Keluhan PDF',
            'objective' => 'Objektif PDF',
            'assessment' => 'Asesmen PDF',
            'plan' => 'Rencana PDF',
            'duration_minutes' => 60,
        ]);

        $sessionWithRelations = Session::with(['patient', 'psychologist', 'medicalCase.client'])->find($session->id);

        $html = view('pdf.rpp-document', [
            'appointment' => $sessionWithRelations,
            'note' => $note,
            'interventions' => [1 => 'Psikoedukasi'],
        ])->render();

        $this->assertStringContainsString('3201019998880001', $html);
        $this->assertStringContainsString('Sarjana (S1)', $html);
        $this->assertStringContainsString('Anak ke-1 dari 3 bersaudara', $html);
        $this->assertStringContainsString('Ya (Tunarungu)', $html);
    }
}


