<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\Organization;
use App\Models\Session as ClientSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemDiagnosticService
{
    /**
     * Get path to the main application log file.
     */
    public function getLogPath(): string
    {
        return storage_path('logs/laravel.log');
    }

    /**
     * Gather health metrics and environment diagnostic summary.
     *
     * @return array<string, mixed>
     */
    public function getDiagnosticSummary(): array
    {
        $logPath = $this->getLogPath();
        $logExists = File::exists($logPath);
        $logSize = $logExists ? File::size($logPath) : 0;
        $logWritable = File::isWritable(dirname($logPath));

        $lastLogEntry = null;
        $errorCount24h = 0;

        if ($logExists && $logSize > 0) {
            $recent = $this->getRecentLogEntries(50);
            if (! empty($recent)) {
                $lastLogEntry = $recent[0]['datetime'] ?? null;
            }

            // Estimate error count in last 24h
            $cutoff = Carbon::now()->subDay();
            foreach ($recent as $entry) {
                if (in_array(strtoupper($entry['level']), ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) {
                    try {
                        $entryDate = Carbon::parse($entry['datetime']);
                        if ($entryDate->gte($cutoff)) {
                            $errorCount24h++;
                        }
                    } catch (\Throwable $e) {
                        // Ignore date parse issues
                    }
                }
            }
        }

        // Database file info
        $dbDriver = DB::connection()->getDriverName();
        $dbSizeFormatted = '-';
        if ($dbDriver === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if (File::exists($dbPath)) {
                $dbSizeFormatted = $this->formatBytes(File::size($dbPath));
            }
        }

        // Storage status
        $storageWritable = File::isWritable(storage_path()) && File::isWritable(storage_path('app'));

        // Overall status evaluation
        $status = 'healthy';
        if ($errorCount24h > 10) {
            $status = 'error';
        } elseif ($errorCount24h > 0 || ! $storageWritable) {
            $status = 'warning';
        }

        return [
            'log_size' => $logSize,
            'log_size_formatted' => $this->formatBytes($logSize),
            'log_exists' => $logExists,
            'log_writable' => $logWritable,
            'last_log_entry' => $lastLogEntry,
            'error_count_last_24h' => $errorCount24h,
            'system_status' => $status,
            'php_version' => PHP_VERSION,
            'os_platform' => PHP_OS_FAMILY . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
            'database_driver' => $dbDriver,
            'database_size_formatted' => $dbSizeFormatted,
            'storage_writable' => $storageWritable,
            'memory_limit' => ini_get('memory_limit') ?: 'N/A',
        ];
    }

    /**
     * Parse recent log entries from laravel.log with sanitized context.
     *
     * @return array<int, array{datetime: string, env: string, level: string, message: string, trace: string}>
     */
    public function getRecentLogEntries(int $limit = 100, ?string $levelFilter = null): array
    {
        $logPath = $this->getLogPath();
        if (! File::exists($logPath)) {
            return [];
        }

        $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false || empty($lines)) {
            return [];
        }

        // Process from bottom to top (most recent first)
        $entries = [];
        $currentEntry = null;

        for ($i = count($lines) - 1; $i >= 0; $i--) {
            $line = $lines[$i];

            // Match Monolog header: [2026-10-11 16:50:00] local.ERROR: Message
            if (preg_match('/^\[(?<datetime>[^\]]+)\]\s+(?<env>\w+)\.(?<level>[A-Z]+):\s+(?<message>.*)$/', $line, $matches)) {
                $level = strtoupper($matches['level']);

                if ($currentEntry !== null) {
                    $entries[] = $currentEntry;
                    if (count($entries) >= $limit) {
                        break;
                    }
                }

                $shouldInclude = true;
                if ($levelFilter && $levelFilter !== 'all') {
                    $filter = strtoupper($levelFilter);
                    if ($filter === 'ERROR' && ! in_array($level, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])) {
                        $shouldInclude = false;
                    } elseif ($filter === 'WARNING' && $level !== 'WARNING') {
                        $shouldInclude = false;
                    } elseif ($filter === 'INFO' && ! in_array($level, ['INFO', 'NOTICE', 'DEBUG'])) {
                        $shouldInclude = false;
                    }
                }

                if ($shouldInclude) {
                    $currentEntry = [
                        'datetime' => $matches['datetime'],
                        'env' => $matches['env'],
                        'level' => $level,
                        'message' => $this->sanitizeText(trim($matches['message'])),
                        'trace' => '',
                    ];
                } else {
                    $currentEntry = null;
                }
            } else {
                // Continuation line / stack trace of the current entry
                if ($currentEntry !== null) {
                    $currentEntry['trace'] = $this->sanitizeText($line) . "\n" . $currentEntry['trace'];
                }
            }
        }

        if ($currentEntry !== null && count($entries) < $limit) {
            $entries[] = $currentEntry;
        }

        return $entries;
    }

    /**
     * Generate full, sanitized diagnostic support report text.
     */
    public function generateDiagnosticReport(): string
    {
        $now = Carbon::now();
        $summary = $this->getDiagnosticSummary();

        // Safe aggregate data statistics (no sensitive personal patient notes)
        $clientCount = 0;
        $caseCount = 0;
        $sessionCount = 0;
        $activityCount = 0;
        $orgCount = 0;

        try {
            $clientCount = Client::count();
            $caseCount = MedicalCase::count();
            $sessionCount = ClientSession::count();
            $activityCount = Activity::count();
            $orgCount = Organization::count();
        } catch (\Throwable $e) {
            // Ignore database fetch errors in report
        }

        $backupService = app(DatabaseBackupService::class);
        $backups = $backupService->listBackups();
        $backupCount = count($backups);
        $lastBackupName = ! empty($backups) ? $backups[0]['filename'] . ' (' . $backups[0]['created_at']->format('Y-m-d H:i:s') . ')' : 'Belum pernah dibuat';

        $report = "======================================================================\n";
        $report .= "SIM KLINIK PSIKOLOGI - SISTEM DIAGNOSTIK & ERROR LOG BUNDLE\n";
        $report .= "Waktu Ekspor  : " . $now->translatedFormat('l, d F Y H:i:s') . " (" . $now->timezoneName . ")\n";
        $report .= "Mode Operasi  : 100% Offline Standalone Desktop/Web\n";
        $report .= "Status Sistem : " . strtoupper($summary['system_status']) . "\n";
        $report .= "======================================================================\n\n";

        $report .= "[1. INFORMASI HOST & LINGKUNGAN SISTEM]\n";
        $report .= "----------------------------------------------------------------------\n";
        $report .= "Versi PHP        : " . $summary['php_version'] . "\n";
        $report .= "Sistem Operasi   : " . $summary['os_platform'] . "\n";
        $report .= "PHP Interface    : " . php_sapi_name() . "\n";
        $report .= "Memory Limit     : " . $summary['memory_limit'] . "\n";
        $report .= "Max Exec Time    : " . ini_get('max_execution_time') . "s\n";
        $report .= "Database Driver  : " . $summary['database_driver'] . " (Ukuran DB: " . $summary['database_size_formatted'] . ")\n";
        $report .= "Storage Writable : " . ($summary['storage_writable'] ? 'YA (OK)' : 'TIDAK (Error Izin Direktori)') . "\n";
        $report .= "Ukuran Log File  : " . $summary['log_size_formatted'] . "\n";
        $report .= "Ekstensi Kunci   : " . implode(', ', [
            'pdo_sqlite' => extension_loaded('pdo_sqlite') ? 'pdo_sqlite:OK' : 'pdo_sqlite:TIDAK ADA',
            'mbstring' => extension_loaded('mbstring') ? 'mbstring:OK' : 'mbstring:TIDAK ADA',
            'openssl' => extension_loaded('openssl') ? 'openssl:OK' : 'openssl:TIDAK ADA',
            'bcmath' => extension_loaded('bcmath') ? 'bcmath:OK' : 'bcmath:TIDAK ADA',
            'fileinfo' => extension_loaded('fileinfo') ? 'fileinfo:OK' : 'fileinfo:TIDAK ADA',
        ]) . "\n\n";

        $report .= "[2. RINGKASAN DATA APLIKASI (METADATA AGREGAT NON-SENSITIF)]\n";
        $report .= "----------------------------------------------------------------------\n";
        $report .= "Total Klien Terdaftar : " . $clientCount . " data\n";
        $report .= "Total Kasus Medis     : " . $caseCount . " data\n";
        $report .= "Total Sesi Konseling  : " . $sessionCount . " data\n";
        $report .= "Total Kegiatan Edukasi: " . $activityCount . " data\n";
        $report .= "Total Mitra Organisasi: " . $orgCount . " mitra\n";
        $report .= "Arsip Backup Tersedia : " . $backupCount . " berkas\n";
        $report .= "Backup Terakhir       : " . $lastBackupName . "\n\n";

        $report .= "[3. LOG ERROR & RIWAYAT AKTIVITAS TERAKHIR (SANITIZED)]\n";
        $report .= "----------------------------------------------------------------------\n";

        $logEntries = $this->getRecentLogEntries(250);
        if (empty($logEntries)) {
            $report .= "(Berkas log bersih atau belum ada catatan error yang terekam)\n";
        } else {
            foreach ($logEntries as $entry) {
                $report .= "[{$entry['datetime']}] [{$entry['level']}] {$entry['message']}\n";
                if (! empty($entry['trace'])) {
                    $traceLines = array_slice(explode("\n", trim($entry['trace'])), 0, 15);
                    foreach ($traceLines as $tl) {
                        $report .= "    " . $tl . "\n";
                    }
                }
                $report .= "\n";
            }
        }

        $report .= "======================================================================\n";
        $report .= "AKHIR LAPORAN DIAGNOSTIK - Kirimkan berkas ini kepada pengembang untuk bantuan teknis.\n";
        $report .= "======================================================================\n";

        return $report;
    }

    /**
     * Stream download response for the diagnostic report.
     */
    public function downloadDiagnosticReport(): StreamedResponse
    {
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "psikolog_diagnostik_{$timestamp}.txt";
        $content = $this->generateDiagnosticReport();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Clear and reset the application log file safely.
     */
    public function clearLog(): bool
    {
        $logPath = $this->getLogPath();
        try {
            $timestamp = Carbon::now()->format('Y-m-d H:i:s');
            $initialContent = "[{$timestamp}] local.INFO: Log error dan diagnostik telah dibersihkan oleh pengguna.\n";
            File::put($logPath, $initialContent);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Sanitize sensitive keys (PINs, passwords, hashes) from text and stack traces.
     */
    public function sanitizeText(string $text): string
    {
        // Redact Bearer tokens
        $text = preg_replace('/(Bearer\s+)[A-Za-z0-9\-_\.=]+/i', '$1[REDACTED_TOKEN]', $text);

        // Redact PIN and password hashes (bcrypt)
        $text = preg_replace('/(\$2y\$\d{2}\$[A-Za-z0-9\.\/]{53})/', '[REDACTED_BCRYPT_HASH]', $text);

        // Redact PIN and passwords: pin: 123456, 'pin' => '123456', "pin":"123456", password = ...
        $text = preg_replace('/([\'"]?(?:pin|restorePin|security_pin|password|secret)[\'"]?\s*(?::|=>|=)\s*[\'"]?)[^\'",\s\}]+/i', '$1[REDACTED]', $text);

        // Redact token keys: token: abc..., token: Bearer abc...
        $text = preg_replace('/([\'"]?(?:token|api_key|auth_token)[\'"]?\s*(?::|=>|=)\s*[\'"]?(?:Bearer\s+)?)[^\'",\s\}]+/i', '$1[REDACTED_TOKEN]', $text);

        return $text;
    }

    /**
     * Format byte values to human readable string.
     */
    public function formatBytes(int $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min((int) $pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
