<?php

namespace Tests\Feature;

use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfflineAssetsTest extends TestCase
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

    public function test_manifest_and_compiled_assets_exist(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertFileExists($manifestPath, 'Vite manifest.json must exist in public/build/');

        $manifest = json_decode(File::get($manifestPath), true);
        $this->assertArrayHasKey('resources/css/app.css', $manifest);

        $cssFile = public_path('build/'.$manifest['resources/css/app.css']['file']);
        $this->assertFileExists($cssFile, 'Compiled CSS asset must exist');
        $this->assertGreaterThan(5000, filesize($cssFile), 'Compiled CSS must not be empty');
    }

    public function test_local_font_files_exist_in_public_directory(): void
    {
        $this->assertFileExists(public_path('fonts/plus-jakarta-sans.woff2'));
        $this->assertFileExists(public_path('fonts/fraunces.woff2'));
        $this->assertFileExists(public_path('fonts/jetbrains-mono.woff2'));
    }

    public function test_dashboard_renders_vite_assets_and_no_external_cdns(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);

        // Must include Vite assets (either dev server or compiled build)
        if (file_exists(public_path('hot'))) {
            $response->assertSee(':5173');
        } else {
            $response->assertSee('build/assets/app-');
        }

        // Must NOT include any external CDNs
        $response->assertDontSee('cdn.tailwindcss.com');
        $response->assertDontSee('fonts.googleapis.com');
        $response->assertDontSee('fonts.gstatic.com');
    }

    public function test_lock_screen_renders_vite_assets_and_no_external_cdns(): void
    {
        $response = $this->get(route('lock-screen'));

        $response->assertStatus(200);

        if (file_exists(public_path('hot'))) {
            $response->assertSee(':5173');
        } else {
            $response->assertSee('build/assets/app-');
        }

        $response->assertDontSee('cdn.tailwindcss.com');
        $response->assertDontSee('fonts.googleapis.com');
        $response->assertDontSee('fonts.gstatic.com');
    }
}
