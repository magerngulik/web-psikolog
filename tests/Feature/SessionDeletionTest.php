<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Sessions\SessionIndex;
use App\Livewire\Sessions\SessionShow;
use App\Livewire\Cases\CaseShow;

class SessionDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        session(['pin_unlocked' => true]);
    }

    private function createClientAndCase(): array
    {
        $client = Client::create([
            'client_code' => 'CLI-' . uniqid(),
            'full_name' => 'Klien Sesi ' . uniqid(),
            'gender' => 'female',
        ]);

        $case = MedicalCase::create([
            'case_code' => 'KASUS-' . uniqid(),
            'client_id' => $client->id,
            'title' => 'Masalah Kecemasan ' . uniqid(),
            'category' => 'Anxiety',
            'status' => 'active',
            'start_date' => now(),
        ]);

        return [$client, $case];
    }

    private function createSession(MedicalCase $case, int $sessionNumber = 1, bool $isLocked = false): PatientSession
    {
        return PatientSession::create([
            'case_id' => $case->id,
            'medical_case_id' => $case->id,
            'session_number' => $sessionNumber,
            'session_date' => '2026-10-25',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 300000,
            'status' => 'scheduled',
            'payment_status' => 'unpaid',
            'is_locked' => $isLocked,
        ]);
    }

    public function test_unlocked_session_can_be_soft_deleted_from_session_index(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, false);

        Livewire::test(SessionIndex::class)
            ->call('deleteSession', $session->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'success' && str_contains($params['message'], 'berhasil dipindahkan ke Tong Sampah');
            });

        $this->assertSoftDeleted('patient_sessions', ['id' => $session->id]);
    }

    public function test_locked_session_cannot_be_deleted_from_session_index(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, true);

        Livewire::test(SessionIndex::class)
            ->call('deleteSession', $session->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'error' && str_contains($params['message'], 'sudah TERKUNCI');
            });

        $this->assertNotSoftDeleted('patient_sessions', ['id' => $session->id]);
    }

    public function test_trashed_sessions_appear_in_trashed_filter(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $activeSession = $this->createSession($case, 1, false);
        $deletedSession = $this->createSession($case, 2, false);
        $deletedSession->delete();

        // Default view shows active, hides trashed
        Livewire::test(SessionIndex::class)
            ->assertSee('Sesi Ke-1', false)
            ->assertDontSee('Sesi Ke-2');

        // Trashed view shows trashed, hides active
        Livewire::test(SessionIndex::class)
            ->set('statusFilter', 'trashed')
            ->assertSee('Sesi Ke-2', false)
            ->assertDontSee('Sesi Ke-1')
            ->assertSee('Pulihkan Sesi')
            ->assertSee('Hapus Permanen');
    }

    public function test_trashed_session_can_be_restored(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, false);
        $session->delete();

        $this->assertSoftDeleted('patient_sessions', ['id' => $session->id]);

        Livewire::test(SessionIndex::class)
            ->set('statusFilter', 'trashed')
            ->call('restoreSession', $session->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'success' && str_contains($params['message'], 'berhasil dipulihkan');
            });

        $this->assertNotSoftDeleted('patient_sessions', ['id' => $session->id]);
    }

    public function test_trashed_session_can_be_force_deleted(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, false);
        $session->delete();

        Livewire::test(SessionIndex::class)
            ->set('statusFilter', 'trashed')
            ->call('forceDeleteSession', $session->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'success' && str_contains($params['message'], 'secara permanen');
            });

        $this->assertDatabaseMissing('patient_sessions', ['id' => $session->id]);
    }

    public function test_unlocked_session_can_be_deleted_from_session_show(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, false);

        Livewire::test(SessionShow::class, ['id' => $session->id])
            ->call('deleteSession')
            ->assertRedirect(route('sessions.index'));

        $this->assertSoftDeleted('patient_sessions', ['id' => $session->id]);
    }

    public function test_locked_session_cannot_be_deleted_from_session_show(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, true);

        Livewire::test(SessionShow::class, ['id' => $session->id])
            ->call('deleteSession')
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'error' && str_contains($params['message'], 'sudah TERKUNCI');
            });

        $this->assertNotSoftDeleted('patient_sessions', ['id' => $session->id]);
    }

    public function test_session_can_be_deleted_from_case_show_and_enables_case_deletion(): void
    {
        [$client, $case] = $this->createClientAndCase();
        $session = $this->createSession($case, 1, false);

        // Before deleting session, case cannot be deleted
        Livewire::test(CaseShow::class, ['id' => $case->id])
            ->call('deleteCase')
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'error' && str_contains($params['message'], 'tidak dapat dihapus');
            });
        $this->assertNotSoftDeleted('cases', ['id' => $case->id]);

        // Delete the session from CaseShow
        Livewire::test(CaseShow::class, ['id' => $case->id])
            ->call('deleteSession', $session->id)
            ->assertDispatched('notify', function ($name, $params) {
                return $params['type'] === 'success' && str_contains($params['message'], 'berhasil dipindahkan ke Tong Sampah');
            });
        $this->assertSoftDeleted('patient_sessions', ['id' => $session->id]);

        // Now that the session is deleted, the case CAN be deleted!
        Livewire::test(CaseShow::class, ['id' => $case->id])
            ->call('deleteCase')
            ->assertRedirect(route('cases.index'));
        $this->assertSoftDeleted('cases', ['id' => $case->id]);
    }
}

