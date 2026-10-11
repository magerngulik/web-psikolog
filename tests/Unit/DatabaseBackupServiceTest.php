<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\SecurityKey;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseBackupServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DatabaseBackupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->service = new DatabaseBackupService;
    }

    public function test_export_sql_generates_valid_schema_and_inserts(): void
    {
        Client::create([
            'client_code' => 'CLI-TEST-001',
            'full_name' => 'John Doe Psikotes',
            'gender' => 'male',
        ]);

        $sql = $this->service->exportSql();

        $this->assertNotEmpty($sql);
        $this->assertStringContainsString('Clinical Sanctuary Database Backup', $sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertStringContainsString('John Doe Psikotes', $sql);
    }

    public function test_create_backup_file_saves_to_storage(): void
    {
        $result = $this->service->createBackupFile('test_backup');

        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('size', $result);

        Storage::disk('local')->assertExists($result['path']);

        $backups = $this->service->listBackups();
        $this->assertCount(1, $backups);
        $this->assertEquals($result['filename'], $backups[0]['filename']);
    }

    public function test_clean_old_backups_respects_retention(): void
    {
        // Create 4 regular backups
        for ($i = 0; $i < 4; $i++) {
            Storage::disk('local')->put("backups/backup_sqlite_2026-10-0{$i}_000000.sql", "-- test {$i}");
            // Sleep slightly or manually touch lastModified
            touch(Storage::disk('local')->path("backups/backup_sqlite_2026-10-0{$i}_000000.sql"), time() - (4 - $i) * 3600);
        }

        $listBefore = $this->service->listBackups();
        $this->assertCount(4, $listBefore);

        // Keep 2
        $deleted = $this->service->cleanOldBackups(2);
        $this->assertEquals(2, $deleted);

        $listAfter = $this->service->listBackups();
        $this->assertCount(2, $listAfter);
    }

    public function test_save_and_get_schedule_config(): void
    {
        $this->service->saveScheduleConfig('weekly', 24);

        $config = $this->service->getScheduleConfig();
        $this->assertEquals('weekly', $config['frequency']);
        $this->assertEquals(24, $config['retention']);
    }

    public function test_should_run_scheduled_backup(): void
    {
        // Frequency disabled -> should NOT run
        $this->service->saveScheduleConfig('disabled', 12);
        $this->assertFalse($this->service->shouldRunScheduledBackup());

        // Frequency monthly with no previous run -> SHOULD run
        $this->service->saveScheduleConfig('monthly', 12);
        $this->assertTrue($this->service->shouldRunScheduledBackup());

        // Record a run today
        $this->service->recordBackupResult('success', 'Backup done');
        // Monthly already ran this month -> should NOT run
        $this->assertFalse($this->service->shouldRunScheduledBackup());
    }

    public function test_restore_from_sql_creates_safety_backup_and_restores_data(): void
    {
        // 1. Initial client
        Client::create([
            'client_code' => 'CLI-BEFORE',
            'full_name' => 'Original Patient',
            'gender' => 'female',
        ]);

        $backupSql = $this->service->exportSql();

        // 2. Change client in database
        Client::query()->forceDelete();
        Client::create([
            'client_code' => 'CLI-CORRUPT',
            'full_name' => 'Accidentally Changed Patient',
            'gender' => 'male',
        ]);

        $this->assertDatabaseMissing('clients', ['full_name' => 'Original Patient']);
        $this->assertDatabaseHas('clients', ['full_name' => 'Accidentally Changed Patient']);

        // 3. Perform restore
        $restored = $this->service->restoreFromSql($backupSql);
        $this->assertTrue($restored);

        // 4. Verify original data is back
        $this->assertDatabaseHas('clients', ['full_name' => 'Original Patient']);
        $this->assertDatabaseMissing('clients', ['full_name' => 'Accidentally Changed Patient']);

        // 5. Verify safety snapshot was created
        $backups = $this->service->listBackups();
        $safetySnapshots = array_filter($backups, fn ($b) => $b['is_safety']);
        $this->assertNotEmpty($safetySnapshots);
    }
}
