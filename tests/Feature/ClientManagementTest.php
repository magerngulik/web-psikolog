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
        ]);

        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('clients.show', $client->id));

        $response->assertStatus(200)
            ->assertSee('Klien Detail')
            ->assertSee('CLI-202610-001');
    }
}

