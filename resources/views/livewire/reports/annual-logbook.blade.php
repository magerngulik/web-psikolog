<div class="p-6 space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-teal-500/10 border border-teal-500/30 text-teal-400">
                    SKP IPK Indonesia
                </span>
                <h1 class="text-2xl font-bold text-white">Logbook Tahunan Psikolog Klinis</h1>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Rekapitulasi beban kerja pelayanan klinis & perhitungan Satuan Kredit Profesi (SKP)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Year Selector -->
            <div class="flex items-center gap-2 bg-slate-800 border border-slate-700 px-3 py-1.5 rounded-xl">
                <span class="text-xs text-slate-400 font-semibold uppercase">Tahun:</span>
                <select wire:model.live="selectedYear" class="bg-transparent text-teal-400 font-bold text-sm focus:outline-none cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" class="bg-slate-900 text-white">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Add Adjustment Button -->
            <button wire:click="openAdjustmentModal" type="button" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-medium rounded-xl transition text-sm flex items-center gap-1.5">
                <span>➕</span> <span>Penyesuaian Manual</span>
            </button>

            <!-- Export PDF Button -->
            <button wire:click="exportPdf" type="button" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-xl transition text-sm flex items-center gap-1.5 shadow-lg shadow-rose-600/20">
                <span>📄</span> <span>Unduh PDF Logbook</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Profile & Practice Context Card -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-2xl text-teal-400 shrink-0">
                🏛️
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-white">
                        {{ $logbookData['user']?->formatted_name ?? ($logbookData['user']?->name ?? 'Psikolog Klinis') }}
                    </h3>
                    <span class="text-xs px-2 py-0.5 rounded bg-slate-700 text-slate-300 font-mono">
                        SIPA: {{ $logbookData['user']?->sipa_number ?? '-' }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Tempat Praktik: <strong class="text-slate-300">{{ $logbookData['user']?->practice_name ?? 'MANDIRI' }}</strong> 
                    • Kota: <strong class="text-slate-300">{{ $logbookData['user']?->practice_city ?? '-' }}</strong>
                    @if($logbookData['user']?->practice_address)
                        • {{ $logbookData['user']->practice_address }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('settings.profile') }}" class="text-xs text-teal-400 hover:text-teal-300 underline font-medium self-start md:self-center">
            ⚙️ Edit Profil Praktik
        </a>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Frekuensi Pelayanan</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-white">{{ number_format($logbookData['grandTotalCount']) }}</span>
                <span class="text-xs text-slate-400">kegiatan di {{ $selectedYear }}</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-teal-500/30 rounded-2xl p-5 shadow bg-gradient-to-br from-slate-800 to-teal-950/20">
            <span class="text-xs font-semibold text-teal-400 uppercase tracking-wider">Total Perolehan SKP</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-teal-400">{{ number_format($logbookData['grandTotalSkp'], 2) }}</span>
                <span class="text-xs text-teal-200/70">SKP IPK</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sesi Tatap Muka (Item 1)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-cyan-400">{{ $logbookData['matrix']['individual']['total_count'] ?? 0 }}</span>
                <span class="text-xs text-slate-400">({{ number_format($logbookData['matrix']['individual']['total_skp'] ?? 0, 2) }} SKP)</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Laporan RPP (Item 7)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="text-3xl font-extrabold text-amber-400">{{ $logbookData['matrix']['report']['total_count'] ?? 0 }}</span>
                <span class="text-xs text-slate-400">({{ number_format($logbookData['matrix']['report']['total_skp'] ?? 0, 2) }} SKP)</span>
            </div>
        </div>
    </div>

    <!-- The 16-Column Matrix Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span>📊</span> Tabel Matriks Logbook Pelayanan Psikolog Klinis — Periode {{ $selectedYear }}
                </h2>
                <p class="text-xs text-slate-400">Berdasarkan acuan Formulir Logbook Satuan Kredit Profesi (SKP) IPK Indonesia</p>
            </div>
            <span class="text-xs text-slate-400 bg-slate-900 border border-slate-700 px-3 py-1 rounded-xl">
                Format Resmi 12 Bulan (Jan - Des)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse min-w-[980px]">
                <thead>
                    <tr class="bg-slate-900/90 text-slate-300 font-semibold border-b border-slate-700 text-center">
                        <th class="p-2.5 w-10 border-r border-slate-700">No</th>
                        <th class="p-2.5 text-left border-r border-slate-700 min-w-[280px]">Kegiatan Pelayanan Psikologi Klinis</th>
                        <th class="p-2.5 w-16 border-r border-slate-700">Nilai SKP</th>
                        @php
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        @endphp
                        @foreach($months as $idx => $mName)
                            <th class="p-2 w-11 border-r border-slate-700 text-[11px]">
                                {{ $idx + 1 }}<br><span class="text-[9px] text-slate-500 font-normal">{{ $mName }}</span>
                            </th>
                        @endforeach
                        <th class="p-2.5 w-14 border-r border-slate-700 bg-slate-800/80 font-bold">Jml</th>
                        <th class="p-2.5 w-16 bg-teal-950/40 text-teal-300 font-bold">Jml SKP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60">
                    <tr class="bg-slate-900/40 font-bold text-slate-300">
                        <td class="p-2 text-center border-r border-slate-700">I</td>
                        <td colspan="15" class="p-2 uppercase tracking-wide text-teal-400 text-[11px]">
                            Pelayanan Psikologi Klinis
                        </td>
                    </tr>

                    @foreach($logbookData['matrix'] as $key => $row)
                        <tr class="hover:bg-slate-700/30 transition {{ $row['is_sub'] ? 'text-slate-300' : 'text-slate-200' }}">
                            <td class="p-2.5 text-center font-mono border-r border-slate-700 text-slate-400 {{ $row['is_sub'] ? 'text-[11px]' : 'font-semibold' }}">
                                {{ $row['number'] }}
                            </td>
                            <td class="p-2.5 border-r border-slate-700 {{ $row['is_sub'] ? 'pl-6 text-slate-300' : 'font-medium' }}">
                                {{ $row['title'] }}
                            </td>
                            <td class="p-2.5 text-center font-mono border-r border-slate-700 text-slate-400">
                                {{ number_format($row['skp_weight'], 2) }}
                            </td>
                            @for($m = 1; $m <= 12; $m++)
                                <td class="p-2 text-center font-mono border-r border-slate-700 {{ $row['months'][$m] > 0 ? 'font-bold text-teal-300 bg-teal-900/10' : 'text-slate-600' }}">
                                    {{ $row['months'][$m] > 0 ? $row['months'][$m] : 0 }}
                                </td>
                            @endfor
                            <td class="p-2.5 text-center font-mono font-bold border-r border-slate-700 bg-slate-800/60 text-white">
                                {{ $row['total_count'] }}
                            </td>
                            <td class="p-2.5 text-center font-mono font-bold bg-teal-950/30 text-teal-300">
                                {{ number_format($row['total_skp'], 2) }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Grand Total Row -->
                    <tr class="bg-slate-900 text-white font-bold border-t-2 border-slate-600 text-center">
                        <td colspan="2" class="p-3 text-right uppercase tracking-wider border-r border-slate-700 text-slate-300">
                            Jumlah Total
                        </td>
                        <td class="p-3 border-r border-slate-700 text-slate-500">-</td>
                        @for($m = 1; $m <= 12; $m++)
                            <td class="p-2.5 font-mono border-r border-slate-700 text-teal-400">
                                {{ $logbookData['monthlyTotals'][$m] }}
                            </td>
                        @endfor
                        <td class="p-3 font-mono border-r border-slate-700 bg-slate-800 text-white text-sm">
                            {{ $logbookData['grandTotalCount'] }}
                        </td>
                        <td class="p-3 font-mono bg-teal-900/60 text-teal-300 text-sm font-extrabold">
                            {{ number_format($logbookData['grandTotalSkp'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Adjustments Section -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-700 pb-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>✏️</span> Daftar Penyesuaian Manual (Tahun {{ $selectedYear }})
                </h3>
                <p class="text-xs text-slate-400">Digunakan untuk menambahkan kegiatan offline/eksternal yang tidak dicatat per sesi (misal: visum pengadilan, tugas bencana/risiko tinggi)</p>
            </div>
            <button wire:click="openAdjustmentModal" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-500 text-slate-900 font-semibold rounded-xl text-xs transition">
                + Tambah Data
            </button>
        </div>

        @if(count($logbookData['adjustments']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900/70 text-slate-300 border-b border-slate-700">
                            <th class="p-2.5">Bulan</th>
                            <th class="p-2.5">Kegiatan</th>
                            <th class="p-2.5 text-center">Jumlah Frekuensi</th>
                            <th class="p-2.5">Keterangan / Catatan</th>
                            <th class="p-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @foreach($logbookData['adjustments'] as $adj)
                            @php
                                $actDef = $activities[$adj->activity_key] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-700/20">
                                <td class="p-2.5 font-bold text-teal-400 font-mono">
                                    Bulan {{ $adj->month }} ({{ \Carbon\Carbon::create(null, $adj->month, 1)->translatedFormat('F') }})
                                </td>
                                <td class="p-2.5 text-slate-200">
                                    {{ $actDef['title'] ?? $adj->activity_key }}
                                </td>
                                <td class="p-2.5 text-center font-mono font-bold text-white">
                                    +{{ $adj->adjustment_count }}
                                </td>
                                <td class="p-2.5 text-slate-400">
                                    {{ $adj->notes ?: '-' }}
                                </td>
                                <td class="p-2.5 text-right space-x-2">
                                    <button wire:click="openAdjustmentModal('{{ $adj->id }}')" class="text-teal-400 hover:underline">
                                        Edit
                                    </button>
                                    <button wire:click="deleteAdjustment('{{ $adj->id }}')" onclick="confirm('Hapus penyesuaian ini?') || event.stopImmediatePropagation()" class="text-rose-400 hover:underline">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-500 py-2">
                Belum ada penyesuaian manual di tahun {{ $selectedYear }}. Semua data dihitung murni dari sesi konseling yang tersimpan.
            </p>
        @endif
    </div>

    <!-- Modal Form Penyesuaian Manual -->
    @if($showAdjustmentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                    <h3 class="text-base font-bold text-white">
                        {{ $editing_adjustment_id ? 'Edit' : 'Tambah' }} Penyesuaian Logbook Manual (Tahun {{ $selectedYear }})
                    </h3>
                    <button wire:click="closeAdjustmentModal" class="text-slate-400 hover:text-white text-lg font-bold">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveAdjustment" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Item Kegiatan Pelayanan *</label>
                        <select wire:model="adj_activity_key" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
                            @foreach($activities as $key => $act)
                                <option value="{{ $key }}">{{ $act['number'] }}. {{ $act['title'] }} ({{ $act['skp_weight'] }} SKP)</option>
                            @endforeach
                        </select>
                        @error('adj_activity_key') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Bulan Pelaksanaan *</label>
                            <select wire:model="adj_month" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">Bulan {{ $m }} - {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                            @error('adj_month') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jumlah Frekuensi *</label>
                            <input wire:model="adj_count" type="number" min="0" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-teal-500">
                            @error('adj_count') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Catatan / Keterangan Penyelenggaraan</label>
                        <textarea wire:model="adj_notes" rows="2" placeholder="Contoh: Saksi ahli di PN Selatpanjang kasus pidana anak No. 12/Pid.Sus/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-teal-500"></textarea>
                        @error('adj_notes') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-700">
                        <button wire:click="closeAdjustmentModal" type="button" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 font-semibold rounded-xl text-xs transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-bold rounded-xl text-xs transition">
                            Simpan Penyesuaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

