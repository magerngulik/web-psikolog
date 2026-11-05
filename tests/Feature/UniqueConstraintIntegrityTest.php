<?php

namespace Tests\Feature;

use App\Livewire\Activities\ActivityCreate;
use App\Livewire\Cases\CaseCreate;
use App\Livewire\Clients\ClientCreate;
use App\Livewire\Organizations\OrganizationIndex;
use App\Livewire\Reports\AnnualLogbook;
use App\Livewire\Sessions\SessionShow;
use App\Models\Activity;
use App\Models\AnnualLogbookAdjustment;
use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\NationalHoliday;
use App\Models\Organization;
use App\Models\PatientSession;
use App\Models\SecurityKey;
use App\Models\SessionNote;
use App\Models\User;
use Database\Seeders\NationalHolidaySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UniqueConstraintIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->withSession(['pin_unlocked' => true]);
    }

    public function test_clients_table_client_code_never_collides_on_soft_delete_gaps_or_explicit_duplicate(): void
    {
        $prefix = 'CLI-'.date('Ym').'-';

        $c1 = Client::create(['full_name' => 'Klien 1', 'gender' => 'male']);
        $c2 = Client::create(['full_name' => 'Klien 2', 'gender' => 'female']);
        $c3 = Client::create(['full_name' => 'Klien 3', 'gender' => 'male']);

        $this->assertSame($prefix.'001', $c1->client_code);
        $this->assertSame($prefix.'002', $c2->client_code);
        $this->assertSame($prefix.'003', $c3->client_code);

        // Soft-delete middle and highest records so active count() is 1
        $c1->delete();
        $c3->delete();
        $this->assertSame(1, Client::count());

        // Create via Livewire component
        Livewire::test(ClientCreate::class)
            ->set('full_name', 'Klien 4 Via Livewire')
            ->set('gender', 'female')
            ->call('save')
            ->assertHasNoErrors();

        $c4 = Client::where('full_name', 'Klien 4 Via Livewire')->firstOrFail();
        $this->assertSame($prefix.'004', $c4->client_code);

        // Explicitly passing an already-used code (even a soft-deleted one like 003) auto-resolves to 005
        $c5 = Client::create([
            'client_code' => $prefix.'003',
            'full_name' => 'Klien 5 Duplikat Eksplisit',
            'gender' => 'male',
        ]);
        $this->assertSame($prefix.'005', $c5->client_code);
    }

    public function test_cases_table_case_code_never_collides_on_soft_delete_gaps_or_explicit_duplicate(): void
    {
        $prefix = 'CAS-'.date('Ym').'-';
        $client = Client::create(['full_name' => 'Klien Induk', 'gender' => 'female']);

        $case1 = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Kasus 1',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
        $case2 = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Kasus 2',
            'category' => 'Depression',
            'status' => 'active',
        ]);

        $this->assertSame($prefix.'001', $case1->case_code);
        $this->assertSame($prefix.'002', $case2->case_code);

        // Soft-delete latest case so active count() is 1
        $case2->delete();
        $this->assertSame(1, MedicalCase::count());

        Livewire::test(CaseCreate::class)
            ->set('client_id', $client->id)
            ->set('title', 'Kasus 3 Via Livewire')
            ->set('category', 'Anxiety')
            ->set('subjective_complaint', 'Keluhan')
            ->set('subjective_problem', 'Masalah')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $case3 = MedicalCase::where('title', 'Kasus 3 Via Livewire')->firstOrFail();
        $this->assertSame($prefix.'003', $case3->case_code);

        // Explicitly passing a soft-deleted case_code (002) auto-resolves to 004
        $case4 = MedicalCase::create([
            'case_code' => $prefix.'002',
            'client_id' => $client->id,
            'title' => 'Kasus 4 Duplikat Eksplisit',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
        $this->assertSame($prefix.'004', $case4->case_code);
    }

    public function test_activities_table_activity_code_never_collides_on_soft_delete_or_explicit_duplicate(): void
    {
        $prefix = 'ACT-'.date('Ym').'-';

        $act1 = Activity::create([
            'title' => 'Kegiatan 1',
            'event_type' => 'workshop',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);
        $act2 = Activity::create([
            'title' => 'Kegiatan 2',
            'event_type' => 'seminar',
            'role' => 'participant',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'online',
        ]);

        $this->assertSame($prefix.'001', $act1->activity_code);
        $this->assertSame($prefix.'002', $act2->activity_code);

        // Soft-delete both activities
        $act1->delete();
        $act2->delete();
        $this->assertSame(0, Activity::count());

        // Create via Livewire ActivityCreate
        Livewire::test(ActivityCreate::class)
            ->set('title', 'Kegiatan 3 Via Livewire')
            ->set('event_type', 'workshop')
            ->set('role', 'speaker')
            ->set('start_date', now()->toDateString())
            ->set('delivery_mode', 'offline')
            ->set('status', 'completed')
            ->call('save')
            ->assertHasNoErrors();

        $act3 = Activity::where('title', 'Kegiatan 3 Via Livewire')->firstOrFail();
        $this->assertSame($prefix.'003', $act3->activity_code);

        // Explicitly passing a duplicate activity_code auto-resolves to 004
        $act4 = Activity::create([
            'activity_code' => $prefix.'001',
            'title' => 'Kegiatan 4 Duplikat Eksplisit',
            'event_type' => 'workshop',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);
        $this->assertSame($prefix.'004', $act4->activity_code);
    }

    public function test_organizations_table_org_code_never_collides_on_soft_delete_or_explicit_duplicate(): void
    {
        $prefix = 'ORG-'.date('Ym').'-';

        $org1 = Organization::create(['name' => 'Instansi 1', 'category' => 'government']);
        $org2 = Organization::create(['name' => 'Instansi 2', 'category' => 'school']);

        $this->assertSame($prefix.'001', $org1->org_code);
        $this->assertSame($prefix.'002', $org2->org_code);

        // Soft-delete latest organization
        $org2->delete();
        $this->assertSame(1, Organization::count());

        Livewire::test(OrganizationIndex::class)
            ->set('name', 'Instansi 3 Via Livewire')
            ->set('category', 'corporate')
            ->call('save')
            ->assertHasNoErrors();

        $org3 = Organization::where('name', 'Instansi 3 Via Livewire')->firstOrFail();
        $this->assertSame($prefix.'003', $org3->org_code);

        // Explicitly passing a soft-deleted org_code (002) auto-resolves to 004
        $org4 = Organization::create([
            'org_code' => $prefix.'002',
            'name' => 'Instansi 4 Duplikat Eksplisit',
            'category' => 'ngo',
        ]);
        $this->assertSame($prefix.'004', $org4->org_code);
    }

    public function test_session_notes_table_qr_code_token_never_collides(): void
    {
        $client = Client::create(['full_name' => 'Klien Sesi', 'gender' => 'female']);
        $case = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Kasus Sesi',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);

        $session1 = PatientSession::create([
            'case_id' => $case->id,
            'session_number' => 1,
            'session_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'duration_minutes' => 60,
            'status' => 'done',
            'fee' => 150000,
            'payment_status' => 'paid',
        ]);

        $session2 = PatientSession::create([
            'case_id' => $case->id,
            'session_number' => 2,
            'session_date' => now()->toDateString(),
            'start_time' => '11:00',
            'end_time' => '12:00',
            'duration_minutes' => 60,
            'status' => 'done',
            'fee' => 150000,
            'payment_status' => 'paid',
        ]);

        $note1 = SessionNote::create([
            'appointment_id' => $session1->id,
            'qr_code_token' => 'DUPLICATE-TOKEN-TEST-123',
            'subjective_complaint' => 'Keluhan 1',
        ]);

        // Creating a second SessionNote with the exact same qr_code_token must auto-regenerate token
        $note2 = SessionNote::create([
            'appointment_id' => $session2->id,
            'qr_code_token' => 'DUPLICATE-TOKEN-TEST-123',
            'subjective_complaint' => 'Keluhan 2',
        ]);

        $this->assertSame('DUPLICATE-TOKEN-TEST-123', $note1->qr_code_token);
        $this->assertNotSame('DUPLICATE-TOKEN-TEST-123', $note2->qr_code_token);
        $this->assertNotEmpty($note2->qr_code_token);

        // Repeatedly saving via SessionShow Livewire component updates cleanly without token collision
        Livewire::test(SessionShow::class, ['id' => $session1->id])
            ->set('subjective_complaint', 'Keluhan Diperbarui 1')
            ->set('objective', 'Observasi 1')
            ->set('assessment', 'Asesmen 1')
            ->set('plan', 'Rencana 1')
            ->call('saveNotes')
            ->assertHasNoErrors()
            ->set('subjective_complaint', 'Keluhan Diperbarui 2')
            ->call('saveNotes')
            ->assertHasNoErrors();
    }

    public function test_national_holidays_table_holiday_date_never_collides_on_reseed_or_recreate(): void
    {
        // Running NationalHolidaySeeder twice must not collide
        $this->seed(NationalHolidaySeeder::class);
        $this->seed(NationalHolidaySeeder::class);

        $holiday = NationalHoliday::whereDate('holiday_date', '2026-01-01')->firstOrFail();
        $this->assertNotNull($holiday);

        // Creating a holiday on an already-existing date must replace cleanly without UniqueConstraintViolationException
        $recreated = NationalHoliday::create([
            'holiday_date' => '2026-01-01',
            'holiday_name' => 'Tahun Baru 2026 (Diperbarui)',
            'is_cuti_bersama' => false,
        ]);

        $this->assertDatabaseHas('national_holidays', [
            'id' => $recreated->id,
            'holiday_name' => 'Tahun Baru 2026 (Diperbarui)',
        ]);
        $this->assertSame(1, NationalHoliday::whereDate('holiday_date', '2026-01-01')->count());
    }

    public function test_annual_logbook_adjustments_composite_unique_key_updates_cleanly(): void
    {
        Livewire::test(AnnualLogbook::class)
            ->set('selectedYear', 2026)
            ->call('openAdjustmentModal')
            ->set('adj_activity_key', 'legal_visum')
            ->set('adj_month', 5)
            ->set('adj_count', 3)
            ->set('adj_notes', 'Input pertama')
            ->call('saveAdjustment')
            ->assertHasNoErrors()
            // Saving again for the exact same (year, activity_key, month) updates without unique violation
            ->call('openAdjustmentModal')
            ->set('adj_activity_key', 'legal_visum')
            ->set('adj_month', 5)
            ->set('adj_count', 7)
            ->set('adj_notes', 'Input kedua diperbarui')
            ->call('saveAdjustment')
            ->assertHasNoErrors();

        $this->assertSame(
            1,
            AnnualLogbookAdjustment::where('year', 2026)
                ->where('activity_key', 'legal_visum')
                ->where('month', 5)
                ->count()
        );

        $this->assertDatabaseHas('annual_logbook_adjustments', [
            'year' => 2026,
            'activity_key' => 'legal_visum',
            'month' => 5,
            'adjustment_count' => 7,
            'notes' => 'Input kedua diperbarui',
        ]);
    }

    public function test_users_table_email_unique_key_is_idempotent_on_seeding(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
    }
}
