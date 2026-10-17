<?php

namespace Tests\Feature;

use App\Livewire\Reports\AnnualLogbook;
use App\Livewire\Settings\ProfileSettings;
use App\Models\Client;
use App\Models\MedicalCase;
use App\Models\PatientSession;
use App\Models\SessionNote;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DigitalSignatureProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        session()->put('pin_unlocked', true);
    }

    public function test_profile_settings_displays_digital_signature_section(): void
    {
        $user = User::create([
            'name' => 'Basirah',
            'email' => 'basirah@example.com',
            'password' => bcrypt('password'),
            'sipa_number' => '503/123-SIPA/2026',
        ]);

        Livewire::test(ProfileSettings::class)
            ->assertSee('3. Tanda Tangan Digital Resmi Psikolog')
            ->assertSee('Pilih File Tanda Tangan')
            ->assertSee('Belum ada tanda tangan digital');
    }

    public function test_can_upload_and_save_digital_signature(): void
    {
        $user = User::create([
            'name' => 'Basirah',
            'email' => 'basirah@example.com',
            'password' => bcrypt('password'),
            'sipa_number' => '503/123-SIPA/2026',
        ]);

        $file = UploadedFile::fake()->image('signature.png', 400, 150);

        Livewire::test(ProfileSettings::class)
            ->set('signature_photo', $file)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Profil psikolog, data tempat praktik & tanda tangan digital berhasil diperbarui!');

        $user->refresh();
        $this->assertNotNull($user->signature_path);
        Storage::disk('public')->assertExists($user->signature_path);

        // Verify base64 accessor
        $base64 = $user->signature_base64;
        $this->assertNotNull($base64);
        $this->assertStringStartsWith('data:image/png;base64,', $base64);
    }

    public function test_can_delete_digital_signature(): void
    {
        // Store initial fake signature
        $file = UploadedFile::fake()->image('old_signature.png', 400, 150);
        $path = $file->store('signatures', 'public');

        $user = User::create([
            'name' => 'Basirah',
            'email' => 'basirah@example.com',
            'password' => bcrypt('password'),
            'sipa_number' => '503/123-SIPA/2026',
            'signature_path' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        Livewire::test(ProfileSettings::class)
            ->assertSee('Hapus Tanda Tangan')
            ->call('deleteSignature')
            ->assertHasNoErrors()
            ->assertSee('Tanda tangan digital berhasil dihapus.');

        $user->refresh();
        $this->assertNull($user->signature_path);
        Storage::disk('public')->assertMissing($path);
        $this->assertNull($user->signature_base64);
    }

    public function test_rpp_document_pdf_renders_with_digital_signature(): void
    {
        $file = UploadedFile::fake()->image('signature.png', 400, 150);
        $path = $file->store('signatures', 'public');

        $user = User::create([
            'name' => 'Basirah',
            'email' => 'basirah@example.com',
            'password' => bcrypt('password'),
            'sipa_number' => '503/123-SIPA/2026',
            'signature_path' => $path,
        ]);

        $client = Client::create([
            'client_code' => 'CLI-202610-001',
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
        ]);

        $case = MedicalCase::create([
            'case_code' => 'CAS-202610-001',
            'client_id' => $client->id,
            'title' => 'Konseling Kecemasan',
            'category' => 'Dewasa',
            'status' => 'active',
        ]);

        $session = PatientSession::create([
            'case_id' => $case->id,
            'session_number' => 1,
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'duration_minutes' => 60,
            'status' => 'done',
            'fee' => 150000,
            'payment_status' => 'paid',
        ]);

        $note = SessionNote::create([
            'appointment_id' => $session->id,
            'qr_code_token' => 'RPP-TOKEN-123456',
            'subjective_complaint' => 'Keluhan cemas',
            'subjective_problem' => 'Masalah tidur',
            'objective' => 'Gelisah',
            'assessment' => 'Kecemasan sedang',
            'plan' => 'CBT',
            'assessment_methods' => ['Observasi'],
            'intervention_ids' => ['CBT'],
        ]);

        $sessionWithRelations = PatientSession::with(['patient', 'psychologist', 'medicalCase.client'])->findOrFail($session->id);

        $pdf = Pdf::loadView('pdf.rpp-document', [
            'appointment' => $sessionWithRelations,
            'note' => $note,
            'interventions' => ['CBT' => 'Cognitive Behavioral Therapy'],
        ])->setPaper('a4', 'portrait');

        $output = $pdf->output();
        $this->assertNotEmpty($output);
    }

    public function test_annual_logbook_pdf_renders_with_digital_signature(): void
    {
        $file = UploadedFile::fake()->image('signature.png', 400, 150);
        $path = $file->store('signatures', 'public');

        User::create([
            'name' => 'Basirah',
            'email' => 'basirah@example.com',
            'password' => bcrypt('password'),
            'sipa_number' => '503/123-SIPA/2026',
            'signature_path' => $path,
        ]);

        $data = AnnualLogbook::calculateLogbookData(2026);
        $pdf = Pdf::loadView('pdf.annual-logbook', $data)->setPaper('a4', 'landscape');

        $output = $pdf->output();
        $this->assertNotEmpty($output);
    }
}
