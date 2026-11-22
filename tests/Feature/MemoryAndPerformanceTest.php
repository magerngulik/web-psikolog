<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use App\Models\SecurityKey;
use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemoryAndPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['license.enabled' => false]);
        LicenseService::setFakeMachineId(null);

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);
    }

    public function test_core_pages_stay_within_low_ram_budget(): void
    {
        // Seed sample clinical data to simulate real offline usage
        for ($i = 1; $i <= 25; $i++) {
            $client = Client::create([
                'full_name' => "Klien Uji {$i}",
                'gender' => $i % 2 === 0 ? 'F' : 'M',
                'birth_date' => '1995-05-10',
                'phone' => '08120000' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
            ]);

            $case = MedicalCase::create([
                'client_id' => $client->id,
                'title' => "Kasus Uji {$i}",
                'category' => 'Klinis Dewasa',
                'status' => 'active',
            ]);

            PatientSession::create([
                'case_id' => $case->id,
                'session_number' => 1,
                'session_date' => now()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '10:00',
                'status' => 'done',
                'fee' => 250000,
                'payment_status' => 'paid',
                'is_locked' => false,
            ]);
        }

        $routes = [
            'dashboard',
            'clients.index',
            'cases.index',
            'sessions.index',
            'activities.index',
            'reports.logbook',
        ];

        $memoryBefore = memory_get_usage(false);

        foreach ($routes as $routeName) {
            $response = $this->withSession(['pin_unlocked' => true])
                ->get(route($routeName));

            $response->assertStatus(200);
        }

        $memoryAfter = memory_get_usage(false);
        $peakMemoryMb = memory_get_peak_usage(true) / 1024 / 1024;
        $deltaMemoryMb = max(0, ($memoryAfter - $memoryBefore) / 1024 / 1024);

        // Peak PHP RAM must remain far below low-spec 8GB Windows laptop budget (< 128 MB)
        $this->assertLessThan(
            128,
            $peakMemoryMb,
            sprintf('PHP peak memory terlalu tinggi: %.2f MB (batas < 128 MB)', $peakMemoryMb)
        );

        // Memory growth across all 6 core pages should stay small (< 32 MB)
        $this->assertLessThan(
            32,
            $deltaMemoryMb,
            sprintf('Kenaikan memori lintas halaman terlalu tinggi: %.2f MB (batas < 32 MB)', $deltaMemoryMb)
        );
    }

    public function test_machine_id_only_runs_hardware_detection_once_across_multiple_requests(): void
    {
        Cache::flush();
        LicenseService::setFakeMachineId(null);

        $spyService = new class extends LicenseService
        {
            public int $detectionCalls = 0;

            protected function detectRawHardwareUuid(): string
            {
                $this->detectionCalls++;

                return 'TEST-WINDOWS-UUID-1234-5678-90AB';
            }
        };

        $firstId = $spyService->getMachineId();

        // Simulate 10 separate HTTP requests where static PHP memory resets each time
        $reflection = new \ReflectionProperty(LicenseService::class, 'cachedMachineId');
        for ($i = 0; $i < 10; $i++) {
            $reflection->setValue(null, null);
            $nextId = $spyService->getMachineId();
            $this->assertEquals($firstId, $nextId);
        }

        // Hardware/PowerShell detection must execute ONLY ONCE, never per request
        $this->assertEquals(1, $spyService->detectionCalls);
    }

    public function test_ui_assets_avoid_heavy_gpu_shared_memory_rules(): void
    {
        $css = File::get(resource_path('css/app.css'));
        $this->assertStringNotContainsString('background-attachment: fixed', $css);
        $this->assertStringNotContainsString('backdrop-filter: blur', $css);
        $this->assertStringNotContainsString('will-change: transform', $css);

        $layout = File::get(resource_path('views/components/layouts/app.blade.php'));
        $this->assertStringNotContainsString('backdrop-blur-xl', $layout);
        $this->assertStringNotContainsString('animate-ping', $layout);

        $lockScreen = File::get(resource_path('views/livewire/lock-screen.blade.php'));
        $this->assertStringContainsString('submitPin', $lockScreen);
        $this->assertStringNotContainsString('wire:click="addNumber', $lockScreen);
    }

    public function test_windows_scripts_avoid_powershell_and_optimize_safely(): void
    {
        $startScript = File::get(base_path('start-server.bat'));
        $executableLines = collect(explode("\n", $startScript))
            ->reject(fn (string $line) => str_starts_with(strtolower(trim($line)), 'rem '))
            ->implode("\n");

        $this->assertStringNotContainsString('powershell', strtolower($executableLines));
        $this->assertStringContainsString('bootstrap\cache\*.php', $startScript);

        $updateScript = File::get(base_path('update.bat'));
        $this->assertStringContainsString('php artisan optimize', $updateScript);
        $this->assertStringContainsString('bootstrap\cache\*.php', $updateScript);
        $this->assertStringNotContainsString('database.sqlite', $updateScript);
    }
}
