<?php

namespace Tests\Feature;

use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_dashboard_is_accessible_when_pin_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_dashboard_renders_livewire_component(): void
    {
        Livewire::test('dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard Analytics');
    }
}
