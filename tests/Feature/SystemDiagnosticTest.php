<?php

namespace Tests\Feature;

use App\Livewire\Settings\BackupManager;
use App\Models\SecurityKey;
use App\Services\SystemDiagnosticService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SystemDiagnosticTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);
    }

    public function test_can_generate_diagnostic_summary(): void
    {
        $service = app(SystemDiagnosticService::class);
        $summary = $service->getDiagnosticSummary();

        $this->assertArrayHasKey('log_size_formatted', $summary);
        $this->assertArrayHasKey('php_version', $summary);
        $this->assertArrayHasKey('os_platform', $summary);
        $this->assertArrayHasKey('database_driver', $summary);
        $this->assertArrayHasKey('system_status', $summary);
        $this->assertArrayHasKey('storage_writable', $summary);
    }

    public function test_diagnostic_report_sanitizes_sensitive_pins_and_passwords(): void
    {
        $service = app(SystemDiagnosticService::class);

        $sensitiveInput = "Error occurred with pin: 123456, password: secretPassword99, and hash: $2y$12\$e8z9a4V1o2p3q4r5s6t7u8v9w0x1y2z3a4b5c6d7e8f9g0h1i2j3k, token: Bearer abcdef123456";
        $sanitized = $service->sanitizeText($sensitiveInput);

        $this->assertStringNotContainsString('123456', $sanitized);
        $this->assertStringNotContainsString('secretPassword99', $sanitized);
        $this->assertStringNotContainsString('$2y$12$', $sanitized);
        $this->assertStringNotContainsString('abcdef123456', $sanitized);

        $this->assertStringContainsString('[REDACTED', $sanitized);
    }

    public function test_can_generate_diagnostic_report_text(): void
    {
        $service = app(SystemDiagnosticService::class);
        $report = $service->generateDiagnosticReport();

        $this->assertStringContainsString('SIM KLINIK PSIKOLOGI - SISTEM DIAGNOSTIK & ERROR LOG BUNDLE', $report);
        $this->assertStringContainsString('[1. INFORMASI HOST & LINGKUNGAN SISTEM]', $report);
        $this->assertStringContainsString('[2. RINGKASAN DATA APLIKASI', $report);
        $this->assertStringContainsString('[3. LOG ERROR & RIWAYAT AKTIVITAS TERAKHIR', $report);
        $this->assertStringContainsString('AKHIR LAPORAN DIAGNOSTIK', $report);
    }

    public function test_can_download_diagnostic_file_via_livewire(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $component = Livewire::test(BackupManager::class)
            ->call('downloadDiagnostic');

        $response = $component->instance()->downloadDiagnostic(app(SystemDiagnosticService::class));

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
        $this->assertStringContainsString('psikolog_diagnostik_', $response->headers->get('content-disposition'));
        $this->assertEquals('text/plain; charset=UTF-8', $response->headers->get('content-type'));
    }

    public function test_can_preview_and_filter_logs_in_livewire(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        // Simulasikan penulisan log
        $logPath = storage_path('logs/laravel.log');
        $sampleLog = "[2026-10-11 16:00:00] local.ERROR: Sample error test for livewire diagnostic preview\n";
        File::append($logPath, $sampleLog);

        Livewire::test(BackupManager::class)
            ->assertSet('showLogPreviewModal', false)
            ->call('openLogPreview')
            ->assertSet('showLogPreviewModal', true)
            ->assertSee('In-App Log Viewer')
            ->assertSee('Sample error test for livewire diagnostic preview')
            ->call('setLogFilter', 'error')
            ->assertSet('logFilterLevel', 'error')
            ->call('closeLogPreview')
            ->assertSet('showLogPreviewModal', false);
    }

    public function test_can_clear_log_file(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $logPath = storage_path('logs/laravel.log');
        File::append($logPath, "Some random error to clear\n");

        Livewire::test(BackupManager::class)
            ->call('clearLogFile')
            ->assertHasNoErrors()
            ->assertSee('Berkas log error dan diagnostik berhasil dibersihkan.');

        $this->assertFileExists($logPath);
        $content = File::get($logPath);
        $this->assertStringContainsString('dibersihkan oleh pengguna', $content);
        $this->assertStringNotContainsString('Some random error to clear', $content);
    }
}

