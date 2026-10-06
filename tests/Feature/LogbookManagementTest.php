<?php

namespace Tests\Feature;

use App\Livewire\Reports\AnnualLogbook;
use App\Livewire\Sessions\SessionCreate;
use App\Livewire\Sessions\SessionShow;
use App\Livewire\Settings\ProfileSettings;
use App\Models\AnnualLogbookAdjustment;
use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use App\Models\SecurityKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LogbookManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $psychologist;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->psychologist = User::create([
            'name' => 'Basirah',
            'title_prefix' => '',
            'title_suffix' => 'S.Psi., M.Psi., Psikolog',
            'sipa_number' => '503/123-SIPA/2026',
            'practice_name' => 'MANDIRI',
            'practice_city' => 'Selatpanjang',
            'practice_address' => 'Jl. Merdeka No. 45',
            'phone' => '081234567890',
            'email' => 'basirah@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_profile_settings_page_is_accessible_and_can_be_updated(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('settings.profile'));

        $response->assertStatus(200);

        Livewire::test(ProfileSettings::class)
            ->set('name', 'Basirah Ahmad')
            ->set('title_prefix', 'Dra.')
            ->set('title_suffix', 'M.Psi., Psikolog')
            ->set('sipa_number', 'SIPA-999-2026')
            ->set('practice_name', 'KLINIK PSIKOLOGI MANDIRI')
            ->set('practice_city', 'Selatpanjang')
            ->set('practice_address', 'Jl. Diponegoro No. 10')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('berhasil');

        $this->assertDatabaseHas('users', [
            'name' => 'Basirah Ahmad',
            'title_prefix' => 'Dra.',
            'title_suffix' => 'M.Psi., Psikolog',
            'sipa_number' => 'SIPA-999-2026',
            'practice_name' => 'KLINIK PSIKOLOGI MANDIRI',
            'practice_city' => 'Selatpanjang',
        ]);

        $user = User::first();
        $this->assertEquals('Dra. Basirah Ahmad, M.Psi., Psikolog', $user->formatted_name);
    }

    public function test_session_create_and_show_with_modality_and_skp_flags(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-TEST-001',
            'full_name' => 'Budi Santoso',
            'nik' => '1401234567890001',
            'birth_date' => '1995-05-15',
            'gender' => 'male',
            'phone' => '08123456789',
        ]);

        $case = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Gangguan Kecemasan',
            'category' => 'clinical',
            'status' => 'active',
        ]);

        // 1. Create Session with modality phone & high-risk
        Livewire::test(SessionCreate::class, ['case_id' => $case->id])
            ->set('medical_case_id', $case->id)
            ->set('session_number', 1)
            ->set('session_date', '2026-03-10')
            ->set('start_time', '09:00')
            ->set('end_time', '10:00')
            ->set('fee', 200000)
            ->set('status', 'scheduled')
            ->set('payment_status', 'unpaid')
            ->set('payment_method', 'cash')
            ->set('service_modality', 'phone')
            ->set('is_high_risk', true)
            ->set('generates_report', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('patient_sessions', [
            'medical_case_id' => $case->id,
            'service_modality' => 'phone',
            'is_high_risk' => true,
            'generates_report' => true,
        ]);

        $session = PatientSession::first();

        // 2. Edit session modality in SessionShow
        Livewire::test(SessionShow::class, ['id' => $session->id])
            ->set('service_modality', 'legal_visum')
            ->set('is_high_risk', false)
            ->set('status', 'done')
            ->call('saveNotes')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('patient_sessions', [
            'id' => $session->id,
            'service_modality' => 'legal_visum',
            'is_high_risk' => false,
            'status' => 'done',
        ]);
    }

    public function test_annual_logbook_aggregates_sessions_and_calculates_skp_correctly(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-TEST-002',
            'full_name' => 'Dewi Lestari',
            'nik' => '1401234567890002',
            'birth_date' => '1998-02-20',
            'gender' => 'female',
            'phone' => '08123456780',
        ]);

        $case = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Depresi Ringan',
            'category' => 'clinical',
            'status' => 'active',
        ]);

        // Session 1: Januari 2026, individual direct (0.05 SKP) + report (0.01 SKP) = 0.06 SKP
        PatientSession::create([
            'case_id' => $case->id,
            'medical_case_id' => $case->id,
            'session_number' => 1,
            'session_date' => '2026-01-15',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'fee' => 250000,
            'status' => 'done',
            'service_modality' => 'individual_direct',
            'is_high_risk' => false,
            'generates_report' => true,
        ]);

        // Session 2: Januari 2026, group (0.10 SKP)
        PatientSession::create([
            'case_id' => $case->id,
            'medical_case_id' => $case->id,
            'session_number' => 2,
            'session_date' => '2026-01-20',
            'start_time' => '14:00',
            'end_time' => '15:30',
            'fee' => 500000,
            'status' => 'done',
            'service_modality' => 'group',
            'is_high_risk' => false,
            'generates_report' => false,
        ]);

        // Session 3: Februari 2026, phone (0.02 SKP) + high-risk (0.05 SKP) = 0.07 SKP
        PatientSession::create([
            'case_id' => $case->id,
            'medical_case_id' => $case->id,
            'session_number' => 3,
            'session_date' => '2026-02-10',
            'start_time' => '10:00',
            'end_time' => '10:30',
            'fee' => 150000,
            'status' => 'done',
            'service_modality' => 'phone',
            'is_high_risk' => true,
            'generates_report' => false,
        ]);

        // Add manual adjustment for legal_visum in Februari (Count: 2 * 0.02 = 0.04 SKP)
        AnnualLogbookAdjustment::create([
            'year' => 2026,
            'activity_key' => 'legal_visum',
            'month' => 2,
            'adjustment_count' => 2,
            'notes' => 'Visum korban di RSUD',
        ]);

        $logbook = AnnualLogbook::calculateLogbookData(2026);

        // Verification:
        // Individual: Jan=1, Total=1, SKP=0.05
        $this->assertEquals(1, $logbook['matrix']['individual']['months'][1]);
        $this->assertEquals(1, $logbook['matrix']['individual']['total_count']);
        $this->assertEquals(0.05, $logbook['matrix']['individual']['total_skp']);

        // Group: Jan=1, Total=1, SKP=0.10
        $this->assertEquals(1, $logbook['matrix']['group']['months'][1]);
        $this->assertEquals(1, $logbook['matrix']['group']['total_count']);
        $this->assertEquals(0.10, $logbook['matrix']['group']['total_skp']);

        // Phone: Feb=1, Total=1, SKP=0.02
        $this->assertEquals(1, $logbook['matrix']['phone']['months'][2]);
        $this->assertEquals(0.02, $logbook['matrix']['phone']['total_skp']);

        // High Risk: Feb=1, Total=1, SKP=0.05
        $this->assertEquals(1, $logbook['matrix']['high_risk']['months'][2]);
        $this->assertEquals(0.05, $logbook['matrix']['high_risk']['total_skp']);

        // Report: Jan=1, Total=1, SKP=0.01
        $this->assertEquals(1, $logbook['matrix']['report']['months'][1]);
        $this->assertEquals(0.01, $logbook['matrix']['report']['total_skp']);

        // Legal Visum (from adjustment): Feb=2, Total=2, SKP=0.04
        $this->assertEquals(2, $logbook['matrix']['legal_visum']['months'][2]);
        $this->assertEquals(0.04, $logbook['matrix']['legal_visum']['total_skp']);

        // Total SKP = 0.05 + 0.10 + 0.02 + 0.05 + 0.01 + 0.04 = 0.27
        $this->assertEquals(0.27, $logbook['grandTotalSkp']);
    }

    public function test_can_manage_logbook_manual_adjustments(): void
    {
        Livewire::test(AnnualLogbook::class)
            ->set('selectedYear', 2026)
            ->call('openAdjustmentModal')
            ->set('adj_activity_key', 'legal_witness')
            ->set('adj_month', 5)
            ->set('adj_count', 3)
            ->set('adj_notes', 'Saksi ahli di Pengadilan Negeri')
            ->call('saveAdjustment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('annual_logbook_adjustments', [
            'year' => 2026,
            'activity_key' => 'legal_witness',
            'month' => 5,
            'adjustment_count' => 3,
        ]);

        $adj = AnnualLogbookAdjustment::first();

        // Edit
        Livewire::test(AnnualLogbook::class)
            ->set('selectedYear', 2026)
            ->call('openAdjustmentModal', $adj->id)
            ->set('adj_count', 5)
            ->call('saveAdjustment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('annual_logbook_adjustments', [
            'id' => $adj->id,
            'adjustment_count' => 5,
        ]);

        // Delete
        Livewire::test(AnnualLogbook::class)
            ->set('selectedYear', 2026)
            ->call('deleteAdjustment', $adj->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('annual_logbook_adjustments', [
            'id' => $adj->id,
        ]);
    }

    public function test_annual_logbook_pdf_download(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('reports.logbook.pdf', ['year' => 2026]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_rpp_pdf_contains_psychologist_practice_profile_and_city(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-TEST-003',
            'full_name' => 'Fajar Nugraha',
            'nik' => '1401234567890003',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'phone' => '08123456781',
        ]);

        $case = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Stres Kerja',
            'category' => 'clinical',
            'status' => 'active',
        ]);

        $session = PatientSession::create([
            'case_id' => $case->id,
            'medical_case_id' => $case->id,
            'session_number' => 1,
            'session_date' => '2026-03-15',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 300000,
            'status' => 'done',
        ]);

        $component = Livewire::test(SessionShow::class, ['id' => $session->id])
            ->set('subjective_complaint', 'Merasa kelelahan mental berat')
            ->call('saveNotes');

        $response = $component->call('exportPdf');
        $this->assertNotNull($response);
    }
}
