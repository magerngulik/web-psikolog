<?php

namespace App\Livewire\Settings;

use App\Models\SecurityKey;
use App\Services\DatabaseBackupService;
use App\Services\SystemDiagnosticService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackupManager extends Component
{
    use WithFileUploads;

    // Schedule Settings
    public string $frequency = 'monthly';

    public int $retention = 12;

    // File Upload for Restore
    public $sqlFile;

    // Modal Restore State
    public bool $showRestoreModal = false;

    public string $restoreSource = 'uploaded'; // 'uploaded' | 'existing'

    public ?string $selectedExistingFile = null;

    public string $restorePin = '';

    public bool $confirmRestore = false;

    // Log Preview Modal State
    public bool $showLogPreviewModal = false;

    public string $logFilterLevel = 'all'; // 'all' | 'error' | 'warning' | 'info'

    public function mount(DatabaseBackupService $service): void
    {
        $config = $service->getScheduleConfig();
        $this->frequency = $config['frequency'] ?? 'monthly';
        $this->retention = (int) ($config['retention'] ?? 12);
    }

    /**
     * Trigger direct browser download of fresh SQL without saving permanently.
     */
    public function downloadDirectSql(DatabaseBackupService $service)
    {
        return $service->downloadDirectSql();
    }

    /**
     * Create fresh backup and save it in server storage.
     */
    public function createServerBackup(DatabaseBackupService $service): void
    {
        try {
            $result = $service->createBackupFile('backup');
            $service->cleanOldBackups($this->retention);
            session()->flash('success_message', "Backup baru berhasil dibuat: {$result['filename']} (".round($result['size'] / 1024, 2).' KB)');
        } catch (\Throwable $e) {
            session()->flash('error_message', 'Gagal membuat backup: '.$e->getMessage());
        }
    }

    /**
     * Download an existing backup file from server.
     */
    public function downloadBackupFile(string $filename, DatabaseBackupService $service)
    {
        return $service->downloadFile($filename);
    }

    /**
     * Delete an existing backup file from server.
     */
    public function deleteBackupFile(string $filename, DatabaseBackupService $service): void
    {
        if ($service->deleteBackup($filename)) {
            session()->flash('success_message', "File backup '{$filename}' berhasil dihapus.");
        } else {
            session()->flash('error_message', "File '{$filename}' gagal dihapus atau tidak ditemukan.");
        }
    }

    /**
     * Save automated backup schedule configuration.
     */
    public function saveSchedule(DatabaseBackupService $service): void
    {
        $this->validate([
            'frequency' => 'required|in:monthly,weekly,daily,disabled',
            'retention' => 'required|integer|min:1|max:100',
        ]);

        $service->saveScheduleConfig($this->frequency, $this->retention);
        session()->flash('success_message', 'Pengaturan jadwal backup otomatis berhasil diperbarui!');
    }

    /**
     * Open confirmation modal to restore from an existing server file.
     */
    public function openRestoreExisting(string $filename): void
    {
        $this->restoreSource = 'existing';
        $this->selectedExistingFile = $filename;
        $this->restorePin = '';
        $this->confirmRestore = false;
        $this->resetErrorBag();
        $this->showRestoreModal = true;
    }

    /**
     * Open confirmation modal to restore from uploaded file.
     */
    public function openRestoreUploaded(): void
    {
        $this->validate([
            'sqlFile' => 'required|file|max:51200', // 50MB max
        ], [
            'sqlFile.required' => 'Pilih file backup .SQL terlebih dahulu.',
            'sqlFile.max' => 'Ukuran file SQL maksimal 50 MB.',
        ]);

        $clientOriginalName = $this->sqlFile->getClientOriginalName();
        if (! str_ends_with(strtolower($clientOriginalName), '.sql')) {
            $this->addError('sqlFile', 'Format file harus berformat .sql');

            return;
        }

        $this->restoreSource = 'uploaded';
        $this->selectedExistingFile = null;
        $this->restorePin = '';
        $this->confirmRestore = false;
        $this->resetErrorBag();
        $this->showRestoreModal = true;
    }

    /**
     * Close restore modal and reset state.
     */
    public function closeRestoreModal(): void
    {
        $this->showRestoreModal = false;
        $this->restorePin = '';
        $this->confirmRestore = false;
        $this->selectedExistingFile = null;
        $this->resetErrorBag();
    }

    /**
     * Execute destructive database restore after verifying PIN and safety checkbox.
     */
    public function executeRestore(DatabaseBackupService $service): void
    {
        $this->validate([
            'restorePin' => 'required|digits:6',
            'confirmRestore' => 'accepted',
        ], [
            'restorePin.required' => 'PIN Keamanan wajib dimasukkan.',
            'restorePin.digits' => 'PIN Keamanan harus 6 digit angka.',
            'confirmRestore.accepted' => 'Anda harus mencentang persetujuan pemulihan database.',
        ]);

        // Verify PIN against active security key
        $activeKey = SecurityKey::where('is_active', true)->first();
        if (! $activeKey || ! Hash::check($this->restorePin, $activeKey->pin_hash)) {
            $this->addError('restorePin', 'PIN Keamanan salah! Pemulihan database dibatalkan.');

            return;
        }

        try {
            if ($this->restoreSource === 'uploaded') {
                if (! $this->sqlFile) {
                    $this->addError('sqlFile', 'File SQL upload tidak ditemukan.');

                    return;
                }

                $content = file_get_contents($this->sqlFile->getRealPath());
                $service->restoreFromSql($content);
                $this->reset('sqlFile');
            } else {
                if (! $this->selectedExistingFile) {
                    $this->addError('restorePin', 'File backup tidak dipilih.');

                    return;
                }

                $service->restoreFromFile($this->selectedExistingFile);
            }

            $this->showRestoreModal = false;
            $this->restorePin = '';
            $this->confirmRestore = false;

            session()->flash('success_message', '✓ Database berhasil dipulihkan! Sistem telah otomatis menyimpan snapshot cadangan darurat (safety backup) sesaat sebelum proses berlangsung.');
        } catch (\Throwable $e) {
            $this->addError('restorePin', 'Gagal memulihkan database: '.$e->getMessage());
        }
    }

    /**
     * Download structured diagnostic support bundle.
     */
    public function downloadDiagnostic(SystemDiagnosticService $diagnosticService)
    {
        return $diagnosticService->downloadDiagnosticReport();
    }

    /**
     * Open log viewer modal.
     */
    public function openLogPreview(): void
    {
        $this->showLogPreviewModal = true;
    }

    /**
     * Close log viewer modal.
     */
    public function closeLogPreview(): void
    {
        $this->showLogPreviewModal = false;
    }

    /**
     * Set level filter for log viewer.
     */
    public function setLogFilter(string $level): void
    {
        $this->logFilterLevel = in_array($level, ['all', 'error', 'warning', 'info']) ? $level : 'all';
    }

    /**
     * Clear application log file.
     */
    public function clearLogFile(SystemDiagnosticService $diagnosticService): void
    {
        if ($diagnosticService->clearLog()) {
            session()->flash('success_message', 'Berkas log error dan diagnostik berhasil dibersihkan.');
        } else {
            session()->flash('error_message', 'Gagal membersihkan berkas log.');
        }
    }

    public function render(DatabaseBackupService $service, SystemDiagnosticService $diagnosticService)
    {
        $backups = $service->listBackups();
        $config = $service->getScheduleConfig();
        $diagnosticSummary = $diagnosticService->getDiagnosticSummary();
        $recentLogs = $this->showLogPreviewModal ? $diagnosticService->getRecentLogEntries(100, $this->logFilterLevel) : [];

        $totalBytes = array_sum(array_column($backups, 'size'));
        $totalSizeFormatted = $this->formatBytes($totalBytes);

        return view('livewire.settings.backup-manager', [
            'backups' => $backups,
            'config' => $config,
            'totalSizeFormatted' => $totalSizeFormatted,
            'diagnosticSummary' => $diagnosticSummary,
            'recentLogs' => $recentLogs,
        ])->layout('components.layouts.app');
    }

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
