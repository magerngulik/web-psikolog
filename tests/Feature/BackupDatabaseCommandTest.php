<?php

namespace Tests\Feature;

use App\Models\SecurityKey;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupDatabaseCommandTest extends TestCase
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
    }

    public function test_command_runs_with_force_option(): void
    {
        $this->artisan('app:backup-database', ['--force' => true])
            ->expectsOutputToContain('Backup database berhasil dibuat')
            ->assertSuccessful();

        $service = new DatabaseBackupService;
        $this->assertNotEmpty($service->listBackups());
    }

    public function test_command_skips_when_schedule_not_due(): void
    {
        $service = new DatabaseBackupService;
        $service->saveScheduleConfig('monthly', 12);
        $service->recordBackupResult('success', 'Previously completed today');

        $this->artisan('app:backup-database')
            ->expectsOutputToContain('Jadwal belum terpenuhi')
            ->assertSuccessful();
    }

    public function test_command_cleans_old_backups_exceeding_retention(): void
    {
        $service = new DatabaseBackupService;
        $service->saveScheduleConfig('monthly', 2);

        // Pre-create 3 backups
        for ($i = 0; $i < 3; $i++) {
            Storage::disk('local')->put("backups/backup_sqlite_2026-10-0{$i}_000000.sql", "-- test {$i}");
            touch(Storage::disk('local')->path("backups/backup_sqlite_2026-10-0{$i}_000000.sql"), time() - (4 - $i) * 3600);
        }

        $this->assertCount(3, $service->listBackups());

        $this->artisan('app:backup-database', ['--force' => true])
            ->expectsOutputToContain('berhasil dibersihkan')
            ->assertSuccessful();

        $remaining = $service->listBackups();
        // Since retention is 2 and force creates 1 new one, the total regular backups should not exceed 2
        $regularBackups = array_filter($remaining, fn ($b) => ! $b['is_safety']);
        $this->assertLessThanOrEqual(2, count($regularBackups));
    }
}
