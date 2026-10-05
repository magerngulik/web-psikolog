<?php

namespace Tests\Feature;

use App\Models\SecurityKey;
use App\Models\SystemReference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
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

    public function test_reference_manager_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('settings.references'));

        $response->assertStatus(200);
    }

    public function test_security_settings_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('settings.security'));

        $response->assertStatus(200);
    }

    public function test_can_create_and_toggle_system_reference(): void
    {
        Livewire::test('settings.reference-manager')
            ->set('group_key', 'case_category')
            ->set('label', 'Trauma Pasca Bencana')
            ->set('value', 'trauma_pasca_bencana')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('system_references', [
            'group_key' => 'case_category',
            'label' => 'Trauma Pasca Bencana',
            'value' => 'trauma_pasca_bencana',
            'is_active' => true,
        ]);

        $ref = SystemReference::where('value', 'trauma_pasca_bencana')->first();

        Livewire::test('settings.reference-manager')
            ->call('toggleActive', $ref->id);

        $this->assertDatabaseHas('system_references', [
            'id' => $ref->id,
            'is_active' => false,
        ]);
    }

    public function test_can_update_pin_security_key(): void
    {
        Livewire::test('settings.security-settings')
            ->set('current_pin', '123456')
            ->set('new_pin', '654321')
            ->set('new_pin_confirmation', '654321')
            ->call('updatePin')
            ->assertHasNoErrors();

        $activeKey = SecurityKey::where('is_active', true)->first();
        $this->assertTrue(Hash::check('654321', $activeKey->pin_hash));
    }

    public function test_update_pin_fails_if_current_pin_is_wrong(): void
    {
        Livewire::test('settings.security-settings')
            ->set('current_pin', '999999')
            ->set('new_pin', '654321')
            ->set('new_pin_confirmation', '654321')
            ->call('updatePin')
            ->assertHasErrors(['current_pin']);
    }

    public function test_lock_now_clears_session_and_redirects_to_lock_screen(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('settings.security-settings')
            ->call('lockNow')
            ->assertRedirect(route('lock-screen'));

        $this->assertNull(session()->get('pin_unlocked'));
    }
}
