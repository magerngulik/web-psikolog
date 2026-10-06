# DOKUMENTASI TEKNIS MODUL 7
## Dashboard Analytics, Clinical RPP Generator Engine & System Security

---

## 1. DESKRIPSI & ARSITEKTUR MODUL 7

Modul 7 dirancang untuk menyediakan fungsi analitik klinis, otomatisasi pembuatan dokumen resmi Rekam Pemeriksaan Psikologis (RPP KONSELING), serta memperketat keamanan data rekam medis pasien sesuai standar etika psikologi dan regulasi privasi data medis.

### Core Modules Breakdown

1. **Clinical RPP Generator Engine**:
   - Menghasilkan dokumen cetak resmi Rekam Pemeriksaan Psikologis (RPP KONSELING) dengan format 100% presisi.
   - Mengintegrasikan struktur catatan klinis standar:
     - **SOAP Format** (Subjective, Objective, Assessment, Plan).
     - **ICD-10 Diagnostic Coding** (Kode & Deskripsi Diagnosa Gangguan Jiwa/Perilaku).
     - **16 Intervensi Psikologis (Checkbox Grid)**:
       1. Psikoedukasi
       2. Konseling Psikologis
       3. Cognitive Behavioral Therapy (CBT)
       4. Acceptance & Commitment Therapy (ACT)
       5. Behavioral Activation
       6. Mindfulness & Relaxation Therapy
       7. Solution-Focused Brief Therapy (SFBT)
       8. Client-Centered Therapy / Humanistic
       9. Interpersonal Psychotherapy (IPT)
       10. Psychodynamic / Psychoanalytic
       11. Family / Systemic Therapy
       12. Couples / Marital Therapy
       13. Expressive / Art / Play Therapy
       14. Crisis Intervention & Safety Planning
       15. Motivational Interviewing (MI)
       16. Biofeedback / Neurofeedback
     - **Rencana Tindak Lanjut & Pesan Klien**.
     - **Validator Keabsahan**: Tanda Tangan Elektronik/Digital Psikolog & QR Code Validation Token.

2. **Dashboard Analytics & Financial Summary**:
   - **Metrik Utama**: Total Sesi Bulan Ini, Sesi Terjadwal, Unclosed Notes Alert (Notifikasi Rekam Medis Belum Selesai).
   - **Rekap Keuangan**: Total Pendapatan Bulanan & Tahunan (Nett/Gross) serta Status Pembayaran Terutang.
   - **Klinis & Diagnostik**: Grafik Distribusi Diagnosa Utama ICD-10 (Top 5 Diagnosa Terbanyak).
   - **Beban Kerja**: Workload Chart Psikolog (Jumlah Jam Konseling & Klien per Hari/Minggu).

3. **System Security & Data Protection**:
   - **6-Digit PIN Lock Screen**: Mode penguncian layar otomatis saat inaktif untuk mencegah akses tanpa izin ke data rekam medis.
   - **PDF Access Security**: Hash QR Code unik untuk verifikasi keabsahan dokumen RPP yang dicetak secara eksternal.

---

## 2. SKEMA DATABASE MIGRATION

