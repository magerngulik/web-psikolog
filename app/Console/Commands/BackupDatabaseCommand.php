<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database {--force : Jalankan proses backup tanpa mengecek interval jadwal}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan proses backup database otomatis dan membersihkan arsip lama berdasarkan konfigurasi retensi';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $isForce = $this->option('force');
        $config = $backupService->getScheduleConfig();

        $this->info("Menjalankan pemeriksaan backup database [Frekuensi: {$config['frequency']}, Retensi: {$config['retention']} file]...");

        if (! $isForce && ! $backupService->shouldRunScheduledBackup()) {
            $this->comment('Jadwal belum terpenuhi (Terakhir berjalan: '.($config['last_run_at'] ?? 'Belum pernah').'). Melewati proses backup.');

            return Command::SUCCESS;
        }

        try {
            $this->info('Membuat snapshot database SQL...');
            $result = $backupService->createBackupFile('backup');

            $this->info("✓ Backup database berhasil dibuat: {$result['filename']} (".round($result['size'] / 1024, 2).' KB)');

            // Auto cleanup old backups
            $cleaned = $backupService->cleanOldBackups($config['retention']);
            if ($cleaned > 0) {
                $this->comment("✓ {$cleaned} file backup lama berhasil dibersihkan sesuai kuota retensi ({$config['retention']} file).");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal menjalankan backup: '.$e->getMessage());
            $backupService->recordBackupResult('failed', 'Gagal: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
