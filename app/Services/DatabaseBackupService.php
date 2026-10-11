<?php

namespace App\Services;

use App\Models\SystemReference;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupService
{
    protected string $disk = 'local';

    protected string $backupDir = 'backups';

    public function __construct()
    {
        if (! Storage::disk($this->disk)->exists($this->backupDir)) {
            Storage::disk($this->disk)->makeDirectory($this->backupDir);
        }
    }

    /**
     * Generate pure SQL dump string for current active database connection.
     */
    public function exportSql(): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return $this->exportSqlite();
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            return $this->exportMysql();
        }

        // Generic fallback using SQLite/ANSI standard
        return $this->exportSqlite();
    }

    /**
     * Generate SQL dump and store as a file in private storage.
     *
     * @return array{filename: string, path: string, size: int, created_at: string}
     */
    public function createBackupFile(string $prefix = 'backup'): array
    {
        $driver = DB::connection()->getDriverName();
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "{$prefix}_{$driver}_{$timestamp}.sql";
        $relativePath = "{$this->backupDir}/{$filename}";

        $sql = $this->exportSql();
        Storage::disk($this->disk)->put($relativePath, $sql);

        $size = Storage::disk($this->disk)->size($relativePath);

        // Update last run time in settings
        if ($prefix !== 'pre_restore_safety') {
            $this->recordBackupResult('success', 'Backup berhasil dibuat: '.$filename);
        }

        return [
            'filename' => $filename,
            'path' => $relativePath,
            'size' => $size,
            'created_at' => Carbon::now()->toIso8601String(),
        ];
    }

    /**
     * Restore database from a raw SQL string.
     * Automatically takes a pre-restore safety snapshot beforehand.
     */
    public function restoreFromSql(string $sqlContent): bool
    {
        // 1. Safety snapshot of current database
        $this->createBackupFile('pre_restore_safety');

        // 2. Perform restore inside transactional or unprepared execution
        $driver = DB::connection()->getDriverName();

        try {
            $inTransaction = false;
            try {
                $inTransaction = DB::connection()->getPdo()->inTransaction();
            } catch (\Throwable $e) {
                // Ignore if driver does not support inTransaction
            }

            if ($inTransaction) {
                $sqlContent = preg_replace('/^\s*(BEGIN TRANSACTION|START TRANSACTION|COMMIT|SET AUTOCOMMIT\s*=\s*[01])\s*;\s*$/mi', '', $sqlContent);
            }

            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
                DB::unprepared($sqlContent);
                DB::statement('PRAGMA foreign_keys = ON;');
            } elseif ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                DB::unprepared($sqlContent);
                DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            } else {
                DB::unprepared($sqlContent);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Database restore failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Restore database from an existing file in backups storage.
     */
    public function restoreFromFile(string $filename): bool
    {
        $relativePath = "{$this->backupDir}/{$filename}";

        if (! Storage::disk($this->disk)->exists($relativePath)) {
            throw new \InvalidArgumentException("File backup '{$filename}' tidak ditemukan di storage.");
        }

        $sqlContent = Storage::disk($this->disk)->get($relativePath);

        return $this->restoreFromSql($sqlContent);
    }

    /**
     * List all backup files in storage sorted from newest to oldest.
     *
     * @return array<int, array{filename: string, path: string, size: int, size_formatted: string, created_at: Carbon, is_safety: bool, driver: string}>
     */
    public function listBackups(): array
    {
        $files = Storage::disk($this->disk)->files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            if (! str_ends_with($filename, '.sql')) {
                continue;
            }

            $size = Storage::disk($this->disk)->size($file);
            $lastModified = Storage::disk($this->disk)->lastModified($file);
            $createdAt = Carbon::createFromTimestamp($lastModified);

            $isSafety = str_starts_with($filename, 'pre_restore_safety');

            // Detect driver from filename (e.g. backup_sqlite_2026-10-10...)
            $parts = explode('_', $filename);
            $driver = $parts[1] ?? 'sql';
            if ($isSafety && isset($parts[3])) {
                $driver = $parts[3];
            }

            $backups[] = [
                'filename' => $filename,
                'path' => $file,
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'created_at' => $createdAt,
                'is_safety' => $isSafety,
                'driver' => strtoupper($driver),
            ];
        }

        // Sort descending by created_at
        usort($backups, fn ($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return $backups;
    }

    /**
     * Delete a single backup file.
     */
    public function deleteBackup(string $filename): bool
    {
        $relativePath = "{$this->backupDir}/{$filename}";
        if (Storage::disk($this->disk)->exists($relativePath)) {
            return Storage::disk($this->disk)->delete($relativePath);
        }

        return false;
    }

    /**
     * Clean old backups respecting retention count.
     * Excludes pre_restore_safety snapshots from regular quota count,
     * but removes safety snapshots older than 30 days.
     */
    public function cleanOldBackups(int $maxFiles = 12): int
    {
        $all = $this->listBackups();
        $deletedCount = 0;

        // Clean safety snapshots older than 14 days
        $cutoff = Carbon::now()->subDays(14);
        foreach ($all as $item) {
            if ($item['is_safety'] && $item['created_at']->lt($cutoff)) {
                $this->deleteBackup($item['filename']);
                $deletedCount++;
            }
        }

        // Filter regular backups for retention quota
        $regularBackups = array_values(array_filter($all, fn ($b) => ! $b['is_safety']));

        if (count($regularBackups) > $maxFiles) {
            $toRemove = array_slice($regularBackups, $maxFiles);
            foreach ($toRemove as $item) {
                if ($this->deleteBackup($item['filename'])) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Stream download response for a backup file.
     */
    public function downloadFile(string $filename): StreamedResponse
    {
        $relativePath = "{$this->backupDir}/{$filename}";
        if (! Storage::disk($this->disk)->exists($relativePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return Storage::disk($this->disk)->download($relativePath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Stream download response of direct fresh SQL without writing permanently.
     */
    public function downloadDirectSql(): StreamedResponse
    {
        $driver = DB::connection()->getDriverName();
        $filename = "backup_web_psikolog_{$driver}_".Carbon::now()->format('Y-m-d_His').'.sql';
        $sql = $this->exportSql();

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Get automated backup configuration.
     *
     * @return array{frequency: string, retention: int, last_run_at: ?string, last_run_status: string, last_run_message: string}
     */
    public function getScheduleConfig(): array
    {
        $default = [
            'frequency' => 'monthly', // 'monthly', 'weekly', 'daily', 'disabled'
            'retention' => 12,        // number of backup files to keep
            'last_run_at' => null,
            'last_run_status' => 'never_run',
            'last_run_message' => 'Belum pernah dijalankan otomatis.',
        ];

        $records = SystemReference::where('group_key', 'backup_configuration')->get();
        if ($records->isEmpty()) {
            return $default;
        }

        $config = $default;
        foreach ($records as $record) {
            if ($record->value === 'frequency') {
                $config['frequency'] = $record->label;
            } elseif ($record->value === 'retention') {
                $config['retention'] = max(1, (int) $record->label);
            } elseif ($record->value === 'last_run_at') {
                $config['last_run_at'] = $record->label;
            } elseif ($record->value === 'last_run_status') {
                $config['last_run_status'] = $record->label;
            } elseif ($record->value === 'last_run_message') {
                $config['last_run_message'] = $record->label;
            }
        }

        return $config;
    }

    /**
     * Save automated backup configuration.
     */
    public function saveScheduleConfig(string $frequency, int $retention): void
    {
        $this->updateOrCreateConfig('frequency', $frequency);
        $this->updateOrCreateConfig('retention', (string) max(1, $retention));
    }

    /**
     * Record last backup execution result.
     */
    public function recordBackupResult(string $status, string $message): void
    {
        $this->updateOrCreateConfig('last_run_at', Carbon::now()->toIso8601String());
        $this->updateOrCreateConfig('last_run_status', $status);
        $this->updateOrCreateConfig('last_run_message', $message);
    }

    /**
     * Determine if scheduled backup should run right now.
     */
    public function shouldRunScheduledBackup(): bool
    {
        $config = $this->getScheduleConfig();
        $frequency = $config['frequency'];

        if ($frequency === 'disabled') {
            return false;
        }

        $lastRunAt = $config['last_run_at'];
        if (! $lastRunAt) {
            return true;
        }

        try {
            $lastDate = Carbon::parse($lastRunAt);
        } catch (\Throwable $e) {
            return true;
        }

        $now = Carbon::now();

        if ($frequency === 'daily') {
            return ! $lastDate->isToday();
        }

        if ($frequency === 'weekly') {
            return $lastDate->diffInDays($now) >= 7;
        }

        if ($frequency === 'monthly') {
            return $lastDate->format('Y-m') !== $now->format('Y-m');
        }

        return false;
    }

    /**
     * Helper to update or create SystemReference config item.
     */
    protected function updateOrCreateConfig(string $key, string $value): void
    {
        $ref = SystemReference::where('group_key', 'backup_configuration')
            ->where('value', $key)
            ->first();

        if ($ref) {
            $ref->update(['label' => $value]);
        } else {
            SystemReference::create([
                'group_key' => 'backup_configuration',
                'value' => $key,
                'label' => $value,
                'sort_order' => 0,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Export SQLite Database to SQL.
     */
    protected function exportSqlite(): string
    {
        $pdo = DB::connection()->getPdo();
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $dbName = config('database.connections.sqlite.database');

        $out = [];
        $out[] = '-- ============================================================';
        $out[] = '-- Clinical Sanctuary Database Backup';
        $out[] = '-- Driver: SQLite';
        $out[] = '-- Database: '.basename($dbName);
        $out[] = "-- Generated: {$now}";
        $out[] = "-- ============================================================\n";
        $out[] = 'PRAGMA foreign_keys = OFF;';
        $out[] = "BEGIN TRANSACTION;\n";

        // 1. Fetch all tables
        $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name ASC;");

        foreach ($tables as $table) {
            $tableName = $table->name;
            $tableSql = $table->sql;

            if (empty($tableSql)) {
                continue;
            }

            $out[] = '-- ------------------------------------------------------------';
            $out[] = "-- Structure for table \"{$tableName}\"";
            $out[] = '-- ------------------------------------------------------------';
            $out[] = "DROP TABLE IF EXISTS \"{$tableName}\";";
            $out[] = "{$tableSql};\n";

            // Fetch table data in chunks
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                $out[] = "-- Data for table \"{$tableName}\"";
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function ($val) use ($pdo) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        if (is_int($val) || (is_numeric($val) && ! str_starts_with((string) $val, '0'))) {
                            return (string) $val;
                        }

                        return $pdo->quote((string) $val);
                    }, array_values($rowArray));

                    $columns = array_map(fn ($col) => "\"{$col}\"", array_keys($rowArray));
                    $out[] = "INSERT INTO \"{$tableName}\" (".implode(', ', $columns).') VALUES ('.implode(', ', $escapedValues).');';
                }
                $out[] = '';
            }
        }

        // 2. Fetch standalone indexes
        $indexes = DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND sql IS NOT NULL AND name NOT LIKE 'sqlite_%' ORDER BY name ASC;");
        if (! empty($indexes)) {
            $out[] = '-- ------------------------------------------------------------';
            $out[] = '-- Standalone Indexes';
            $out[] = '-- ------------------------------------------------------------';
            foreach ($indexes as $index) {
                $out[] = "{$index->sql};";
            }
            $out[] = '';
        }

        $out[] = 'COMMIT;';
        $out[] = "PRAGMA foreign_keys = ON;\n";

        return implode("\n", $out);
    }

    /**
     * Export MySQL / MariaDB Database to SQL.
     */
    protected function exportMysql(): string
    {
        $pdo = DB::connection()->getPdo();
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $dbName = config('database.connections.mysql.database', 'laravel');

        $out = [];
        $out[] = '-- ============================================================';
        $out[] = '-- Clinical Sanctuary Database Backup';
        $out[] = '-- Driver: MySQL / MariaDB';
        $out[] = "-- Database: {$dbName}";
        $out[] = "-- Generated: {$now}";
        $out[] = "-- ============================================================\n";
        $out[] = 'SET FOREIGN_KEY_CHECKS = 0;';
        $out[] = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';";
        $out[] = 'SET AUTOCOMMIT = 0;';
        $out[] = "START TRANSACTION;\n";

        $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        $keyName = 'Tables_in_'.$dbName;

        foreach ($tables as $t) {
            $tArray = (array) $t;
            $tableName = reset($tArray);

            $createResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createArray = (array) ($createResult[0] ?? []);
            $createSql = $createArray['Create Table'] ?? '';

            if (empty($createSql)) {
                continue;
            }

            $out[] = '-- ------------------------------------------------------------';
            $out[] = "-- Structure for table `{$tableName}`";
            $out[] = '-- ------------------------------------------------------------';
            $out[] = "DROP TABLE IF EXISTS `{$tableName}`;";
            $out[] = "{$createSql};\n";

            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                $out[] = "-- Data for table `{$tableName}`";
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function ($val) use ($pdo) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        if (is_int($val) || (is_numeric($val) && ! str_starts_with((string) $val, '0'))) {
                            return (string) $val;
                        }

                        return $pdo->quote((string) $val);
                    }, array_values($rowArray));

                    $columns = array_map(fn ($col) => "`{$col}`", array_keys($rowArray));
                    $out[] = "INSERT INTO `{$tableName}` (".implode(', ', $columns).') VALUES ('.implode(', ', $escapedValues).');';
                }
                $out[] = '';
            }
        }

        $out[] = 'COMMIT;';
        $out[] = "SET FOREIGN_KEY_CHECKS = 1;\n";

        return implode("\n", $out);
    }

    /**
     * Helper to format bytes into readable KB / MB.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
