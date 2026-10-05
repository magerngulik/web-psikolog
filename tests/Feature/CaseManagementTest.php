<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class CaseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Klien Test Kasus',
            'gender' => 'female',
        ]);
    }

    public function test_case_index_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('cases.index'));

        $response->assertStatus(200);
    }

    public function test_case_create_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('cases.create'));

        $response->assertStatus(200);
    }

    public function test_can_create_new_medical_case(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('cases.case-create')
            ->set('client_id', $this->client->id)
            ->set('title', 'Kecemasan Menghadapi Karir Baru')
            ->set('category', 'Anxiety')
            ->set('complaint', 'Serang kecemasan saat berbicara di depan publik')
            ->set('goal', 'Menurunkan skala kecemasan dari 8 ke 3')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cases', [
            'client_id' => $this->client->id,
            'title' => 'Kecemasan Menghadapi Karir Baru',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
    }

    public function test_can_search_and_filter_medical_cases(): void
    {
        $caseA = MedicalCase::create([
            'case_code' => 'CAS-202610-001',
            'client_id' => $this->client->id,
            'title' => 'Kecemasan Pekerjaan',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);

        $caseB = MedicalCase::create([
            'case_code' => 'CAS-202610-002',
            'client_id' => $this->client->id,
            'title' => 'Depresi Ringan',
            'category' => 'Depression',
            'status' => 'completed',
        ]);

        Livewire::test('cases.case-index')
            ->set('search', 'Kecemasan')
            ->assertSee('Kecemasan Pekerjaan')
            ->assertDontSee('Depresi Ringan');

        Livewire::test('cases.case-index')
            ->set('statusFilter', 'completed')
            ->assertSee('Depresi Ringan')
            ->assertDontSee('Kecemasan Pekerjaan');
    }

    public function test_can_update_case_status_and_progress_note(): void
    {
        $case = MedicalCase::create([
            'case_code' => 'CAS-202610-001',
            'client_id' => $this->client->id,
            'title' => 'Kasus Update Test',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);

        Livewire::test('cases.case-show', ['id' => $case->id])
            ->set('progressNote', 'Catatan perkembangan terapi sesi 1 membaik.')
            ->call('updateProgress')
            ->assertHasNoErrors()
            ->call('updateStatus', 'completed')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'status' => 'completed',
            'progress_note' => 'Catatan perkembangan terapi sesi 1 membaik.',
        ]);
    }
}
