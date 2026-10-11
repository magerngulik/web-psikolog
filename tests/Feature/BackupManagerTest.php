<?php

namespace Tests\Feature;

use App\Livewire\Settings\BackupManager;
use App\Models\Client;
use App\Models\SecurityKey;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BackupManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        session(['pin_unlocked' => true]);
    }

    public function test_backup_page_requires_pin_unlocked_session(): void
    {
        session()->forget('pin_unlocked');
        $response = $this->get(route('settings.backup'));
        $response->assertRedirect(route('lock-screen'));
    }

    public function test_backup_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('settings.backup'));

        $response->assertStatus(200);
        $response->assertSeeText('Backup & Restore Database');
    }

    public function test_can_create_server_backup_from_component(): void
    {
        Livewire::test(BackupManager::class)
            ->call('createServerBackup')
            ->assertHasNoErrors()
            ->assertSee('Backup baru berhasil dibuat');

        $service = new DatabaseBackupService;
        $this->assertNotEmpty($service->listBackups());
    }

    public function test_can_update_schedule_settings(): void
    {
        Livewire::test(BackupManager::class)
            ->set('frequency', 'weekly')
            ->set('retention', 24)
            ->call('saveSchedule')
            ->assertHasNoErrors()
            ->assertSee('Pengaturan jadwal backup otomatis berhasil diperbarui');

        $service = new DatabaseBackupService;
        $config = $service->getScheduleConfig();

        $this->assertEquals('weekly', $config['frequency']);
        $this->assertEquals(24, $config['retention']);
    }

    public function test_can_delete_backup_file(): void
    {
        $service = new DatabaseBackupService;
        $backup = $service->createBackupFile('test');

        Livewire::test(BackupManager::class)
            ->call('deleteBackupFile', $backup['filename'])
            ->assertHasNoErrors()
            ->assertSee('berhasil dihapus');

        Storage::disk('local')->assertMissing($backup['path']);
    }

    public function test_restore_fails_with_invalid_pin(): void
    {
        $service = new DatabaseBackupService;
        $backup = $service->createBackupFile('test');

        Livewire::test(BackupManager::class)
            ->call('openRestoreExisting', $backup['filename'])
            ->set('confirmRestore', true)
            ->set('restorePin', '000000') // Wrong PIN
            ->call('executeRestore')
            ->assertHasErrors(['restorePin']);
    }

    public function test_restore_succeeds_with_valid_pin_and_confirmation(): void
    {
        Client::create([
            'client_code' => 'CLI-BACKUP-TEST',
            'full_name' => 'Original Patient Data',
            'gender' => 'male',
        ]);

        $service = new DatabaseBackupService;
        $backup = $service->createBackupFile('valid_backup');

        // Delete original and create corrupted
        Client::query()->forceDelete();
        Client::create([
            'client_code' => 'CLI-CORRUPT',
            'full_name' => 'Corrupted Patient Data',
            'gender' => 'female',
        ]);

        Livewire::test(BackupManager::class)
            ->call('openRestoreExisting', $backup['filename'])
            ->set('confirmRestore', true)
            ->set('restorePin', '123456') // Correct PIN
            ->call('executeRestore')
            ->assertHasNoErrors()
            ->assertSee('Database berhasil dipulihkan');

        $this->assertDatabaseHas('clients', ['full_name' => 'Original Patient Data']);
        $this->assertDatabaseMissing('clients', ['full_name' => 'Corrupted Patient Data']);
    }

    public function test_can_upload_sql_and_restore_with_pin(): void
    {
        $service = new DatabaseBackupService;
        $validSql = $service->exportSql();

        $uploadedFile = UploadedFile::fake()->createWithContent('backup_upload.sql', $validSql);

        Livewire::test(BackupManager::class)
            ->set('sqlFile', $uploadedFile)
            ->call('openRestoreUploaded')
            ->assertSet('showRestoreModal', true)
            ->set('confirmRestore', true)
            ->set('restorePin', '123456')
            ->call('executeRestore')
            ->assertHasNoErrors()
            ->assertSee('Database berhasil dipulihkan');
    }
}
