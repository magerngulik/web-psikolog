<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/10 border border-teal-500/30 text-teal-300">
                    SKP IPK Indonesia
                </span>
                <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">Logbook Tahunan Pelayanan Klinis</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">
                Rekapitulasi beban kerja pelayanan klinis & perhitungan Satuan Kredit Profesi (SKP)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Year Selector -->
            <div class="flex items-center gap-2 bg-spruce-900/80 border border-teal-500/20 px-3.5 py-2 rounded-xl text-xs sm:text-sm">
                <span class="text-xs text-slate-400 font-medium uppercase font-mono">Tahun:</span>
                <select wire:model.live="selectedYear" aria-label="Pilih Tahun Logbook" class="bg-transparent text-teal-300 font-bold focus:outline-none cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" class="bg-spruce-950 text-white">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Add Adjustment Button -->
            <button wire:click="openAdjustmentModal" type="button" class="px-3.5 py-2 bg-spruce-900/80 hover:bg-spruce-800 border border-teal-500/20 text-slate-200 font-medium rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-sm">
                <span>➕</span> <span>Penyesuaian Manual</span>
            </button>

            <!-- Export PDF Button -->
            <button wire:click="exportPdf" type="button" class="px-4 py-2 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-lg shadow-rose-950/50 transform hover:-translate-y-0.5">
                <span>📄</span> <span>Unduh PDF Logbook</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 rounded-2xl text-xs sm:text-sm font-medium flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Profile & Practice Context Card -->
    <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-2xl text-teal-300 shrink-0 shadow-sm">
                🏛️
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-serif text-lg font-semibold text-white">
                        {{ $logbookData['user']?->formatted_name ?? ($logbookData['user']?->name ?? 'Psikolog Klinis') }}
                    </h2>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-300 font-mono">
                        SIPA: {{ $logbookData['user']?->sipa_number ?? '-' }}
                    </span>
                </div>
                <p class="text-xs text-slate-300/80 mt-1">
                    Tempat Praktik: <strong class="text-white">{{ $logbookData['user']?->practice_name ?? 'MANDIRI' }}</strong> 
                    • Kota: <strong class="text-white">{{ $logbookData['user']?->practice_city ?? '-' }}</strong>
                    @if($logbookData['user']?->practice_address)
                        • {{ $logbookData['user']->practice_address }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('settings.profile') }}" class="text-xs text-teal-300 hover:text-teal-200 underline font-medium self-start md:self-center">
            ⚙️ Edit Profil Praktik
        </a>
    </div>

    <!-- Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Total Frekuensi Pelayanan</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-semibold text-white">{{ number_format($logbookData['grandTotalCount']) }}</span>
                <span class="text-xs text-slate-400">kegiatan di {{ $selectedYear }}</span>
            </div>
        </div>

        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-400/30 bg-gradient-to-br from-spruce-900/90 to-teal-950/40 flex flex-col justify-between shadow-lg shadow-teal-950/40">
            <span class="text-xs font-semibold text-teal-300 uppercase tracking-wider">Total Perolehan SKP</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-bold text-teal-300">{{ number_format($logbookData['grandTotalSkp'], 2) }}</span>
                <span class="text-xs text-teal-200/80 font-mono">SKP IPK</span>
            </div>
        </div>

        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Sesi Tatap Muka (Item 1)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-semibold text-emerald-300">{{ $logbookData['matrix']['individual']['total_count'] ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-mono">({{ number_format($logbookData['matrix']['individual']['total_skp'] ?? 0, 2) }} SKP)</span>
            </div>
        </div>

        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Laporan RPP (Item 7)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-semibold text-amber-300">{{ $logbookData['matrix']['report']['total_count'] ?? 0 }}</span>
                <span class="text-xs text-slate-400 font-mono">({{ number_format($logbookData['matrix']['report']['total_skp'] ?? 0, 2) }} SKP)</span>
            </div>
        </div>
    </div>

    <!-- The 16-Column Matrix Table -->
    <div class="sanctuary-glass-card rounded-2xl shadow-xl overflow-hidden border border-teal-500/15">
        <div class="p-4 border-b border-teal-500/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-serif text-lg font-semibold text-white flex items-center gap-2">
                    <span>📊</span> Tabel Matriks Logbook Pelayanan Psikolog Klinis — Periode {{ $selectedYear }}
                </h2>
                <p class="text-xs text-slate-400">Berdasarkan acuan Formulir Logbook Satuan Kredit Profesi (SKP) IPK Indonesia</p>
            </div>
            <span class="text-[11px] font-mono text-teal-300 bg-spruce-950 border border-teal-500/20 px-3 py-1 rounded-xl">
                Format Resmi 12 Bulan (Jan - Des)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse min-w-[980px]">
                <thead>
                    <tr class="bg-spruce-950/90 text-teal-300/80 font-mono font-semibold border-b border-teal-500/15 text-center text-[11px]">
                        <th scope="col" class="p-2.5 w-10 border-r border-teal-500/10">No</th>
                        <th scope="col" class="p-2.5 text-left border-r border-teal-500/10 min-w-[280px] sticky left-0 bg-spruce-950 z-10 shadow-sm font-sans">Kegiatan Pelayanan Psikologi Klinis</th>
                        <th scope="col" class="p-2.5 w-16 border-r border-teal-500/10">SKP</th>
                        @php
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        @endphp
                        @foreach($months as $idx => $mName)
                            <th scope="col" class="p-2 w-11 border-r border-teal-500/10">
                                {{ $idx + 1 }}<br><span class="text-[10px] text-slate-400 font-normal">{{ $mName }}</span>
                            </th>
                        @endforeach
                        <th scope="col" class="p-2.5 w-14 border-r border-teal-500/10 bg-spruce-900/80 font-bold text-white">Jml</th>
                        <th scope="col" class="p-2.5 w-16 bg-teal-950/50 text-teal-300 font-bold">Jml SKP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10">
                    <tr class="bg-teal-500/10 font-bold text-teal-300">
                        <td class="p-2 text-center border-r border-teal-500/10">I</td>
                        <td colspan="16" class="p-2 uppercase tracking-wide text-teal-300 text-[11px] font-mono">
                            Pelayanan Psikologi Klinis
                        </td>
                    </tr>

                    @foreach($logbookData['matrix'] as $key => $row)
                        <tr class="hover:bg-teal-500/[0.04] transition {{ $row['is_sub'] ? 'text-slate-300' : 'text-slate-200' }}">
                            <td class="p-2.5 text-center font-mono border-r border-teal-500/10 text-slate-400 {{ $row['is_sub'] ? 'text-[11px]' : 'font-semibold' }}">
                                {{ $row['number'] }}
                            </td>
                            <td class="p-2.5 border-r border-teal-500/10 {{ $row['is_sub'] ? 'pl-6 text-slate-300' : 'font-medium' }} sticky left-0 bg-[#0d171d] z-10 font-sans">
                                {{ $row['title'] }}
                            </td>
                            <td class="p-2.5 text-center font-mono border-r border-teal-500/10 text-slate-400">
                                {{ number_format($row['skp_weight'], 2) }}
                            </td>
                            @for($m = 1; $m <= 12; $m++)
                                <td class="p-2 text-center font-mono border-r border-teal-500/10 {{ $row['months'][$m] > 0 ? 'font-bold text-teal-300 bg-teal-500/10' : 'text-slate-600' }}">
                                    {{ $row['months'][$m] > 0 ? $row['months'][$m] : 0 }}
                                </td>
                            @endfor
                            <td class="p-2.5 text-center font-mono font-bold border-r border-teal-500/10 bg-spruce-900/60 text-white">
                                {{ $row['total_count'] }}
                            </td>
                            <td class="p-2.5 text-center font-mono font-bold bg-teal-950/30 text-teal-300">
                                {{ number_format($row['total_skp'], 2) }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Grand Total Row -->
                    <tr class="bg-spruce-950 text-white font-bold border-t-2 border-teal-500/30 text-center">
                        <td colspan="2" class="p-3 text-right uppercase tracking-wider border-r border-teal-500/10 text-teal-300 font-mono text-xs">
                            Jumlah Total
                        </td>
                        <td class="p-3 border-r border-teal-500/10 text-slate-500">-</td>
                        @for($m = 1; $m <= 12; $m++)
                            <td class="p-2.5 font-mono border-r border-teal-500/10 text-teal-300">
                                {{ $logbookData['monthlyTotals'][$m] }}
                            </td>
                        @endfor
                        <td class="p-3 font-mono border-r border-teal-500/10 bg-spruce-900 text-white text-sm">
                            {{ $logbookData['grandTotalCount'] }}
                        </td>
                        <td class="p-3 font-mono bg-teal-500/20 text-teal-300 text-sm font-extrabold">
                            {{ number_format($logbookData['grandTotalSkp'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Adjustments Section -->
    <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-teal-500/15">
        <div class="flex items-center justify-between border-b border-teal-500/10 pb-3">
            <div>
                <h3 class="font-serif text-lg font-semibold text-white flex items-center gap-2">
                    <span>✏️</span> Daftar Penyesuaian Manual (Tahun {{ $selectedYear }})
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Digunakan untuk menambahkan kegiatan offline/eksternal yang tidak dicatat per sesi (misal: visum pengadilan, tugas bencana/risiko tinggi)</p>
            </div>
            <button wire:click="openAdjustmentModal" class="px-3.5 py-1.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold rounded-xl text-xs transition shadow-sm">
                + Tambah Data
            </button>
        </div>

        @if(count($logbookData['adjustments']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-spruce-950/80 text-teal-300/80 font-mono border-b border-teal-500/15">
                            <th class="p-2.5">Bulan</th>
                            <th class="p-2.5">Kegiatan</th>
                            <th class="p-2.5 text-center">Jumlah Frekuensi</th>
                            <th class="p-2.5">Keterangan / Catatan</th>
                            <th class="p-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-teal-500/10">
                        @foreach($logbookData['adjustments'] as $adj)
                            @php
                                $actDef = $activities[$adj->activity_key] ?? null;
                            @endphp
                            <tr class="hover:bg-teal-500/[0.04]">
                                <td class="p-2.5 font-bold text-teal-300 font-mono">
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
            <p class="text-xs text-slate-400 py-2">
                Belum ada penyesuaian manual di tahun {{ $selectedYear }}. Semua data dihitung murni dari sesi konseling yang tersimpan.
            </p>
        @endif
    </div>

    <!-- Modal Form Penyesuaian Manual -->
    @if($showAdjustmentModal)
        <div x-data @keydown.escape.window="$wire.closeAdjustmentModal()" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md" role="dialog" aria-modal="true" aria-labelledby="modal-adj-title">
            <div class="sanctuary-glass-card border border-teal-500/30 rounded-3xl w-full max-w-lg p-6 sm:p-7 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-teal-500/15 pb-3">
                    <h3 id="modal-adj-title" class="font-serif text-lg font-semibold text-white">
                        {{ $editing_adjustment_id ? 'Edit' : 'Tambah' }} Penyesuaian Logbook (Tahun {{ $selectedYear }})
                    </h3>
                    <button wire:click="closeAdjustmentModal" type="button" aria-label="Tutup jendela penyesuaian" class="text-slate-400 hover:text-white text-lg font-bold p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveAdjustment" class="space-y-4">
                    <div>
                        <label for="adj_activity_key" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Item Kegiatan Pelayanan *</label>
                        <select id="adj_activity_key" wire:model="adj_activity_key" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            @foreach($activities as $key => $act)
                                <option value="{{ $key }}">{{ $act['number'] }}. {{ $act['title'] }} ({{ $act['skp_weight'] }} SKP)</option>
                            @endforeach
                        </select>
                        @error('adj_activity_key') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="adj_month" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Bulan Pelaksanaan *</label>
                            <select id="adj_month" wire:model="adj_month" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">Bulan {{ $m }} - {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                            @error('adj_month') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="adj_count" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jumlah Frekuensi *</label>
                            <input id="adj_count" wire:model="adj_count" type="number" min="0" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            @error('adj_count') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="adj_notes" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Catatan / Keterangan Penyelenggaraan</label>
                        <textarea id="adj_notes" wire:model="adj_notes" rows="2" placeholder="Contoh: Saksi ahli di PN Selatpanjang kasus pidana anak No. 12/Pid.Sus/2026" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"></textarea>
                        @error('adj_notes') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-teal-500/15">
                        <button wire:click="closeAdjustmentModal" type="button" class="px-4 py-2 bg-spruce-900 hover:bg-spruce-800 text-slate-300 font-semibold rounded-xl text-xs transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl text-xs transition focus:outline-none focus:ring-2 focus:ring-teal-400 shadow-md">
                            Simpan Penyesuaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
