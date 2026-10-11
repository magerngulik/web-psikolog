<?php

namespace Tests\Feature;

use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LockScreenTest extends TestCase
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

    public function test_lock_screen_renders_successfully(): void
    {
        Livewire::test('lock-screen')
            ->assertStatus(200);
    }

    public function test_entering_correct_pin_unlocks_and_redirects(): void
    {
        Livewire::test('lock-screen')
            ->call('addNumber', '1')
            ->call('addNumber', '2')
            ->call('addNumber', '3')
            ->call('addNumber', '4')
            ->call('addNumber', '5')
            ->call('addNumber', '6')
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(session()->get('pin_unlocked'));
    }

    public function test_entering_wrong_pin_shows_error_message(): void
    {
        Livewire::test('lock-screen')
            ->call('addNumber', '1')
            ->call('addNumber', '1')
            ->call('addNumber', '1')
            ->call('addNumber', '1')
            ->call('addNumber', '1')
            ->call('addNumber', '1')
            ->assertSet('errorMessage', 'PIN Security Key salah. Silakan coba lagi.')
            ->assertSet('pin', '');

        $this->assertNull(session()->get('pin_unlocked'));
    }

    public function test_delete_number_removes_last_digit(): void
    {
        Livewire::test('lock-screen')
            ->call('addNumber', '1')
            ->call('addNumber', '2')
            ->call('deleteNumber')
            ->assertSet('pin', '1');
    }

    public function test_fresh_database_auto_initializes_default_pin_123456(): void
    {
        // Wipe all security keys to simulate fresh installation / unseeded database
        SecurityKey::query()->delete();
        $this->assertEquals(0, SecurityKey::count());

        Livewire::test('lock-screen')
            ->call('addNumber', '1')
            ->call('addNumber', '2')
            ->call('addNumber', '3')
            ->call('addNumber', '4')
            ->call('addNumber', '5')
            ->call('addNumber', '6')
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(session()->get('pin_unlocked'));
        $this->assertEquals(1, SecurityKey::count());
    }
}

