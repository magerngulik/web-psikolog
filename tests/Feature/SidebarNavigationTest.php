<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_navigation_renders_all_categorized_modules(): void
    {
        // Simulate unlocked PIN session
        session(['pin_unlocked' => true]);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);

        // Sidebar container
        $response->assertSee('id="sanctuary-sidebar"', false);
        $response->assertSee('Web Psikolog');
        $response->assertSee('Clinical Sanctuary');

        // Categories
        $response->assertSee('Layanan Klinis');
        $response->assertSee('Profesi & Komunitas', false);
        $response->assertSee('Pengaturan & Sistem', false);

        // Category 1: Layanan Klinis
        $response->assertSee(route('dashboard'));
        $response->assertSee('Dashboard');
        $response->assertSee(route('clients.index'));
        $response->assertSee('Data Klien');
        $response->assertSee(route('cases.index'));
        $response->assertSee('Kasus Medis');
        $response->assertSee(route('sessions.index'));
        $response->assertSee('Sesi Konseling');

        // Category 2: Profesi & Komunitas
        $response->assertSee(route('activities.index'));
        $response->assertSee('Kegiatan & Seminar', false);
        $response->assertSee(route('organizations.index'));
        $response->assertSee('Mitra Organisasi');
        $response->assertSee(route('reports.logbook'));
        $response->assertSee('Logbook SKP');

        // Category 3: Pengaturan & Sistem
        $response->assertSee(route('settings.references'));
        $response->assertSee('Master Referensi');
        $response->assertSee(route('settings.profile'));
        $response->assertSee('Profil Praktik');
        $response->assertSee(route('settings.security'));
        $response->assertSee('Keamanan PIN');
        $response->assertSee(route('settings.backup'));
        $response->assertSee('Backup Database');

        // Status & Quick Lock button
        $response->assertSee('Praktik Aktif');
        $response->assertSee(route('lock-screen'));
    }
}
