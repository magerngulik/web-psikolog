<?php

namespace Tests\Feature;

use App\Models\AppLicense;
use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OfflineLicenseTest extends TestCase
{
    use RefreshDatabase;

    protected LicenseService $licenseService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->licenseService = new LicenseService();
        config(['license.enabled' => true]);
        LicenseService::setFakeMachineId(null);
    }

    protected function tearDown(): void
    {
        LicenseService::setFakeMachineId(null);
        parent::tearDown();
    }

    public function test_machine_id_format_and_stability(): void
    {
        $id1 = $this->licenseService->getMachineId();
        $id2 = $this->licenseService->getMachineId();

        $this->assertEquals($id1, $id2);
        $this->assertMatchesRegularExpression('/^WP-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $id1);
    }

    public function test_license_generation_and_valid_offline_activation(): void
    {
        $machineId = $this->licenseService->getMachineId();
        $clientName = 'Dr. Sarah, M.Psi., Psikolog';

        $token = $this->licenseService->generateLicenseToken($machineId, $clientName);
        $this->assertStringStartsWith('WP-LIC-', $token);

        $result = $this->licenseService->activate($token);
        $this->assertTrue($result['success']);
        $this->assertTrue($this->licenseService->isLicensed());

        $activeLicense = $this->licenseService->getActiveLicense();
        $this->assertNotNull($activeLicense);
        $this->assertEquals($clientName, $activeLicense->client_name);
        $this->assertEquals($machineId, $activeLicense->machine_id);
    }

    public function test_tampered_token_is_rejected(): void
    {
        $machineId = $this->licenseService->getMachineId();
        $token = $this->licenseService->generateLicenseToken($machineId, 'Ibu Sarah');

        // Tamper with the token string
        $tamperedToken = substr($token, 0, -5) . 'XXXXX';

        $result = $this->licenseService->activate($tamperedToken);
        $this->assertFalse($result['success']);
        $this->assertFalse($this->licenseService->isLicensed());
    }

    public function test_license_issued_for_another_machine_is_rejected(): void
    {
        $machineA = 'WP-AAAA-1111-2222-3333';
        $machineB = 'WP-BBBB-4444-5555-6666';

        // Generate token strictly for Machine A
        $tokenForA = $this->licenseService->generateLicenseToken($machineA, 'Klien A');

        // Attempt activation on Machine B
        LicenseService::setFakeMachineId($machineB);

        $result = $this->licenseService->activate($tokenForA);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('tidak cocok dengan laptop ini', $result['message']);
        $this->assertFalse($this->licenseService->isLicensed());
    }

    public function test_unlicensed_machine_is_redirected_to_activation(): void
    {
        $this->assertFalse($this->licenseService->isLicensed());

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('activation'));

        $responseLock = $this->get(route('lock-screen'));
        $responseLock->assertRedirect(route('activation'));
    }

    public function test_activation_screen_renders_successfully(): void
    {
        $response = $this->get(route('activation'));
        $response->assertStatus(200);
        $response->assertSee('Aktivasi Lisensi Web Psikolog');
        $response->assertSee($this->licenseService->getMachineId());
        $response->assertSee('Aktifkan Aplikasi Sekarang');
    }

    public function test_livewire_activation_flow_with_valid_token(): void
    {
        $machineId = $this->licenseService->getMachineId();
        $token = $this->licenseService->generateLicenseToken($machineId, 'Psikolog Teladan');

        Livewire::test('activation')
            ->set('licenseToken', $token)
            ->call('activate')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('message');

        $this->assertTrue($this->licenseService->isLicensed());
        $this->assertEquals(1, AppLicense::count());
    }

    public function test_livewire_activation_flow_with_invalid_token(): void
    {
        Livewire::test('activation')
            ->set('licenseToken', 'WP-LIC-invalid-token-here')
            ->call('activate')
            ->assertNoRedirect()
            ->assertSet('errorMessage', 'Struktur Kunci Lisensi tidak dikenali.');

        $this->assertFalse($this->licenseService->isLicensed());
    }

    public function test_artisan_license_generate_command(): void
    {
        $machineId = 'WP-9999-8888-7777-6666';

        $this->artisan('license:generate', [
            'machine_id' => $machineId,
            '--client' => 'Dr. Budi Santoso',
        ])
        ->expectsOutputToContain('KUNCI LISENSI AKTIVASI')
        ->expectsOutputToContain('WP-LIC-')
        ->assertSuccessful();
    }
}
