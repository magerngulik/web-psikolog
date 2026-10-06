<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\Reports\RppVerifier;
use App\Livewire\Sessions\SessionShow;
use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use App\Models\Payment;
use App\Models\SessionNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class Modul7Test extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $client;
    protected $case;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'sipa_number' => '503/TEST-SIPA/2026'
        ]);

        $this->client = Client::create([
            'client_code' => 'CLI-2026-TEST',
            'full_name' => 'John Modul 7 Test',
            'gender' => 'Laki-laki',
            'date_of_birth' => '1995-05-15',
            'phone_number' => '08123456789'
        ]);

        $this->case = MedicalCase::create([
            'client_id' => $this->client->id,
            'case_code' => 'CAS-2026-TEST',
            'category' => 'Umum',
            'title' => 'Gangguan Kecemasan',
            'status' => 'active'
        ]);

        $this->session = PatientSession::create([
            'case_id' => $this->case->id,
            'session_number' => 1,
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'duration_minutes' => 60,
            'status' => 'done',
            'fee' => 150000,
            'payment_status' => 'paid'
        ]);

        session()->put('pin_unlocked', true);
    }

    public function test_rpp_route_redirects_to_session_show_page()
    {
        $this->get(route('rpp.edit', $this->session->id))
            ->assertRedirect(route('sessions.show', $this->session->id));

        $this->get(route('sessions.show', $this->session->id))
            ->assertStatus(200)
            ->assertSee('Intervensi Psikologis yang Diberikan')
            ->assertSee('Diagnosis Kustom / Catatan Diagnosis Bebas');
    }

    public function test_session_show_can_save_soap_notes_and_interventions()
    {
        Livewire::test(SessionShow::class, ['id' => $this->session->id])
            ->set('subjective_complaint', 'Pasien mengeluh cemas berlebihan menjelang wawancara kerja.')
            ->set('subjective_problem', 'Tekanan ekonomi keluarga dan ekspektasi orang tua.')
            ->set('objective', 'Pasien tampak gelisah, kontak mata kurang konsisten, denyut nadi 88 bpm.')
            ->set('selected_assessments', ['Paper & Pencil Test', 'Observasi'])
            ->set('assessment', 'Menunjukkan kecenderungan Generalized Anxiety Disorder.')
            ->set('plan', 'Diberikan teknik relaksasi napas dalam.')
            ->set('icd10_code', 'F41.1')
            ->set('icd10_description', 'Gangguan Kecemasan Menyeluruh')
            ->set('diagnosis_notes', 'Diagnosis banding F43.2 Gangguan Penyesuaian.')
            ->set('selected_interventions', [1, 3, 6])
            ->set('intervention_notes', 'Diberikan edukasi kognitif restrukturisasi pikiran irasional dan relaksasi diafragma.')
            ->set('client_message', 'Latihan pernapasan 4-7-8 setiap pagi dan malam hari selama 10 menit.')
            ->set('duration_minutes', 60)
            ->call('saveNotes')
            ->assertHasNoErrors()
            ->assertSee('Catatan rekam medis sesi berhasil diperbarui!');

        $this->assertDatabaseHas('session_notes', [
            'appointment_id' => $this->session->id,
            'subjective_complaint' => 'Pasien mengeluh cemas berlebihan menjelang wawancara kerja.',
            'subjective_problem' => 'Tekanan ekonomi keluarga dan ekspektasi orang tua.',
            'icd10_code' => 'F41.1',
            'icd10_description' => 'Gangguan Kecemasan Menyeluruh',
            'diagnosis_notes' => 'Diagnosis banding F43.2 Gangguan Penyesuaian.',
            'intervention_notes' => 'Diberikan edukasi kognitif restrukturisasi pikiran irasional dan relaksasi diafragma.',
        ]);
    }

    public function test_session_show_can_save_custom_free_text_diagnosis_without_dropdown()
    {
        Livewire::test(SessionShow::class, ['id' => $this->session->id])
            ->set('subjective_complaint', 'Keluhan pasien.')
            ->set('objective', 'Observasi objektif.')
            ->set('icd10_code', '')
            ->set('diagnosis_notes', 'Kondisi Burnout Kronis Akut & Kelelahan Emosional Belum Masuk PPDGJ')
            ->set('duration_minutes', 60)
            ->call('saveNotes')
            ->assertHasNoErrors()
            ->assertSee('Catatan rekam medis sesi berhasil diperbarui!');

        $this->assertDatabaseHas('session_notes', [
            'appointment_id' => $this->session->id,
            'icd10_code' => 'F99',
            'diagnosis_notes' => 'Kondisi Burnout Kronis Akut & Kelelahan Emosional Belum Masuk PPDGJ',
        ]);
    }

    public function test_session_show_can_search_and_select_ppdgj_diagnosis()
    {
        Livewire::test(SessionShow::class, ['id' => $this->session->id])
            ->set('icd_search', 'F41')
            ->call('selectDiagnosis', 'F41.1', 'Gangguan Kecemasan Menyeluruh (Generalized Anxiety Disorder)')
            ->assertSet('icd10_code', 'F41.1')
            ->assertSet('icd10_description', 'Gangguan Kecemasan Menyeluruh (Generalized Anxiety Disorder)')
            ->assertSet('show_icd_dropdown', false);
    }

    public function test_session_show_pdf_export_returns_stream_download()
    {
        SessionNote::create([
            'appointment_id' => $this->session->id,
            'subjective_complaint' => 'Keluhan utama test',
            'subjective_problem' => 'Masalah pemicu test',
            'objective' => 'Dinamika psikologis observasi test',
            'assessment_methods' => ['Observasi', 'Wawancara'],
            'assessment' => 'Assessment analysis test',
            'plan' => 'Plan test',
            'icd10_code' => 'F41.1',
            'icd10_description' => 'Gangguan Kecemasan Menyeluruh',
            'diagnosis_notes' => 'Catatan diagnosis kustom test',
            'intervention_ids' => [1, 3],
            'intervention_notes' => 'Catatan teknik intervensi relaksasi diafragma.',
            'client_message' => 'Tugas rumah latihan pernapasan.',
            'qr_code_token' => 'token-test-123456789'
        ]);

        Livewire::test(SessionShow::class, ['id' => $this->session->id])
            ->call('exportPdf')
            ->assertFileDownloaded("RPP_Konseling_{$this->client->full_name}_{$this->session->session_date}.pdf");
    }

    public function test_dashboard_analytics_calculates_metrics_correctly()
    {
        Payment::create([
            'patient_session_id' => $this->session->id,
            'amount' => 150000,
            'status' => 'Paid'
        ]);

        SessionNote::create([
            'appointment_id' => $this->session->id,
            'subjective' => 'S',
            'objective' => 'O',
            'assessment' => 'A',
            'plan' => 'P',
            'icd10_code' => 'F41.1',
            'icd10_description' => 'Anxiety',
            'is_locked' => false
        ]);

        Livewire::test(Dashboard::class)
            ->assertSet('totalSessionsMonth', 1)
            ->assertSet('unclosedNotesCount', 1)
            ->assertSet('monthlyRevenue', 150000)
            ->assertSee('F41.1');
    }

    public function test_rpp_verifier_validates_qr_code_token()
    {
        $token = 'VALID-TOKEN-999';

        SessionNote::create([
            'appointment_id' => $this->session->id,
            'subjective' => 'S',
            'objective' => 'O',
            'assessment' => 'A',
            'plan' => 'P',
            'icd10_code' => 'F41.1',
            'icd10_description' => 'Anxiety',
            'qr_code_token' => $token
        ]);

        Livewire::test(RppVerifier::class, ['token' => $token])
            ->assertSet('isValid', true)
            ->assertSee('DOKUMEN VALID DAN TERVERIFIKASI');

        Livewire::test(RppVerifier::class, ['token' => 'INVALID-TOKEN'])
            ->assertSet('isValid', false)
            ->assertSee('DOKUMEN TIDAK VALID');
    }
}
