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
            'summary' => 'Ringkasan sesi 1',
            'is_locked' => true,
            'status' => 'done',
        ]);
    }
}
