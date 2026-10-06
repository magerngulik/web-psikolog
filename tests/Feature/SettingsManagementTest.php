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

    public function test_can_create_custom_psychological_intervention_with_auto_generated_id(): void
    {
        Livewire::test('settings.reference-manager')
            ->call('switchGroup', 'psychological_intervention')
            ->set('label', 'Hypnotherapy & Guided Imagery')
            ->set('value', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('system_references', [
            'group_key' => 'psychological_intervention',
            'label' => 'Hypnotherapy & Guided Imagery',
            'value' => '17',
            'is_active' => true,
        ]);
    }

    public function test_can_create_custom_clinical_diagnosis_and_search_via_ppdgj_catalog(): void
    {
        Livewire::test('settings.reference-manager')
            ->call('switchGroup', 'clinical_diagnosis')
            ->set('label', 'Sindrom Kelelahan Kronis / Burnout Akut')
            ->set('value', 'Z73.0')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('system_references', [
            'group_key' => 'clinical_diagnosis',
            'label' => 'Sindrom Kelelahan Kronis / Burnout Akut',
            'value' => 'Z73.0',
            'is_active' => true,
        ]);

        $searchResult = \App\Services\PpdgjCatalog::search('Burnout Akut');
        $this->assertNotEmpty($searchResult);
        $this->assertEquals('Z73.0', $searchResult[0]['code']);
        $this->assertEquals('Sindrom Kelelahan Kronis / Burnout Akut', $searchResult[0]['name']);
    }

    public function test_new_intervention_is_available_in_session_notes_form(): void
    {
        SystemReference::create([
            'group_key' => 'psychological_intervention',
            'label' => 'EMDR Therapy',
            'value' => '17',
            'sort_order' => 17,
            'is_active' => true,
        ]);

        $client = \App\Models\Client::create([
            'client_code' => 'CLI-202610-099',
            'full_name' => 'Klien Master Ref',
            'gender' => 'male',
        ]);

        $case = \App\Models\MedicalCase::create([
            'case_code' => 'CAS-202610-099',
            'client_id' => $client->id,
            'title' => 'Kasus Ref Test',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);

        $session = \App\Models\Session::create([
            'case_id' => $case->id,
            'medical_case_id' => $case->id,
            'session_number' => 1,
            'session_date' => '2026-10-15',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'fee' => 200000,
            'status' => 'scheduled',
            'is_locked' => false,
        ]);

        $component = Livewire::test('sessions.session-show', ['id' => $session->id]);
        $availableInterventions = $component->get('availableInterventions');
        $this->assertArrayHasKey(17, $availableInterventions);
        $this->assertEquals('EMDR Therapy', $availableInterventions[17]);

        $component->set('selected_interventions', [17])
            ->set('objective', 'Klien merespon baik teknik EMDR')
            ->call('saveNotes')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('session_notes', [
            'appointment_id' => $session->id,
            'intervention_ids' => json_encode([17]),
        ]);
    }

    public function test_can_edit_and_delete_reference_in_reference_manager(): void
    {
        $ref = SystemReference::create([
            'group_key' => 'case_category',
            'label' => 'Opsi Sementara',
            'value' => 'opsi_sementara',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Livewire::test('settings.reference-manager')
            ->call('edit', $ref->id)
            ->set('label', 'Opsi Diperbarui')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('system_references', [
            'id' => $ref->id,
            'label' => 'Opsi Diperbarui',
        ]);

        Livewire::test('settings.reference-manager')
            ->call('delete', $ref->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('system_references', [
            'id' => $ref->id,
        ]);
    }
}

