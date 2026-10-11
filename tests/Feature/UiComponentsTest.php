<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use App\Models\Activity;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Clients\ClientCreate;
use App\Livewire\Clients\ClientEdit;
use App\Livewire\Sessions\SessionCreate;
use App\Livewire\Sessions\SessionShow;
use App\Livewire\Activities\ActivityCreate;

class UiComponentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        session(['pin_unlocked' => true]);
    }

    public function test_client_create_renders_datepicker_with_max_date_today(): void
    {
        $response = $this->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee("maxDate: 'today'", false);
        $response->assertSee('id="client_dob"', false);

        // Submit client with date of birth
        Livewire::test(ClientCreate::class)
            ->set('full_name', 'Budi Perkasa')
            ->set('gender', 'male')
            ->set('date_of_birth', '1990-08-17')
            ->call('save')
            ->assertHasNoErrors();

        $client = Client::where('full_name', 'Budi Perkasa')->first();
        $this->assertNotNull($client);
        $this->assertEquals('1990-08-17', $client->date_of_birth->format('Y-m-d'));
    }

    public function test_session_create_renders_datepicker_and_currency_input(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Dewi Lestari',
            'gender' => 'female',
        ]);

        $medicalCase = MedicalCase::create([
            'case_code' => 'KASUS-202610-001',
            'client_id' => $client->id,
            'title' => 'Kecemasan Ujian',
            'category' => 'dewasa',
            'status' => 'open',
            'start_date' => now(),
        ]);

        $response = $this->get(route('sessions.create'));
        $response->assertStatus(200);

        // Check date picker and currency input are present
        $response->assertSee('id="session_date"', false);
        $response->assertSee('id="fee"', false);
        $response->assertSee('Rp');

        // Create session with date and fee
        Livewire::test(SessionCreate::class)
            ->set('medical_case_id', $medicalCase->id)
            ->set('session_number', 1)
            ->set('session_date', '2026-10-25')
            ->set('start_time', '10:00')
            ->set('end_time', '11:00')
            ->set('fee', 350000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('patient_sessions', [
            'medical_case_id' => $medicalCase->id,
            'session_date' => '2026-10-25',
            'fee' => 350000,
        ]);
    }

    public function test_session_show_updates_fee_and_dispatches_notify_event(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-002',
            'full_name' => 'Rian Hidayat',
            'gender' => 'male',
        ]);

        $medicalCase = MedicalCase::create([
            'case_code' => 'KASUS-202610-002',
            'client_id' => $client->id,
            'title' => 'Insomnia Klinis',
            'category' => 'dewasa',
            'status' => 'open',
            'start_date' => now(),
        ]);

        $session = PatientSession::create([
            'medical_case_id' => $medicalCase->id,
            'session_number' => 1,
            'session_date' => '2026-10-15',
            'start_time' => '14:00',
            'end_time' => '15:00',
            'fee' => 250000,
            'status' => 'scheduled',
            'payment_status' => 'unpaid',
            'is_locked' => false,
        ]);

        $response = $this->get(route('sessions.show', $session->id));
        $response->assertStatus(200);
        $response->assertSee('id="session_fee"', false);

        // Test updating fee via Livewire component
        Livewire::test(SessionShow::class, ['id' => $session->id])
            ->set('fee', 400000)
            ->call('saveNotes')
            ->assertDispatched('notify');

        $session->refresh();
        $this->assertEquals(400000, $session->fee);
    }

    public function test_activity_create_renders_datepickers_and_currency_input(): void
    {
        $response = $this->get(route('activities.create'));
        $response->assertStatus(200);

        $response->assertSee('id="start_date"', false);
        $response->assertSee('id="end_date"', false);
        $response->assertSee('id="fee"', false);

        Livewire::test(ActivityCreate::class)
            ->set('title', 'Seminar Kesehatan Mental Remaja')
            ->set('event_type', 'seminar')
            ->set('start_date', '2026-11-01')
            ->set('end_date', '2026-11-02')
            ->set('fee', 1500000)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('activities', [
            'title' => 'Seminar Kesehatan Mental Remaja',
            'fee' => 1500000,
        ]);
    }

    public function test_global_floating_toast_notification_renders_at_top_right(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('fixed top-6 right-6 z-[9999]', false);
    }

    public function test_datepicker_has_wire_ignore_and_year_dropdown_setup(): void
    {
        $response = $this->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('wire:ignore', false);
        $response->assertSee('setupYearDropdown', false);
        $response->assertSee('flatpickr-monthDropdown-years', false);
    }

    public function test_session_create_preserves_session_date_on_schedule_overlap_validation_error(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-099',
            'full_name' => 'Bintang Pratama',
            'gender' => 'male',
        ]);

        $medicalCase = MedicalCase::create([
            'case_code' => 'KASUS-202610-099',
            'client_id' => $client->id,
            'title' => 'Stres Kerja Berat',
            'category' => 'dewasa',
            'status' => 'open',
            'start_date' => now(),
        ]);

        // Create an existing session
        PatientSession::create([
            'medical_case_id' => $medicalCase->id,
            'session_number' => 1,
            'session_date' => '2026-10-25',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 300000,
            'status' => 'scheduled',
            'payment_status' => 'unpaid',
            'is_locked' => false,
        ]);

        // Attempt to create overlapping session
        Livewire::test(SessionCreate::class)
            ->set('medical_case_id', $medicalCase->id)
            ->set('session_number', 2)
            ->set('session_date', '2026-10-25')
            ->set('start_time', '10:00')
            ->set('end_time', '11:00')
            ->set('fee', 300000)
            ->call('save')
            ->assertHasErrors(['start_time'])
            ->assertSee('WARNING: Terjadi bentrok jadwal')
            ->assertSet('session_date', '2026-10-25');
    }
}
