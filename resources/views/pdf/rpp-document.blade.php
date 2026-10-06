<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPP KONSELING - {{ $appointment->patient?->name ?? 'Klien' }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9.5pt; line-height: 1.35; color: #111; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 12px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 13pt; letter-spacing: 0.5px; }
        .header p { margin: 2px 0; font-size: 8.5pt; font-weight: bold; color: #444; }
        .table-info, .table-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table-info td { padding: 3px 4px; vertical-align: top; }
        .table-grid th, .table-grid td { border: 1px solid #000; padding: 4.5px 6px; text-align: left; }
        .table-grid th { background-color: #f1f5f9; font-size: 9pt; }
        .title-box { background-color: #e2e8f0; font-weight: bold; padding: 4px 6px; border: 1px solid #000; margin-top: 8px; font-size: 9.5pt; }
        .checkbox-cell { font-size: 8.5pt; }
        .footer-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .qr-code { width: 70px; height: 70px; }
        .sub-label { font-weight: bold; color: #1e293b; display: block; margin-bottom: 2px; }
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
            <td width="30%">{{ $appointment->patient?->name ?? 'Klien' }}</td>
            <td width="18%"><strong>No. RM</strong></td>
            <td width="2%">:</td>
            <td width="30%">{{ $appointment->patient?->medical_record_number ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>NIK KTP</strong></td>
            <td>:</td>
            <td>{{ $appointment->patient?->nik ?? '-' }}</td>
            <td><strong>Tgl. Pemeriksaan</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tgl. Lahir / Umur</strong></td>
            <td>:</td>
            <td>{{ $appointment->patient?->dob ?? '-' }} ({{ $appointment->patient?->age ?? 0 }} Thn)</td>
            <td><strong>Jenis Kelamin</strong></td>
            <td>:</td>
            <td>{{ ($appointment->patient?->gender == 'L' || $appointment->patient?->gender == 'Laki-laki') ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td><strong>Pendidikan</strong></td>
            <td>:</td>
            <td>{{ $appointment->patient?->last_education ?? '-' }}</td>
            <td><strong>Urutan Lahir</strong></td>
            <td>:</td>
            <td>
                @if($appointment->patient?->birth_order && $appointment->patient?->total_siblings)
                    Anak ke-{{ $appointment->patient->birth_order }} dari {{ $appointment->patient->total_siblings }} bersaudara
                @elseif($appointment->patient?->birth_order)
                    Anak ke-{{ $appointment->patient->birth_order }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Status Difabel</strong></td>
            <td>:</td>
            <td>{{ $appointment->patient?->disabled_status ?? 'Tidak' }}</td>
            <td><strong>Durasi Konseling</strong></td>
            <td>:</td>
            <td>{{ $note->duration_minutes ?? 60 }} Menit</td>
        </tr>
    </table>

    <!-- Format SOAP -->
    <div class="title-box">I. CATATAN PERKEMBANGAN KLINIS (SOAP)</div>
    <table class="table-grid" style="margin-top: 4px;">
        <!-- Subjective: Keluhan & Masalah -->
        <tr>
            <th width="22%" style="vertical-align: top;">
                Subjective (S)
            </th>
            <td>
                <div style="margin-bottom: 4px;">
                    <span class="sub-label">• Keluhan Klien:</span>
                    {{ $note->subjective_complaint ?? ($note->subjective ?? '-') }}
                </div>
                @if(!empty($note->subjective_problem))
                    <div style="margin-top: 4px; border-top: 1px dashed #cbd5e1; padding-top: 4px;">
                        <span class="sub-label">• Masalah Klien:</span>
                        {{ $note->subjective_problem }}
                    </div>
                @endif
            </td>
        </tr>

        <!-- Objective: Dinamika Psikologis -->
        <tr>
            <th style="vertical-align: top;">
                Objective (O)<br>
                <small style="font-weight: normal; color: #555;">(Dinamika Psikologis)</small>
            </th>
            <td>
                {{ $note->objective ?? '-' }}
            </td>
        </tr>

        <!-- Assessment: Metode Checkbox + Free Text -->
        <tr>
            <th style="vertical-align: top;">
                Assessment (A)
            </th>
            <td>
                @php
                    $methods = $note->assessment_methods ?? [];
                    if (is_string($methods)) {
                        $methods = json_decode($methods, true) ?? [];
                    }
                @endphp
                @if(!empty($methods))
                    <div style="margin-bottom: 4px;">
                        <span class="sub-label">• Metode Asesmen:</span>
                        @foreach(['Paper & Pencil Test', 'Inventory', 'Observasi', 'Wawancara'] as $methodName)
                            <span style="display: inline-block; margin-right: 12px; font-size: 8.5pt;">
                                [{{ in_array($methodName, $methods) ? 'X' : ' ' }}] {{ $methodName }}
                            </span>
                        @endforeach
                    </div>
                @endif
                @if(!empty($note->assessment))
                    <div style="margin-top: 4px; border-top: 1px dashed #cbd5e1; padding-top: 4px;">
                        <span class="sub-label">• Analisis & Kesimpulan Asesmen:</span>
                        {{ $note->assessment }}
                    </div>
                @endif
                @if(empty($methods) && empty($note->assessment))
                    -
                @endif
            </td>
        </tr>

        <!-- Plan -->
        <tr>
            <th style="vertical-align: top;">Plan (P)</th>
            <td>{{ $note->plan ?? 'Dilanjutkan sesuai target terapi yang disepakati.' }}</td>
        </tr>
    </table>

    <!-- Diagnosa PPDGJ-III / ICD-10 -->
    <div class="title-box">II. DIAGNOSIS KLINIS (PPDGJ-III / ICD-10)</div>
    <table class="table-grid" style="margin-top: 4px;">
        <tr>
            <th width="22%">Kode Diagnosis</th>
            <td width="25%"><strong>{{ $note->icd10_code ?? '-' }}</strong></td>
            <th width="15%">Deskripsi</th>
            <td>{{ $note->icd10_description ?? '-' }}</td>
        </tr>
        @if(!empty($note->diagnosis_notes))
            <tr>
                <th style="vertical-align: top;">Diagnosis Kustom / Tambahan</th>
                <td colspan="3" style="background-color: #fafafa;">{{ $note->diagnosis_notes }}</td>
            </tr>
        @endif
    </table>

    <!-- Intervensi Psikologis -->
    <div class="title-box">III. METODE & INTERVENSI PSIKOLOGIS</div>
    <table class="table-grid" style="margin-top: 4px;">
        @php
            $activeInterventions = $note->intervention_ids ?? [];
            if (is_string($activeInterventions)) {
                $activeInterventions = json_decode($activeInterventions, true) ?? [];
            }
        @endphp
        @foreach(array_chunk($interventions, 2, true) as $chunk)
            <tr>
                @foreach($chunk as $id => $name)
                    <td width="50%" class="checkbox-cell">
                        [{{ in_array((string)$id, array_map('strval', $activeInterventions)) || in_array($id, $activeInterventions) ? 'X' : ' ' }}] {{ is_numeric($id) ? $id . '. ' : ($id ? "({$id}) " : '') }}{{ $name }}
                    </td>
                @endforeach
                @if(count($chunk) < 2)
                    <td width="50%" class="checkbox-cell"></td>
                @endif
            </tr>
        @endforeach

        @if(!empty($note->intervention_notes))
            <tr>
                <td colspan="2" style="background-color: #fafafa; padding: 6px;">
                    <span class="sub-label">• Catatan Khusus / Detail Intervensi:</span>
                    {{ $note->intervention_notes }}
                </td>
            </tr>
        @endif
    </table>

    <!-- Pesan Klien & Tindak Lanjut -->
    <div class="title-box">IV. TUGAS / PESAN UNTUK KLIEN & TINDAK LANJUT</div>
    <table class="table-grid" style="margin-top: 4px;">
        <tr>
            <th width="22%" style="vertical-align: top;">Pesan / Tugas Rumah</th>
            <td>{{ $note->client_message ?? '-' }}</td>
        </tr>
        <tr>
            <th>Rencana Tindak Lanjut</th>
            <td><strong>{{ $note->follow_up_status ?? 'Selesai' }}</strong></td>
        </tr>
    </table>

    <!-- Tanda Tangan & QR Code Validator -->
    <table class="footer-table">
        <tr>
            <td width="40%" style="text-align: center; vertical-align: top;">
                <p style="font-size: 8pt; margin-bottom: 4px;">QR Validation Token Security:</p>
                @php
                    $verifyUrl = config('app.url').'/verify-rpp/'.($note->qr_code_token ?? 'token');
                    try {
                        $qrBase64 = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(70)->generate($verifyUrl));
                    } catch (\Throwable $e) {
                        $qrBase64 = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate($verifyUrl));
                    }
                @endphp
                <img src="data:image/png;base64,{{ $qrBase64 }}" class="qr-code" alt="QR Code">
                <br>
                <span style="font-size: 7pt; color: #555;">Scan untuk verifikasi dokumen resmi</span>
            </td>
            <td width="20%"></td>
            <td width="40%" style="text-align: center; vertical-align: top;">
                <p>Jakarta, {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('d F Y') }}</p>
                <p>Psikolog Penanggung Jawab,</p>
                <br><br>
                <p><strong><u>{{ $appointment->psychologist?->name ?? 'Psikolog Penanggung Jawab' }}</u></strong><br>SIPA: {{ $appointment->psychologist?->sipa_number ?? '503/123-SIPA/2026' }}</p>
            </td>
        </tr>
    </table>

</body>
</html>
