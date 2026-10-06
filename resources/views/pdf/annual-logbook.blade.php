<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LOGBOOK PSIKOLOG KLINIS TAHUN {{ $year }} - {{ $user?->name ?? 'Psikolog' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #111;
        }
        .header-title {
            text-align: center;
            margin-bottom: 18px;
        }
        .header-title h2 {
            font-size: 13pt;
            margin: 0;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-title h3 {
            font-size: 11pt;
            margin: 3px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-title h4 {
            font-size: 9.5pt;
            margin: 2px 0 0 0;
            font-weight: normal;
            color: #222;
        }
        .table-matrix {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-matrix th, .table-matrix td {
            border: 1px solid #222;
            padding: 4px 3px;
        }
        .table-matrix th {
            background-color: #f1f5f9;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
        }
        .table-matrix td.center {
            text-align: center;
        }
        .table-matrix td.right {
            text-align: right;
        }
        .table-matrix td.bold {
            font-weight: bold;
        }
        .category-row {
            background-color: #e2e8f0;
            font-weight: bold;
            font-size: 8.5pt;
        }
        .total-row {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .footer-table td {
            vertical-align: top;
        }
        .sig-block {
            text-align: center;
            width: 280px;
            float: right;
        }
    </style>
</head>
<body>

    <div class="header-title">
        <h2>LOGBOOK PSIKOLOG KLINIS</h2>
        <h3>TEMPAT PRAKTIK: {{ strtoupper($user?->practice_name ?: 'MANDIRI') }}</h3>
        <h4>PERIODE: JANUARI – DESEMBER {{ $year }}</h4>
    </div>

    <table class="table-matrix">
        <thead>
            <tr>
                <th rowspan="2" width="30">No</th>
                <th rowspan="2" style="text-align: left; padding-left: 6px;">Kegiatan</th>
                <th rowspan="2" width="45">Nilai SKP</th>
                <th colspan="12">Bulan</th>
                <th rowspan="2" width="40">Jumlah</th>
                <th rowspan="2" width="55">Jumlah SKP</th>
            </tr>
            <tr>
                <th width="24">1</th>
                <th width="24">2</th>
                <th width="24">3</th>
                <th width="24">4</th>
                <th width="24">5</th>
                <th width="24">6</th>
                <th width="24">7</th>
                <th width="24">8</th>
                <th width="24">9</th>
                <th width="24">10</th>
                <th width="24">11</th>
                <th width="24">12</th>
            </tr>
        </thead>
        <tbody>
            <tr class="category-row">
                <td class="center">I</td>
                <td colspan="15" style="padding-left: 6px; text-transform: uppercase;">Pelayanan Psikologi Klinis</td>
            </tr>

            @foreach($matrix as $key => $row)
                <tr>
                    <td class="center {{ $row['is_sub'] ? '' : 'bold' }}">{{ $row['number'] }}</td>
                    <td style="padding-left: {{ $row['is_sub'] ? '18px' : '6px' }}; {{ $row['is_sub'] ? '' : 'font-weight: 500;' }}">
                        {{ $row['title'] }}
                    </td>
                    <td class="center">{{ number_format($row['skp_weight'], 2) }}</td>
                    @for($m = 1; $m <= 12; $m++)
                        <td class="center {{ $row['months'][$m] > 0 ? 'bold' : '' }}">
                            {{ $row['months'][$m] > 0 ? $row['months'][$m] : 0 }}
                        </td>
                    @endfor
                    <td class="center bold">{{ $row['total_count'] }}</td>
                    <td class="center bold">{{ number_format($row['total_skp'], 2) }}</td>
                </tr>
            @endforeach

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="2" class="center bold" style="letter-spacing: 1px;">JUMLAH</td>
                <td class="center">-</td>
                @for($m = 1; $m <= 12; $m++)
                    <td class="center bold">{{ $monthlyTotals[$m] }}</td>
                @endfor
                <td class="center bold" style="font-size: 9pt;">{{ $grandTotalCount }}</td>
                <td class="center bold" style="font-size: 9pt;">{{ number_format($grandTotalSkp, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td width="60%">
                <div style="font-size: 7.5pt; color: #555; line-height: 1.4;">
                    <strong>Catatan:</strong><br>
                    • Format logbook disusun berdasarkan Pedoman Satuan Kredit Profesi (SKP) Ikatan Psikolog Klinis (IPK) Indonesia.<br>
                    • Akumulasi pelayanan klinis dihitung dari rekam medis sesi konseling yang telah dilaksanakan.<br>
                    • Dokumen dicetak secara resmi melalui Sistem Informasi Rekam Medis Web Psikolog.
                </div>
            </td>
            <td width="40%">
                <div class="sig-block">
                    <p style="margin: 0 0 4px 0;">{{ $user?->practice_city ?: 'Selatpanjang' }}, 31 Desember {{ $year }}</p>
                    <p style="margin: 0 0 50px 0;">Psikolog Klinis,</p>
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                        {{ $user?->formatted_name ?? ($user?->name ?? 'Psikolog Klinis') }}
                    </p>
                    <p style="margin: 2px 0 0 0; font-size: 8pt;">
                        SIPA: {{ $user?->sipa_number ?? '-' }}
                    </p>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>

