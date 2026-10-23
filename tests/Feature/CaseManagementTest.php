<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\SecurityKey;
use App\Models\SystemReference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class CaseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        $this->client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Klien Test Kasus',
            'gender' => 'female',
        ]);
    }

    public function test_case_index_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('cases.index'));

        $response->assertStatus(200);
    }

    public function test_case_create_page_is_accessible_when_unlocked(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('cases.create'));

        $response->assertStatus(200)
            ->assertSee('Keluhan Utama Klien')
            ->assertSee('Pokok Masalah / Pemicu');
    }

    public function test_can_create_new_medical_case(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Livewire::test('cases.case-create')
            ->set('client_id', $this->client->id)
            ->set('title', 'Kecemasan Menghadapi Karir Baru')
            ->set('category', 'Anxiety')
            ->set('subjective_complaint', 'Kaki bergetar dan jantung berdebar')
            ->set('subjective_problem', 'Cemas ketemu orang dan takut dihakimi')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cases', [
            'client_id' => $this->client->id,
            'title' => 'Kecemasan Menghadapi Karir Baru',
            'category' => 'Anxiety',
            'subjective_complaint' => 'Kaki bergetar dan jantung berdebar',
            'subjective_problem' => 'Cemas ketemu orang dan takut dihakimi',
            'status' => 'active',
        ]);
    }

    public function test_can_search_and_filter_medical_cases(): void
    {
        $caseA = MedicalCase::create([
            'case_code' => 'CAS-202610-001',
            'client_id' => $this->client->id,
            'title' => 'Kecemasan Pekerjaan',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);

        $caseB = MedicalCase::create([
            'case_code' => 'CAS-202610-002',
            'client_id' => $this->client->id,
            'title' => 'Depresi Ringan',
            'category' => 'Depression',
            'status' => 'completed',
        ]);

        Livewire::test('cases.case-index')
            ->set('search', 'Kecemasan')
            ->assertSee('Kecemasan Pekerjaan')
            ->assertDontSee('Depresi Ringan');

        Livewire::test('cases.case-index')
            ->set('statusFilter', 'completed')
            ->assertSee('Depresi Ringan')
            ->assertDontSee('Kecemasan Pekerjaan');
    }

    public function test_can_update_case_status_and_progress_note(): void
    {
        $case = MedicalCase::create([
            'case_code' => 'CAS-202610-001',
            'client_id' => $this->client->id,
            'title' => 'Kasus Update Test',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);

        Livewire::test('cases.case-show', ['id' => $case->id])
            ->set('progressNote', 'Catatan perkembangan terapi sesi 1 membaik.')
            ->call('updateProgress')
            ->assertHasNoErrors()
            ->call('updateStatus', 'completed')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'status' => 'completed',
            'progress_note' => 'Catatan perkembangan terapi sesi 1 membaik.',
        ]);
    }

    public function test_case_create_displays_info_tooltip_and_loads_categories_from_system_reference(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        SystemReference::create([
            'group_key' => 'case_category',
            'label' => 'Trauma Masa Kecil',
            'value' => 'trauma_masa_kecil',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $component = Livewire::test('cases.case-create');
        $categories = $component->get('categories');

        $this->assertTrue($categories->pluck('label')->contains('Trauma Masa Kecil'));
        $component->assertSee('Kustomisasi Kategori Kasus')
            ->assertSee('Buka Master Referensi');
    }

    public function test_custom_category_added_in_admin_is_available_in_case_create_and_case_index(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        // 1. Tambah kategori baru via ReferenceManager (admin)
        Livewire::test('settings.reference-manager')
            ->set('group_key', 'case_category')
            ->set('label', 'Gangguan Tidur & Insomnia')
            ->set('value', 'insomnia_disorder')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('system_references', [
            'group_key' => 'case_category',
            'label' => 'Gangguan Tidur & Insomnia',
            'value' => 'insomnia_disorder',
        ]);

        // 2. Kategori baru tersebut langsung muncul di dropdown CaseCreate
        Livewire::test('cases.case-create')
            ->assertSee('Gangguan Tidur & Insomnia');

        // 3. Kategori baru tersebut juga muncul di filter CaseIndex
        Livewire::test('cases.case-index')
            ->assertSee('Gangguan Tidur & Insomnia');
    }

    public function test_creating_case_after_soft_delete_does_not_collide_case_code(): void
    {
        $this->withSession(['pin_unlocked' => true]);
        $prefix = 'CAS-'.date('Ym').'-';

        $case1 = MedicalCase::create([
            'client_id' => $this->client->id,
            'title' => 'Kasus Pertama',
            'category' => 'Anxiety',
            'status' => 'active',
        ]);
        $case2 = MedicalCase::create([
            'client_id' => $this->client->id,
            'title' => 'Kasus Kedua',
            'category' => 'Depression',
            'status' => 'active',
        ]);

        $this->assertEquals($prefix.'001', $case1->case_code);
        $this->assertEquals($prefix.'002', $case2->case_code);

        // Soft delete case 2 (the highest code 002) so active MedicalCase::count() becomes 1
        $case2->delete();
        $this->assertEquals(1, MedicalCase::count());

        Livewire::test('cases.case-create')
            ->set('client_id', $this->client->id)
            ->set('title', 'Kasus Ketiga Setelah Hapus')
            ->set('category', 'Anxiety')
            ->set('subjective_complaint', 'Keluhan')
            ->set('subjective_problem', 'Masalah')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cases', [
            'title' => 'Kasus Ketiga Setelah Hapus',
            'case_code' => $prefix.'003',
        ]);
    }
}
