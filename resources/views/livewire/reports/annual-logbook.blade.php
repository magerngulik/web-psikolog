<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-blush-100 border border-blush-200 text-blush-700">
                    SKP IPK Indonesia
                </span>
                <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight">Logbook Tahunan Pelayanan Klinis</h1>
            </div>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">
                Rekapitulasi beban kerja pelayanan klinis & perhitungan Satuan Kredit Profesi (SKP)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Year Selector -->
            <div class="flex items-center gap-2 bg-white border border-blush-200 px-3.5 py-2 rounded-xl text-xs sm:text-sm">
                <span class="text-xs text-ink-500 font-medium uppercase font-mono">Tahun:</span>
                <select wire:model.live="selectedYear" aria-label="Pilih Tahun Logbook" class="bg-transparent text-blush-700 font-bold focus:outline-none cursor-pointer">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" class="bg-blush-50 text-ink-900">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Add Adjustment Button -->
            <button wire:click="openAdjustmentModal" type="button" class="px-3.5 py-2 bg-white hover:bg-blush-100 border border-blush-200 text-ink-700 font-medium rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-sm">
                <span>➕</span> <span>Penyesuaian Manual</span>
            </button>

            <!-- Export PDF Button -->
            <button wire:click="exportPdf" type="button" class="px-4 py-2 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-lg shadow-blush-200/50 transform hover:-translate-y-0.5">
                <span>📄</span> <span>Unduh PDF Logbook</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-blush-100 border border-blush-200 text-blush-700 rounded-2xl text-xs sm:text-sm font-medium flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Profile & Practice Context Card -->
    <div class="sanctuary-glass-card rounded-2xl p-5 border border-blush-200 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blush-100 border border-blush-200 flex items-center justify-center text-2xl text-blush-700 shrink-0 shadow-sm">
                🏛️
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-serif text-lg font-semibold text-ink-900">
                        {{ $logbookData['user']?->formatted_name ?? ($logbookData['user']?->name ?? 'Psikolog Klinis') }}
                    </h2>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-blush-100 border border-blush-200 text-blush-700 font-mono">
                        SIPPK: {{ $logbookData['user']?->sipa_number ?? '-' }}
                    </span>
                </div>
                <p class="text-xs text-ink-700 mt-1">
                    Tempat Praktik: <strong class="text-ink-900">{{ $logbookData['user']?->practice_name ?? 'MANDIRI' }}</strong> 
                    • Kota: <strong class="text-ink-900">{{ $logbookData['user']?->practice_city ?? '-' }}</strong>
                    @if($logbookData['user']?->practice_address)
                        • {{ $logbookData['user']->practice_address }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('settings.profile') }}" class="text-xs text-blush-700 hover:text-blush-800 underline font-medium self-start md:self-center">
            ⚙️ Edit Profil Praktik
        </a>
    </div>

    <!-- Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="sanctuary-glass-card rounded-2xl p-5 border border-blush-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-ink-500 uppercase tracking-wider">Total Frekuensi Pelayanan</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-semibold text-ink-900">{{ number_format($logbookData['grandTotalCount']) }}</span>
                <span class="text-xs text-ink-500">kegiatan di {{ $selectedYear }}</span>
            </div>
        </div>

        <div class="pastel-card-blush rounded-2xl p-5 flex flex-col justify-between">
            <span class="text-xs font-semibold text-blush-700 uppercase tracking-wider">Total Perolehan SKP</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-bold text-blush-700">{{ number_format($logbookData['grandTotalSkp'], 2) }}</span>
                <span class="text-xs text-blush-700 font-mono">SKP IPK</span>
            </div>
        </div>

        <div class="sanctuary-glass-card rounded-2xl p-5 border border-blush-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-ink-500 uppercase tracking-wider">Sesi Tatap Muka (Item 1)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-semibold text-emerald-700">{{ $logbookData['matrix']['individual']['total_count'] ?? 0 }}</span>
                <span class="text-xs text-ink-500 font-mono">({{ number_format($logbookData['matrix']['individual']['total_skp'] ?? 0, 2) }} SKP)</span>
            </div>
        </div>

        <div class="sanctuary-glass-card rounded-2xl p-5 border border-blush-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-ink-500 uppercase tracking-wider">Laporan RPP (Item 7)</span>
            <div class="flex items-baseline gap-2 mt-2">
                <span class="font-serif text-3xl sm:text-4xl font-semibold text-amber-800">{{ $logbookData['matrix']['report']['total_count'] ?? 0 }}</span>
                <span class="text-xs text-ink-500 font-mono">({{ number_format($logbookData['matrix']['report']['total_skp'] ?? 0, 2) }} SKP)</span>
            </div>
        </div>
    </div>

    <!-- The 16-Column Matrix Table -->
    <div class="sanctuary-glass-card rounded-2xl shadow-xl overflow-hidden border border-blush-200">
        <div class="p-4 border-b border-blush-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="font-serif text-lg font-semibold text-ink-900 flex items-center gap-2">
                    <span>📊</span> Tabel Matriks Logbook Pelayanan Psikolog Klinis — Periode {{ $selectedYear }}
                </h2>
                <p class="text-xs text-ink-500">Berdasarkan acuan Formulir Logbook Satuan Kredit Profesi (SKP) IPK Indonesia</p>
            </div>
            <span class="text-[11px] font-mono text-blush-700 bg-blush-50 border border-blush-200 px-3 py-1 rounded-xl">
                Format Resmi 12 Bulan (Jan - Des)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse min-w-[980px]">
                <thead>
                    <tr class="bg-blush-50 text-blush-700 font-mono font-semibold border-b border-blush-200 text-center text-[11px]">
                        <th scope="col" class="p-2.5 w-10 border-r border-blush-200">No</th>
                        <th scope="col" class="p-2.5 text-left border-r border-blush-200 min-w-[280px] sticky left-0 bg-blush-50 z-10 shadow-sm font-sans">Kegiatan Pelayanan Psikologi Klinis</th>
                        <th scope="col" class="p-2.5 w-16 border-r border-blush-200">SKP</th>
                        @php
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        @endphp
                        @foreach($months as $idx => $mName)
                            <th scope="col" class="p-2 w-11 border-r border-blush-200">
                                {{ $idx + 1 }}<br><span class="text-[10px] text-ink-500 font-normal">{{ $mName }}</span>
                            </th>
                        @endforeach
                        <th scope="col" class="p-2.5 w-14 border-r border-blush-200 bg-white font-bold text-ink-900">Jml</th>
                        <th scope="col" class="p-2.5 w-16 bg-blush-50 text-blush-700 font-bold">Jml SKP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blush-100">
                    <tr class="bg-blush-100 font-bold text-blush-700">
                        <td class="p-2 text-center border-r border-blush-200">I</td>
                        <td colspan="16" class="p-2 uppercase tracking-wide text-blush-700 text-[11px] font-mono">
                            Pelayanan Psikologi Klinis
                        </td>
                    </tr>

                    @foreach($logbookData['matrix'] as $key => $row)
                        <tr class="hover:bg-blush-50/70 transition {{ $row['is_sub'] ? 'text-ink-700' : 'text-ink-900' }}">
                            <td class="p-2.5 text-center font-mono border-r border-blush-200 text-ink-500 {{ $row['is_sub'] ? 'text-[11px]' : 'font-semibold' }}">
                                {{ $row['number'] }}
                            </td>
                            <td class="p-2.5 border-r border-blush-200 {{ $row['is_sub'] ? 'pl-6 text-ink-700' : 'font-semibold text-ink-900' }} sticky left-0 bg-white z-10 font-sans">
                                {{ $row['title'] }}
                            </td>
                            <td class="p-2.5 text-center font-mono border-r border-blush-200 text-ink-500">
                                {{ number_format($row['skp_weight'], 2) }}
                            </td>
                            @for($m = 1; $m <= 12; $m++)
                                <td class="p-2 text-center font-mono border-r border-blush-200 {{ $row['months'][$m] > 0 ? 'font-bold text-blush-700 bg-blush-100' : 'text-slate-600' }}">
                                    {{ $row['months'][$m] > 0 ? $row['months'][$m] : 0 }}
                                </td>
                            @endfor
                            <td class="p-2.5 text-center font-mono font-bold border-r border-blush-200 bg-white text-ink-900">
                                {{ $row['total_count'] }}
                            </td>
                            <td class="p-2.5 text-center font-mono font-bold bg-blush-50 text-blush-700">
                                {{ number_format($row['total_skp'], 2) }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Grand Total Row -->
                    <tr class="bg-blush-50 text-ink-900 font-bold border-t-2 border-blush-200 text-center">
                        <td colspan="2" class="p-3 text-right uppercase tracking-wider border-r border-blush-200 text-blush-700 font-mono text-xs">
                            Jumlah Total
                        </td>
                        <td class="p-3 border-r border-blush-200 text-ink-500">-</td>
                        @for($m = 1; $m <= 12; $m++)
                            <td class="p-2.5 font-mono border-r border-blush-200 text-blush-700">
                                {{ $logbookData['monthlyTotals'][$m] }}
                            </td>
                        @endfor
                        <td class="p-3 font-mono border-r border-blush-200 bg-white text-ink-900 text-sm">
                            {{ $logbookData['grandTotalCount'] }}
                        </td>
                        <td class="p-3 font-mono bg-blush-100 text-blush-700 text-sm font-extrabold">
                            {{ number_format($logbookData['grandTotalSkp'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Adjustments Section -->
    <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-blush-200">
        <div class="flex items-center justify-between border-b border-blush-200 pb-3">
            <div>
                <h3 class="font-serif text-lg font-semibold text-ink-900 flex items-center gap-2">
                    <span>✏️</span> Daftar Penyesuaian Manual (Tahun {{ $selectedYear }})
                </h3>
                <p class="text-xs text-ink-500 mt-0.5">Digunakan untuk menambahkan kegiatan offline/eksternal yang tidak dicatat per sesi (misal: visum pengadilan, tugas bencana/risiko tinggi)</p>
            </div>
            <button wire:click="openAdjustmentModal" class="px-3.5 py-1.5 bg-blush-500 hover:bg-blush-200/80 text-white font-bold rounded-xl text-xs transition shadow-sm">
                + Tambah Data
            </button>
        </div>

        @if(count($logbookData['adjustments']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-blush-50 text-blush-700 font-mono border-b border-blush-200">
                            <th class="p-2.5">Bulan</th>
                            <th class="p-2.5">Kegiatan</th>
                            <th class="p-2.5 text-center">Jumlah Frekuensi</th>
                            <th class="p-2.5">Keterangan / Catatan</th>
                            <th class="p-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blush-100">
                        @foreach($logbookData['adjustments'] as $adj)
                            @php
                                $actDef = $activities[$adj->activity_key] ?? null;
                            @endphp
                            <tr class="hover:bg-blush-50/70">
                                <td class="p-2.5 font-bold text-blush-700 font-mono">
                                    Bulan {{ $adj->month }} ({{ \Carbon\Carbon::create(null, $adj->month, 1)->translatedFormat('F') }})
                                </td>
                                <td class="p-2.5 text-ink-700">
                                    {{ $actDef['title'] ?? $adj->activity_key }}
                                </td>
                                <td class="p-2.5 text-center font-mono font-bold text-ink-900">
                                    +{{ $adj->adjustment_count }}
                                </td>
                                <td class="p-2.5 text-ink-500">
                                    {{ $adj->notes ?: '-' }}
                                </td>
                                <td class="p-2.5 text-right space-x-2">
                                    <button wire:click="openAdjustmentModal('{{ $adj->id }}')" class="text-blush-600 hover:underline">
                                        Edit
                                    </button>
                                    <button wire:click="deleteAdjustment('{{ $adj->id }}')" onclick="confirm('Hapus penyesuaian ini?') || event.stopImmediatePropagation()" class="text-rose-700 hover:underline">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-ink-500 py-2">
                Belum ada penyesuaian manual di tahun {{ $selectedYear }}. Semua data dihitung murni dari sesi konseling yang tersimpan.
            </p>
        @endif
    </div>

    <!-- Modal Form Penyesuaian Manual -->
    @if($showAdjustmentModal)
        <div x-data @keydown.escape.window="$wire.closeAdjustmentModal()" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/50" role="dialog" aria-modal="true" aria-labelledby="modal-adj-title">
            <div class="sanctuary-glass-card border border-blush-200 rounded-3xl w-full max-w-lg p-6 sm:p-7 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-blush-200 pb-3">
                    <h3 id="modal-adj-title" class="font-serif text-lg font-semibold text-ink-900">
                        {{ $editing_adjustment_id ? 'Edit' : 'Tambah' }} Penyesuaian Logbook (Tahun {{ $selectedYear }})
                    </h3>
                    <button wire:click="closeAdjustmentModal" type="button" aria-label="Tutup jendela penyesuaian" class="text-ink-500 hover:text-ink-900 text-lg font-bold p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-blush-400">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveAdjustment" class="space-y-4">
                    <div>
                        <label for="adj_activity_key" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Item Kegiatan Pelayanan *</label>
                        <select id="adj_activity_key" wire:model="adj_activity_key" class="w-full bg-white border border-blush-200 rounded-xl px-3 py-2 text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200">
                            @foreach($activities as $key => $act)
                                <option value="{{ $key }}">{{ $act['number'] }}. {{ $act['title'] }} ({{ $act['skp_weight'] }} SKP)</option>
                            @endforeach
                        </select>
                        @error('adj_activity_key') <span class="text-rose-700 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="adj_month" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Bulan Pelaksanaan *</label>
                            <select id="adj_month" wire:model="adj_month" class="w-full bg-white border border-blush-200 rounded-xl px-3 py-2 text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">Bulan {{ $m }} - {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                            @error('adj_month') <span class="text-rose-700 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="adj_count" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Jumlah Frekuensi *</label>
                            <input id="adj_count" wire:model="adj_count" type="number" min="0" class="w-full bg-white border border-blush-200 rounded-xl px-3 py-2 text-xs text-ink-900 font-mono focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200">
                            @error('adj_count') <span class="text-rose-700 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="adj_notes" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Catatan / Keterangan Penyelenggaraan</label>
                        <textarea id="adj_notes" wire:model="adj_notes" rows="2" placeholder="Contoh: Saksi ahli di PN Selatpanjang kasus pidana anak No. 12/Pid.Sus/2026" class="w-full bg-white border border-blush-200 rounded-xl px-3 py-2 text-xs text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200"></textarea>
                        @error('adj_notes') <span class="text-rose-700 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-blush-200">
                        <button wire:click="closeAdjustmentModal" type="button" class="px-4 py-2 bg-white hover:bg-blush-100 text-ink-700 font-semibold rounded-xl text-xs transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold rounded-xl text-xs transition focus:outline-none focus:ring-2 focus:ring-blush-400 shadow-md">
                            Simpan Penyesuaian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
