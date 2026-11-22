<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);
    }

    public function test_dashboard_is_accessible_when_pin_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_dashboard_renders_livewire_component(): void
    {
        Livewire::test('dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard Analytics');
    }

    public function test_recently_saved_session_appears_first_on_dashboard_even_with_future_sessions(): void
    {
        $client = Client::create([
            'full_name' => 'Klien Uji Dashboard',
            'gender' => 'F',
            'birth_date' => '1995-01-01',
            'phone' => '081234567890',
        ]);

        $case = MedicalCase::create([
            'client_id' => $client->id,
            'title' => 'Kasus Uji Dashboard',
            'category' => 'Klinis Dewasa',
            'status' => 'active',
        ]);

        // Older session date, edited most recently
        $olderSession = PatientSession::create([
            'case_id' => $case->id,
            'session_number' => 1,
            'session_date' => now()->subDays(5)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'scheduled',
            'fee' => 300000,
            'payment_status' => 'unpaid',
            'is_locked' => false,
        ]);

        // Future session created earlier
        $futureSession = PatientSession::create([
            'case_id' => $case->id,
            'session_number' => 2,
            'session_date' => now()->addDays(14)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'scheduled',
            'fee' => 300000,
            'payment_status' => 'paid',
            'is_locked' => false,
        ]);

        // Force olderSession to have a newer updated_at (simulating just saved notes)
        $olderSession->forceFill(['updated_at' => now()->addMinute()])->saveQuietly();

        $component = Livewire::test('dashboard');
        $recent = $component->get('recentAppointments');

        $this->assertEquals($olderSession->id, $recent->first()->id);
        $this->assertEquals(1, $component->get('unpaidSessionsCount'));
        $this->assertEquals(300000.0, $component->get('unpaidSessionsTotal'));
    }
}
