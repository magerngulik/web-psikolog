<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ClientManagementTest extends TestCase
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

    public function test_client_index_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('clients.index'));

        $response->assertStatus(200);
    }

    public function test_client_create_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('clients.create'));

        $response->assertStatus(200);
    }

    public function test_can_create_new_client(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('clients.client-create')
            ->set('full_name', 'Budi Santoso')
            ->set('nickname', 'Budi')
            ->set('gender', 'male')
            ->set('phone_number', '081234567890')
            ->set('email', 'budi@example.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'full_name' => 'Budi Santoso',
            'phone_number' => '081234567890',
            'email' => 'budi@example.com',
        ]);
    }

    public function test_can_search_and_filter_clients(): void
    {
        $clientA = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Siti Rahma',
            'gender' => 'female',
        ]);

        $clientB = Client::create([
            'client_code' => 'CLI-202610-002',
            'full_name' => 'Ahmad Dahlan',
            'gender' => 'male',
        ]);

        Livewire::test('clients.client-index')
            ->set('search', 'Siti')
            ->assertSee('Siti Rahma')
            ->assertDontSee('Ahmad Dahlan');

        Livewire::test('clients.client-index')
            ->set('genderFilter', 'male')
            ->assertSee('Ahmad Dahlan')
            ->assertDontSee('Siti Rahma');
    }

    public function test_can_soft_delete_client(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Klien Hapus',
            'gender' => 'female',
        ]);

        Livewire::test('clients.client-index')
            ->call('deleteClient', $client->id);

        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_client_show_page_displays_client_info(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Klien Detail',
            'gender' => 'female',
            'phone_number' => '0899999999',
            'nik' => '3201012345670001',
            'last_education' => 'Sarjana (S1)',
            'birth_order' => 2,
            'total_siblings' => 3,
            'is_disabled' => true,
            'disability_description' => 'Tunarungu',
        ]);

        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('clients.show', $client->id));

        $response->assertStatus(200)
            ->assertSee('Klien Detail')
            ->assertSee('CLI-202610-001')
            ->assertSee('3201012345670001')
            ->assertSee('Sarjana (S1)')
            ->assertSee('Anak ke-2 dari 3 bersaudara')
            ->assertSee('Penyandang Disabilitas (Difabel)')
            ->assertSee('Tunarungu');
    }

    public function test_can_create_client_with_extended_demographic_fields(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('clients.client-create')
            ->set('full_name', 'Dewi Lestari')
            ->set('gender', 'female')
            ->set('nik', '3273019876540002')
            ->set('last_education', 'Magister (S2)')
            ->set('birth_order', 1)
            ->set('total_siblings', 4)
            ->set('is_disabled', true)
            ->set('disability_description', 'Tunadaksa')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'full_name' => 'Dewi Lestari',
            'nik' => '3273019876540002',
            'last_education' => 'Magister (S2)',
            'birth_order' => 1,
            'total_siblings' => 4,
            'is_disabled' => true,
            'disability_description' => 'Tunadaksa',
        ]);
    }

    public function test_can_create_client_with_empty_extended_fields(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('clients.client-create')
            ->set('full_name', 'Klien Tanpa Detail')
            ->set('gender', 'male')
            ->set('nik', '')
            ->set('last_education', '')
            ->set('birth_order', '')
            ->set('total_siblings', '')
            ->set('is_disabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'full_name' => 'Klien Tanpa Detail',
            'nik' => null,
            'last_education' => null,
            'birth_order' => null,
            'total_siblings' => null,
            'is_disabled' => false,
            'disability_description' => null,
        ]);
    }

    public function test_client_edit_page_is_accessible_when_unlocked(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-003',
            'full_name' => 'Klien Untuk Edit',
            'gender' => 'female',
        ]);

        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('clients.edit', $client->id));

        $response->assertStatus(200)
            ->assertSee('Edit Data Klien');
    }

    public function test_can_edit_client_profile_and_extended_fields(): void
    {
        $client = Client::create([
            'client_code' => 'CLI-202610-004',
            'full_name' => 'Klien Awal',
            'gender' => 'male',
            'is_disabled' => false,
        ]);

        Livewire::test('clients.client-edit', ['id' => $client->id])
            ->set('full_name', 'Klien Diperbarui')
            ->set('nik', '3171011122330005')
            ->set('last_education', 'Diploma (D1-D4)')
            ->set('birth_order', 3)
            ->set('total_siblings', 5)
            ->set('is_disabled', true)
            ->set('disability_description', 'ADHD / Neurodivergent')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('clients.show', $client->id));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'full_name' => 'Klien Diperbarui',
            'nik' => '3171011122330005',
            'last_education' => 'Diploma (D1-D4)',
            'birth_order' => 3,
            'total_siblings' => 5,
            'is_disabled' => true,
            'disability_description' => 'ADHD / Neurodivergent',
        ]);
    }
}


