<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Cases\CaseIndex;
use App\Livewire\Cases\CaseShow;

class CaseDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        session(['pin_unlocked' => true]);
    }

    private function createClient(): Client
    {
        return Client::create([
            'client_code' => 'CLI-' . uniqid(),
            'full_name' => 'Klien Uji Hapus',
            'gender' => 'female',
        ]);
    }

    public function test_case_without_sessions_can_be_soft_deleted_from_index(): void
    {
        $client = $this->createClient();
        $case = MedicalCase::create([
            'case_code' => 'KASUS-DEL-001',
            'client_id' => $client->id,
            'title' => 'Salah Input Kasus A',
            'category' => 'Anxiety',
            'status' => 'active',
            'start_date' => now(),
        ]);

        Livewire::test(CaseIndex::class)
            ->call('deleteCase', $case->id)
            ->assertDispatched('notify');

        $this->assertSoftDeleted('cases', ['id' => $case->id]);
    }

    public function test_case_with_sessions_cannot_be_deleted_from_index(): void
    {
        $client = $this->createClient();
        $case = MedicalCase::create([
            'case_code' => 'KASUS-DEL-002',
            'client_id' => $client->id,
            'title' => 'Kasus Dengan Sesi B',
            'category' => 'Depression',
            'status' => 'active',
            'start_date' => now(),
        ]);

        PatientSession::create([
            'medical_case_id' => $case->id,
            'session_number' => 1,
            'session_date' => '2026-10-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'fee' => 250000,
            'status' => 'scheduled',
            'payment_status' => 'unpaid',
            'is_locked' => false,
        ]);

        Livewire::test(CaseIndex::class)
            ->call('deleteCase', $case->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'error' && str_contains($params['message'], 'tidak dapat dihapus');
            });

        $this->assertNotSoftDeleted('cases', ['id' => $case->id]);
    }

    public function test_trashed_cases_appear_in_trashed_filter(): void
    {
        $client = $this->createClient();
        $activeCase = MedicalCase::create([
            'case_code' => 'KASUS-ACT-001',
            'client_id' => $client->id,
            'title' => 'Kasus Aktif Tetap Tampil',
            'category' => 'Family',
            'status' => 'active',
        ]);

        $deletedCase = MedicalCase::create([
            'case_code' => 'KASUS-DEL-003',
            'client_id' => $client->id,
            'title' => 'Kasus Terhapus Masuk Sampah',
            'category' => 'Family',
            'status' => 'active',
        ]);
        $deletedCase->delete();

        // Default view shows active cases, not trashed
        Livewire::test(CaseIndex::class)
            ->assertSee('Kasus Aktif Tetap Tampil')
            ->assertDontSee('Kasus Terhapus Masuk Sampah');

        // Trashed filter shows only trashed cases
        Livewire::test(CaseIndex::class)
            ->set('statusFilter', 'trashed')
            ->assertSee('Kasus Terhapus Masuk Sampah')
            ->assertDontSee('Kasus Aktif Tetap Tampil')
            ->assertSee('Pulihkan Kasus')
            ->assertSee('Hapus Permanen');
    }

    public function test_trashed_case_can_be_restored(): void
    {
        $client = $this->createClient();
        $case = MedicalCase::create([
            'case_code' => 'KASUS-RES-001',
            'client_id' => $client->id,
            'title' => 'Kasus Ingin Dipulihkan',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
        $case->delete();

        $this->assertSoftDeleted('cases', ['id' => $case->id]);

        Livewire::test(CaseIndex::class)
            ->set('statusFilter', 'trashed')
            ->call('restoreCase', $case->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'success' && str_contains($params['message'], 'berhasil dipulihkan');
            });

        $this->assertNotSoftDeleted('cases', ['id' => $case->id]);
    }

    public function test_trashed_case_can_be_force_deleted(): void
    {
        $client = $this->createClient();
        $case = MedicalCase::create([
            'case_code' => 'KASUS-FRC-001',
            'client_id' => $client->id,
            'title' => 'Kasus Ingin Dimusnahkan',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
        $case->delete();

        Livewire::test(CaseIndex::class)
            ->set('statusFilter', 'trashed')
            ->call('forceDeleteCase', $case->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'success' && str_contains($params['message'], 'secara permanen');
            });

        $this->assertDatabaseMissing('cases', ['id' => $case->id]);
    }

    public function test_case_without_sessions_can_be_deleted_from_case_show(): void
    {
        $client = $this->createClient();
        $case = MedicalCase::create([
            'case_code' => 'KASUS-SHW-001',
            'client_id' => $client->id,
            'title' => 'Hapus Dari Detail Kasus',
            'category' => 'Work & Career',
            'status' => 'active',
        ]);

        Livewire::test(CaseShow::class, ['id' => $case->id])
            ->call('deleteCase')
            ->assertRedirect(route('cases.index'));

        $this->assertSoftDeleted('cases', ['id' => $case->id]);
    }

    public function test_case_with_sessions_cannot_be_deleted_from_case_show(): void
    {
        $client = $this->createClient();
        $case = MedicalCase::create([
            'case_code' => 'KASUS-SHW-002',
            'client_id' => $client->id,
            'title' => 'Kasus Detail Ada Sesi',
            'category' => 'Work & Career',
            'status' => 'active',
        ]);

        PatientSession::create([
            'medical_case_id' => $case->id,
            'session_number' => 1,
            'session_date' => '2026-10-21',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 250000,
            'status' => 'scheduled',
            'payment_status' => 'unpaid',
            'is_locked' => false,
        ]);

        Livewire::test(CaseShow::class, ['id' => $case->id])
            ->call('deleteCase')
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'error' && str_contains($params['message'], 'tidak dapat dihapus');
            });

        $this->assertNotSoftDeleted('cases', ['id' => $case->id]);
    }
}