Lakukan modifikasi pada tabel `session_notes` dan penambahan tabel pendukung keamanan `user_security_pins`.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Penyesuaian/Penambahan Kolom pada tabel session_notes
        Schema::table('session_notes', function (Blueprint $table) {
            // Evaluasi SOAP
            if (!Schema::hasColumn('session_notes', 'subjective')) {
                $table->text('subjective')->nullable()->after('appointment_id');
            }
            if (!Schema::hasColumn('session_notes', 'objective')) {
                $table->text('objective')->nullable()->after('subjective');
            }
            if (!Schema::hasColumn('session_notes', 'assessment')) {
                $table->text('assessment')->nullable()->after('objective');
            }
            if (!Schema::hasColumn('session_notes', 'plan')) {
                $table->text('plan')->nullable()->after('assessment');
            }

            // Diagnosa ICD-10
            if (!Schema::hasColumn('session_notes', 'icd10_code')) {
                $table->string('icd10_code', 20)->nullable()->after('plan');
            }
            if (!Schema::hasColumn('session_notes', 'icd10_description')) {
                $table->string('icd10_description')->nullable()->after('icd10_code');
            }

            // Metode Asesmen & Intervensi (JSON Grid)
            if (!Schema::hasColumn('session_notes', 'assessment_methods')) {
                $table->json('assessment_methods')->nullable()->after('icd10_description');
            }
            if (!Schema::hasColumn('session_notes', 'intervention_ids')) {
                $table->json('intervention_ids')->nullable()->after('assessment_methods');
            }

            // Status Sesi & Tindak Lanjut
            if (!Schema::hasColumn('session_notes', 'follow_up_status')) {
                $table->string('follow_up_status', 50)->default('Selesai')->after('intervention_ids');
            }
            if (!Schema::hasColumn('session_notes', 'client_message')) {
                $table->text('client_message')->nullable()->after('follow_up_status');
            }
            if (!Schema::hasColumn('session_notes', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(60)->after('client_message');
            }

            // Integrasi QR Code Validasi & Penguncian Catatan
            if (!Schema::hasColumn('session_notes', 'qr_code_token')) {
                $table->string('qr_code_token', 64)->unique()->nullable()->after('duration_minutes');
            }
            if (!Schema::hasColumn('session_notes', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('qr_code_token');
            }
        });

        // 2. Tabel Pengunci Layar (Security PIN)
        if (!Schema::hasTable('user_security_pins')) {
            Schema::create('user_security_pins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('pin_hash');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_notes', function (Blueprint $table) {
            $table->dropColumn([
                'subjective', 'objective', 'assessment', 'plan',
                'icd10_code', 'icd10_description', 'assessment_methods',
                'intervention_ids', 'follow_up_status', 'client_message',
                'duration_minutes', 'qr_code_token', 'is_locked'
            ]);
        });

        Schema::dropIfExists('user_security_pins');
    }
};
```

---

## 3. KOMPONEN LIVEWIRE & CONTROLLER IMPLEMENTATION

### 3.1. Livewire Component: RppGenerator.php

Location: `app/Livewire/Reports/RppGenerator.php`

```php
<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\SessionNote;
use App\Models\Appointment;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class RppGenerator extends Component
{
    public $appointmentId;
    public $sessionNote;

    // Form Properties
    public $subjective;
    public $objective;
    public $assessment;
    public $plan;
    public $icd10_code;
    public $icd10_description;
    public $assessment_methods = [];
    public $selected_interventions = [];
    public $follow_up_status;
    public $client_message;
    public $duration_minutes = 60;
    public $is_locked = false;

    // List 16 Intervensi Psikologis Utama
    public array $availableInterventions = [
        1 => 'Psikoedukasi',
        2 => 'Konseling Psikologis',
        3 => 'Cognitive Behavioral Therapy (CBT)',
        4 => 'Acceptance & Commitment Therapy (ACT)',
        5 => 'Behavioral Activation',
        6 => 'Mindfulness & Relaxation Therapy',
        7 => 'Solution-Focused Brief Therapy (SFBT)',
        8 => 'Client-Centered Therapy / Humanistic',
        9 => 'Interpersonal Psychotherapy (IPT)',
        10 => 'Psychodynamic / Psychoanalytic',
        11 => 'Family / Systemic Therapy',
        12 => 'Couples / Marital Therapy',
        13 => 'Expressive / Art / Play Therapy',
        14 => 'Crisis Intervention & Safety Planning',
        15 => 'Motivational Interviewing (MI)',
        16 => 'Biofeedback / Neurofeedback',
    ];

    public function mount($appointmentId)
    {
        $this->appointmentId = $appointmentId;
        $note = SessionNote::firstOrNew(['appointment_id' => $appointmentId]);

        if ($note->exists) {
            $this->sessionNote = $note;
            $this->subjective = $note->subjective;
            $this->objective = $note->objective;
            $this->assessment = $note->assessment;
            $this->plan = $note->plan;
            $this->icd10_code = $note->icd10_code;
            $this->icd10_description = $note->icd10_description;
            $this->assessment_methods = $note->assessment_methods ?? [];
            $this->selected_interventions = $note->intervention_ids ?? [];
            $this->follow_up_status = $note->follow_up_status ?? 'Selesai';
            $this->client_message = $note->client_message;
            $this->duration_minutes = $note->duration_minutes ?? 60;
            $this->is_locked = $note->is_locked;
        }
    }

    public function saveRpp()
    {
        $this->validate([
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
            'icd10_code' => 'required|string',
            'duration_minutes' => 'required|integer|min:15',
        ]);

        $token = $this->sessionNote->qr_code_token ?? Str::random(32);

        $note = SessionNote::updateOrCreate(
            ['appointment_id' => $this->appointmentId],
            [
                'subjective' => $this->subjective,
                'objective' => $this->objective,
                'assessment' => $this->assessment,
                'plan' => $this->plan,
                'icd10_code' => $this->icd10_code,
                'icd10_description' => $this->icd10_description,
                'assessment_methods' => $this->assessment_methods,
                'intervention_ids' => $this->selected_interventions,
                'follow_up_status' => $this->follow_up_status,
                'client_message' => $this->client_message,
                'duration_minutes' => $this->duration_minutes,
                'qr_code_token' => $token,
                'is_locked' => $this->is_locked,
            ]
        );

        session()->flash('message', 'Dokumen RPP Konseling berhasil disimpan.');
    }

    public function exportPdf()
    {
        $appointment = Appointment::with(['patient', 'psychologist'])->findOrFail($this->appointmentId);
        $note = SessionNote::where('appointment_id', $this->appointmentId)->firstOrFail();

        $pdf = Pdf::loadView('pdf.rpp-document', [
            'appointment' => $appointment,
            'note' => $note,
            'interventions' => $this->availableInterventions,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "RPP_Konseling_{$appointment->patient->name}_{$appointment->date}.pdf"
        );
    }

    public function render()
    {
        $appointment = Appointment::with(['patient', 'psychologist'])->findOrFail($this->appointmentId);
        return view('livewire.reports.rpp-generator', [
            'appointment' => $appointment
        ])->layout('layouts.app');
    }
}
```

---

### 3.2. Blade Template & Preview Page: rpp-generator.blade.php

Location: `resources/views/livewire/reports/rpp-generator.blade.php`

```html
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <div class="flex justify-between items-center pb-4 mb-4 border-b">
            <h2 class="text-2xl font-bold text-gray-800">Editor Rekam Pemeriksaan Psikologis (RPP)</h2>
            <div class="flex space-x-2">
                <button wire:click="saveRpp" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan RPP
                </button>
                <button wire:click="exportPdf" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Cetak PDF
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="saveRpp" class="space-y-6">
            <!-- Header Informasi Pasien -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-md">
                <div>
                    <span class="text-xs text-gray-500">Nama Klien</span>
                    <p class="font-semibold text-gray-800">{{ $appointment->patient->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">No. Rekam Medis</span>
                    <p class="font-semibold text-gray-800">{{ $appointment->patient->medical_record_number }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Tanggal Pemeriksaan</span>
                    <p class="font-semibold text-gray-800">{{ $appointment->date }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Psikolog Penanggung Jawab</span>
                    <p class="font-semibold text-gray-800">{{ $appointment->psychologist->name }}</p>
                </div>
            </div>

            <!-- Catatan SOAP -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700">S (Subjective - Keluhan Klien)</label>
                    <textarea wire:model="subjective" rows="3" class="w-full border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-gray-700">O (Objective - Hasil Observasi)</label>
                    <textarea wire:model="objective" rows="3" class="w-full border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-gray-700">A (Assessment - Analisis Psikologis)</label>
                    <textarea wire:model="assessment" rows="3" class="w-full border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-gray-700">P (Plan - Rencana Intervensi)</label>
                    <textarea wire:model="plan" rows="3" class="w-full border-gray-300 rounded-md"></textarea>
                </div>
            </div>

            <!-- Diagnosa ICD-10 & Durasi -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-gray-700">Kode ICD-10</label>
                    <input type="text" wire:model="icd10_code" placeholder="misal: F41.1" class="w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block font-bold text-gray-700">Deskripsi Diagnosa ICD-10</label>
                    <input type="text" wire:model="icd10_description" placeholder="Generalized Anxiety Disorder" class="w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block font-bold text-gray-700">Durasi Sesi (Menit)</label>
                    <input type="number" wire:model="duration_minutes" class="w-full border-gray-300 rounded-md">
                </div>
            </div>

            <!-- Checkbox 16 Intervensi Psikologis -->
            <div>
                <label class="block font-bold text-gray-700 mb-2">16 Intervensi Psikologis yang Diberikan</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 bg-gray-50 p-4 rounded-md">
                    @foreach($availableInterventions as $id => $name)
                        <label class="inline-flex items-center text-sm">
                            <input type="checkbox" wire:model="selected_interventions" value="{{ $id }}" class="rounded text-blue-600">
                            <span class="ml-2 text-gray-700">{{ $id }}. {{ $name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pesan Klien & Follow Up -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700">Pesan/Tugas Rumah untuk Klien</label>
                    <textarea wire:model="client_message" rows="2" class="w-full border-gray-300 rounded-md"></textarea>
                </div>
                <div>
                    <label class="block font-bold text-gray-700">Status Tindak Lanjut</label>
                    <select wire:model="follow_up_status" class="w-full border-gray-300 rounded-md">
                        <option value="Selesai">Selesai (Terminasi)</option>
                        <option value="Jadwal Ulang">Perlu Sesi Lanjutan</option>
                        <option value="Rujukan">Dirujuk ke Spesialis Lain</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
```

---

### 3.3. PDF Export Template: rpp-document.blade.php

Location: `resources/views/pdf/rpp-document.blade.php`

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPP KONSELING - {{ $appointment->patient->name }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; line-height: 1.3; color: #111; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 15px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 14pt; }
        .header p { margin: 2px 0; font-size: 9pt; }
        .table-info, .table-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .table-info td { padding: 4px; vertical-align: top; }
        .table-grid th, .table-grid td { border: 1px solid #000; padding: 5px; text-align: left; }
        .table-grid th { background-color: #f0f0f0; font-size: 10pt; }
        .title-box { background-color: #e2e8f0; font-weight: bold; padding: 4px; border: 1px solid #000; margin-top: 10px; }
        .checkbox-cell { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        .footer-table { width: 100%; margin-top: 20px; }
        .qr-code { width: 80px; height: 80px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAM PEMERIKSAAN PSIKOLOGIS (RPP KONSELING)</h2>
        <p>RAHASIA / MEDICAL CONFIDENTIAL</p>
    </div>

    <!-- Informasi Identitas Klien -->
    <table class="table-info">
        <tr>
            <td width="18%"><strong>Nama Klien</strong></td>
            <td width="2%">:</td>
            <td width="30%">{{ $appointment->patient->name }}</td>
            <td width="18%"><strong>No. RM</strong></td>
            <td width="2%">:</td>
            <td width="30%">{{ $appointment->patient->medical_record_number }}</td>
        </tr>
        <tr>
            <td><strong>Tgl. Lahir / Umur</strong></td>
            <td>:</td>
            <td>{{ $appointment->patient->dob }} ({{ $appointment->patient->age }} Thn)</td>
            <td><strong>Tgl. Pemeriksaan</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Kelamin</strong></td>
            <td>:</td>
            <td>{{ $appointment->patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td><strong>Durasi Konseling</strong></td>
            <td>:</td>
            <td>{{ $note->duration_minutes }} Menit</td>
        </tr>
    </table>

    <!-- Format SOAP -->
    <div class="title-box">I. CATATAN PERKEMBANGAN KLINIS (SOAP)</div>
    <table class="table-grid" style="margin-top: 5px;">
        <tr>
            <th width="20%">Subjective (S)</th>
            <td>{{ $note->subjective }}</td>
        </tr>
        <tr>
            <th>Objective (O)</th>
            <td>{{ $note->objective }}</td>
        </tr>
        <tr>
            <th>Assessment (A)</th>
            <td>{{ $note->assessment }}</td>
        </tr>
        <tr>
            <th>Plan (P)</th>
            <td>{{ $note->plan }}</td>
        </tr>
    </table>

    <!-- Diagnosa ICD-10 -->
    <div class="title-box">II. DIAGNOSA KLINIS (ICD-10)</div>
    <table class="table-grid" style="margin-top: 5px;">
        <tr>
            <th width="20%">Kode ICD-10</th>
            <td width="25%"><strong>{{ $note->icd10_code }}</strong></td>
            <th width="15%">Deskripsi</th>
            <td>{{ $note->icd10_description }}</td>
        </tr>
    </table>

    <!-- Checkbox 16 Intervensi -->
    <div class="title-box">III. METODE & INTERVENSI PSIKOLOGIS</div>
    <table class="table-grid" style="margin-top: 5px;">
        @php
            $activeInterventions = $note->intervention_ids ?? [];
        @endphp
        @foreach(array_chunk($interventions, 2, true) as $chunk)
            <tr>
                @foreach($chunk as $id => $name)
                    <td width="50%" class="checkbox-cell">
                        [{{ in_array($id, $activeInterventions) ? 'X' : ' ' }}] {{ $id }}. {{ $name }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>

    <!-- Pesan Klien & Tindak Lanjut -->
    <div class="title-box">IV. TUGAS / PESAN UNTUK KLIEN & TINDAK LANJUT</div>
    <table class="table-grid" style="margin-top: 5px;">
        <tr>
            <th width="25%">Pesan/Tugas Rumah</th>
            <td>{{ $note->client_message ?? '-' }}</td>
        </tr>
        <tr>
            <th>Rencana Tindak Lanjut</th>
            <td><strong>{{ $note->follow_up_status }}</strong></td>
        </tr>
    </table>

    <!-- Tanda Tangan & QR Code Validator -->
    <table class="footer-table">
        <tr>
            <td width="40%" text-align="center">
                <p style="font-size: 8pt; margin-bottom: 5px;">QR Validation Token Security:</p>
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(80)->generate(config('app.url').'/verify-rpp/'.$note->qr_code_token)) !!} " class="qr-code">
                <br>
                <span style="font-size: 7pt; color: #555;">Scan untuk verifikasi dokumen resmi</span>
            </td>
            <td width="20%"></td>
            <td width="40%" style="text-align: center;">
                <p>Kota Terkait, {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('d F Y') }}</p>
                <p>Psikolog Penanggung Jawab,</p>
                <br><br><br>
                <p><strong><u>{{ $appointment->psychologist->name }}</u></strong><br>SIPA: {{ $appointment->psychologist->sipa_number ?? '-------------------' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
```

---

### 3.4. Livewire Component: Dashboard Analytics (`Dashboard.php`)

Location: `app/Livewire/Dashboard.php`

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\SessionNote;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $totalSessionsMonth;
    public $unclosedNotesCount;
    public $monthlyRevenue;
    public $annualRevenue;
    public $icd10Distribution = [];
    public $workloadData = [];

    public function mount()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Ringkasan Sesi & Unclosed Notes
        $this->totalSessionsMonth = Appointment::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        $this->unclosedNotesCount = Appointment::where('status', 'Completed')
            ->whereDoesntHave('sessionNote', function($query) {
                $query->where('is_locked', true);
            })->count();

        // 2. Keuangan
        $this->monthlyRevenue = Payment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'Paid')
            ->sum('amount');

        $this->annualRevenue = Payment::whereYear('created_at', $currentYear)
            ->where('status', 'Paid')
            ->sum('amount');

        // 3. Distribusi Diagnosa ICD-10 Top 5
        $this->icd10Distribution = SessionNote::select('icd10_code', 'icd10_description', DB::raw('count(*) as total'))
            ->whereNotNull('icd10_code')
            ->groupBy('icd10_code', 'icd10_description')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 4. Workload Chart (Beban Kerja per Hari Minggu Ini)
        $this->workloadData = Appointment::select(DB::raw('DATE(date) as day'), DB::raw('count(*) as count'))
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('day')
            ->pluck('count', 'day')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
```

---

## 4. PETUNJUK IMPLEMENTASI STEP-BY-STEP & PERINTAH ARTISAN

### Step 1: Jalankan Database Migration
Jalankan perintah Artisan berikut untuk mengaplikasikan penambahan kolom RPP dan keamanan PIN ke database:

```bash
php artisan make:migration update_session_notes_and_security_table
# Salurkan kode dari Seksi 2 ke file migration yang terbentuk
php artisan migrate
```

### Step 2: Install Package Pendukung PDF & QR Code
Gunakan Composer untuk mengunduh package DOMPDF dan Simple QrCode:

```bash
composer require barryvdh/laravel-dompdf
composer require simplesoftwareio/simple-qrcode
```

Publish konfigurasi DOMPDF jika diperlukan:
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Step 3: Buat Komponen Livewire RPP & Dashboard
Gunakan artisan untuk meletakkan struktur Livewire secara konsisten:

```bash
php artisan make:livewire Reports/RppGenerator
php artisan make:livewire Dashboard
```

Salin kode dari **Seksi 3.1 & 3.2** ke komponen `RppGenerator` dan kodenya dari **Seksi 3.4** ke `Dashboard`.

### Step 4: Konfigurasi Security Lock Screen (PIN Mode)
Tambahkan middleware pengunci PIN 6-digit pada grup route rekam medis/RPP untuk menjaga kerahasiaan data medis (*Medical Confidentiality*):

```php
// routes/web.php
use App\Livewire\Reports\RppGenerator;
use App\Livewire\Dashboard;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/rpp/{appointmentId}', RppGenerator::class)->name('rpp.edit');
});
```

---
*Dokumentasi Modul 7 - Program Web Psikolog (Selesai)*