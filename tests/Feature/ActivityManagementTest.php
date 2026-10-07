<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Organization;
use App\Models\SecurityKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SecurityKey::create([
            'pin_hash' => Hash::make('123456'),
            'is_active' => true,
        ]);

        Storage::fake('public');
    }

    public function test_organization_index_accessible_and_can_create_organization(): void
    {
        $response = $this->withSession(['pin_unlocked' => true])
            ->get(route('organizations.index'));

        $response->assertStatus(200);

        Livewire::test('organizations.organization-index')
            ->set('name', 'SMA Negeri 1 Pekanbaru')
            ->set('category', 'school')
            ->set('city', 'Pekanbaru')
            ->set('pic_name', 'Ibu Siti Rahma')
            ->set('pic_phone', '081234567890')
            ->set('pic_position', 'Guru BK')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('organizations', [
            'name' => 'SMA Negeri 1 Pekanbaru',
            'category' => 'school',
            'city' => 'Pekanbaru',
            'pic_name' => 'Ibu Siti Rahma',
        ]);
    }

    public function test_can_schedule_activity_with_file_uploads(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $org = Organization::create([
            'name' => 'Universitas Indonesia Peduli',
            'category' => 'university',
            'city' => 'Depok',
        ]);

        $invitation = UploadedFile::fake()->create('surat_undangan.pdf', 500, 'application/pdf');
        $certificate = UploadedFile::fake()->create('sertifikat_narasumber.pdf', 300, 'application/pdf');
        $photo1 = UploadedFile::fake()->image('dokumentasi_1.jpg');
        $photo2 = UploadedFile::fake()->image('dokumentasi_2.jpg');

        Livewire::test('activities.activity-create')
            ->set('organization_id', $org->id)
            ->set('title', 'Seminar Regulasi Emosi Remaja')
            ->set('event_type', 'seminar')
            ->set('role', 'keynote_speaker')
            ->set('start_date', '2026-10-25')
            ->set('start_time', '09:00')
            ->set('end_time', '12:00')
            ->set('delivery_mode', 'offline')
            ->set('location_venue', 'Auditorium UI')
            ->set('fee', 2500000)
            ->set('payment_status', 'paid')
            ->set('payment_method', 'transfer')
            ->set('skp_points', 1.0)
            ->set('estimated_audience', 150)
            ->set('invitation_file', $invitation)
            ->set('certificate_file', $certificate)
            ->set('photos', [$photo1, $photo2])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $activity = Activity::where('title', 'Seminar Regulasi Emosi Remaja')->first();
        $this->assertNotNull($activity);
        $this->assertEquals(2500000, $activity->fee);
        $this->assertEquals('paid', $activity->payment_status);
        $this->assertEquals(1.0, $activity->skp_points);

        // Verify attachments created in database
        $this->assertDatabaseHas('activity_attachments', [
            'activity_id' => $activity->id,
            'file_type' => 'invitation_letter',
        ]);
        $this->assertDatabaseHas('activity_attachments', [
            'activity_id' => $activity->id,
            'file_type' => 'certificate',
        ]);
        $this->assertDatabaseHas('activity_attachments', [
            'activity_id' => $activity->id,
            'file_type' => 'documentation_photo',
        ]);
    }

    public function test_activity_revenue_is_included_in_dashboard(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        Activity::create([
            'title' => 'Workshop Psikoedukasi Komunitas',
            'event_type' => 'workshop',
            'role' => 'facilitator',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
            'fee' => 1500000,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);

        Livewire::test('dashboard')
            ->assertViewHas('monthlyRevenue', function ($val) {
                return $val >= 1500000;
            })
            ->assertViewHas('monthlyActivityRevenue', function ($val) {
                return $val == 1500000;
            });
    }

    public function test_can_view_and_manage_activity_details(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $activity = Activity::create([
            'title' => 'Talkshow Kesehatan Mental di Sekolah',
            'event_type' => 'talkshow',
            'role' => 'keynote_speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
            'location_venue' => 'Aula Sekolah',
            'fee' => 1000000,
            'payment_status' => 'unpaid',
            'status' => 'scheduled',
        ]);

        Livewire::test('activities.activity-show', ['id' => $activity->id])
            ->assertSee('Talkshow Kesehatan Mental di Sekolah')
            ->call('markStatus', 'completed')
            ->assertHasNoErrors()
            ->call('markPaymentStatus', 'paid')
            ->assertHasNoErrors();

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertEquals('paid', $activity->payment_status);
    }

    public function test_activity_code_generation_handles_soft_deleted_records(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        // Create first activity
        $act1 = Activity::create([
            'title' => 'Acara Pertama',
            'event_type' => 'seminar',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);

        $firstCode = $act1->activity_code;

        // Soft delete the first activity
        $act1->delete();

        // Create second activity - should NOT collide with the soft-deleted code
        $act2 = Activity::create([
            'title' => 'Acara Kedua',
            'event_type' => 'workshop',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);

        $this->assertNotEquals($firstCode, $act2->activity_code);
        $this->assertDatabaseCount('activities', 2); // 1 soft-deleted, 1 active
    }

    public function test_can_delete_activity_from_index(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $activity = Activity::create([
            'title' => 'Kegiatan Yang Akan Dihapus',
            'event_type' => 'seminar',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);

        Livewire::test('activities.activity-index')
            ->call('delete', $activity->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('activities', ['id' => $activity->id]);
    }

    public function test_can_delete_activity_from_show_and_edit(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $activity1 = Activity::create([
            'title' => 'Kegiatan Dihapus dari Show',
            'event_type' => 'seminar',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);

        Livewire::test('activities.activity-show', ['id' => $activity1->id])
            ->call('deleteActivity')
            ->assertRedirect(route('activities.index'));

        $this->assertSoftDeleted('activities', ['id' => $activity1->id]);

        $activity2 = Activity::create([
            'title' => 'Kegiatan Dihapus dari Edit',
            'event_type' => 'workshop',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);

        Livewire::test('activities.activity-edit', ['id' => $activity2->id])
            ->call('deleteActivity')
            ->assertRedirect(route('activities.index'));

        $this->assertSoftDeleted('activities', ['id' => $activity2->id]);
    }

    public function test_can_delete_attachment_from_show_and_edit(): void
    {
        $this->withSession(['pin_unlocked' => true]);

        $activity = Activity::create([
            'title' => 'Kegiatan Pengujian Lampiran',
            'event_type' => 'seminar',
            'role' => 'speaker',
            'start_date' => now()->toDateString(),
            'delivery_mode' => 'offline',
        ]);

        $file = UploadedFile::fake()->create('surat_salah.pdf', 100, 'application/pdf');
        $storedPath = $file->store('activities/invitations', 'public');

        $attachment = \App\Models\ActivityAttachment::create([
            'activity_id' => $activity->id,
            'file_type' => 'invitation_letter',
            'file_path' => $storedPath,
            'file_name' => 'surat_salah.pdf',
        ]);

        Storage::disk('public')->assertExists($storedPath);

        // Delete via ActivityEdit
        Livewire::test('activities.activity-edit', ['id' => $activity->id])
            ->call('deleteAttachment', $attachment->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('activity_attachments', ['id' => $attachment->id]);
        Storage::disk('public')->assertMissing($storedPath);
    }
}

