<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Dashboard Analytics & Rekam Medis</h1>
        <p class="text-sm text-slate-400">Ringkasan kinerja klinis, keuangan, distribusi diagnosa, & status rekam medis.</p>
    </div>

    <!-- Alert Unclosed Notes -->
    @if($unclosedNotesCount > 0)
        <div class="mb-6 p-4 bg-amber-950/60 border-l-4 border-amber-500 text-amber-200 rounded-r-lg shadow-sm flex items-center justify-between border border-slate-700">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="font-bold text-amber-300">Perhatian: {{ $unclosedNotesCount }} Rekam Medis Belum Dikunci (Unclosed Notes)</p>
                    <p class="text-xs text-amber-200/80">Pastikan seluruh rekam medis sesi yang telah selesai segera dilengkapi dan dikunci demi keamanan data etis pasien.</p>
                </div>
            </div>
            <a href="{{ route('sessions.index') }}" class="text-xs bg-amber-600 hover:bg-amber-500 text-white font-semibold px-3 py-1.5 rounded transition">
                Tinjau Sesi
            </a>
        </div>
    @endif

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1: Total Sesi -->
        <div class="bg-slate-800 p-5 rounded-xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sesi Bulan Ini</p>
                <h3 class="text-3xl font-extrabold text-white mt-1">{{ $totalSessionsMonth }}</h3>
                <p class="text-xs text-slate-400 mt-1">Konsultasi terjadwal & selesai</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-indigo-950/80 text-indigo-400 border border-indigo-700/50 flex items-center justify-center text-xl font-bold">
                📅
            </div>
        </div>

        <!-- Card 2: Unclosed Notes -->
        <div class="bg-slate-800 p-5 rounded-xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Unclosed Notes</p>
                <h3 class="text-3xl font-extrabold text-amber-400 mt-1">{{ $unclosedNotesCount }}</h3>
                <p class="text-xs text-slate-400 mt-1">Catatan belum dikunci</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-amber-950/80 text-amber-400 border border-amber-700/50 flex items-center justify-center text-xl font-bold">
                📝
            </div>
        </div>

        <!-- Card 3: Pendapatan Bulanan -->
        <div class="bg-slate-800 p-5 rounded-xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pendapatan Bulan Ini</p>
                <h3 class="text-2xl font-extrabold text-emerald-400 mt-1">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Total pembayaran nett</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-emerald-950/80 text-emerald-400 border border-emerald-700/50 flex items-center justify-center text-xl font-bold">
                💵
            </div>
        </div>

        <!-- Card 4: Pendapatan Tahunan -->
        <div class="bg-slate-800 p-5 rounded-xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pendapatan Tahun Ini</p>
                <h3 class="text-2xl font-extrabold text-teal-400 mt-1">Rp {{ number_format($annualRevenue, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Akumulasi pendapatan</p>
            </div>
            <div class="w-12 h-12 rounded-lg bg-teal-950/80 text-teal-400 border border-teal-700/50 flex items-center justify-center text-xl font-bold">
                📊
            </div>
        </div>
    </div>

    <!-- Analytics Section: Top 5 ICD-10 & Workload Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top 5 ICD-10 Diagnoses -->
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 shadow-sm">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-between">
                <span>🩺 Top 5 Distribusi Diagnosa (ICD-10)</span>
                <span class="text-xs font-normal text-slate-400">Frekuensi Kasus</span>
            </h3>
            
            @if(count($icd10Distribution) > 0)
                <div class="space-y-4">
                    @foreach($icd10Distribution as $item)
                        @php
                            $max = max(collect($icd10Distribution)->pluck('total')->toArray() ?: [1]);
                            $percentage = round(($item->total / $max) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between items-center text-sm mb-1">
                                <span class="font-semibold text-slate-200">
                                    <span class="bg-slate-900 text-teal-400 font-mono px-2 py-0.5 rounded text-xs mr-2 border border-slate-700">{{ $item->icd10_code }}</span>
                                    {{ $item->icd10_description ?? 'Diagnosa Medis' }}
                                </span>
                                <span class="font-bold text-teal-400">{{ $item->total }} kasus</span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-2.5 overflow-hidden border border-slate-700/50">
                                <div class="bg-teal-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-slate-500">
                    <p class="text-sm">Belum ada data diagnosa ICD-10 yang tercatat.</p>
                </div>
            @endif
        </div>

        <!-- Workload Minggu Ini -->
        <div class="bg-slate-800 p-6 rounded-xl border border-slate-700 shadow-sm">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-between">
                <span>📈 Beban Kerja Konseling (Minggu Ini)</span>
                <span class="text-xs font-normal text-slate-400">Jumlah Sesi per Hari</span>
            </h3>

            <div class="space-y-3">
                @php
                    $days = [
                        now()->startOfWeek()->format('Y-m-d') => 'Senin',
                        now()->startOfWeek()->addDay()->format('Y-m-d') => 'Selasa',
                        now()->startOfWeek()->addDays(2)->format('Y-m-d') => 'Rabu',
                        now()->startOfWeek()->addDays(3)->format('Y-m-d') => 'Kamis',
                        now()->startOfWeek()->addDays(4)->format('Y-m-d') => 'Jumat',
                        now()->startOfWeek()->addDays(5)->format('Y-m-d') => 'Sabtu',
                        now()->startOfWeek()->addDays(6)->format('Y-m-d') => 'Minggu',
                    ];
                @endphp
                @foreach($days as $dateStr => $dayName)
                    @php
                        $count = $workloadData[$dateStr] ?? 0;
                    @endphp
                    <div class="flex items-center justify-between p-2 rounded bg-slate-900/60 border border-slate-700/50 hover:bg-slate-700/40 transition">
                        <span class="text-sm font-medium text-slate-300 w-24">{{ $dayName }}</span>
                        <div class="flex-1 mx-4 bg-slate-950 h-2 rounded-full overflow-hidden">
                            <div class="bg-teal-500 h-2 rounded-full" style="width: {{ min(100, $count * 20) }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-teal-400 w-12 text-right">{{ $count }} Sesi</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Sessions Quick Action Table -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-white">📋 Daftar Sesi Terbaru & Rekam Medis</h3>
                <p class="text-xs text-slate-400">Akses langsung rekam medis sesi & cetak dokumen resmi</p>
            </div>
            <a href="{{ route('sessions.index') }}" class="text-xs text-teal-400 font-bold hover:underline">
                Lihat Semua Sesi →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-400 text-xs uppercase font-semibold border-b border-slate-700">
                        <th class="p-3">Tanggal & Waktu</th>
                        <th class="p-3">Klien</th>
                        <th class="p-3">Status Sesi</th>
                        <th class="p-3">Status Rekam Medis</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-sm">
                    @forelse($recentAppointments as $session)
                        <tr class="hover:bg-slate-700/40 transition">
                            <td class="p-3 font-medium text-slate-200">
                                {{ $session->session_date }}
                                <span class="text-xs text-slate-400 block">{{ $session->start_time }} - {{ $session->end_time }}</span>
                            </td>
                            <td class="p-3 font-semibold text-white">
                                {{ $session->medicalCase?->client?->full_name ?? 'Klien' }}
                                <span class="text-xs text-slate-400 font-normal block">{{ $session->medicalCase?->client?->client_code ?? '-' }}</span>
                            </td>
                            <td class="p-3">
                                @if($session->status == 'done' || $session->status == 'Completed')
                                    <span class="bg-emerald-950 text-emerald-400 border border-emerald-700/50 text-xs px-2.5 py-0.5 rounded-full font-semibold">Selesai</span>
                                @elseif($session->status == 'scheduled')
                                    <span class="bg-blue-950 text-blue-400 border border-blue-700/50 text-xs px-2.5 py-0.5 rounded-full font-semibold">Terjadwal</span>
                                @else
                                    <span class="bg-slate-700 text-slate-300 border border-slate-600 text-xs px-2.5 py-0.5 rounded-full font-semibold">{{ ucfirst($session->status) }}</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($session->is_locked)
                                    <span class="bg-emerald-950 text-emerald-400 border border-emerald-700/50 text-xs px-2 py-0.5 rounded font-medium">🔒 Terkunci (Locked)</span>
                                @else
                                    <span class="bg-amber-950 text-amber-400 border border-amber-700/50 text-xs px-2 py-0.5 rounded font-medium">✏️ Draft (Terbuka)</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('sessions.show', $session->id) }}" class="inline-flex items-center bg-teal-950 hover:bg-teal-900 text-teal-300 border border-teal-700/50 font-medium px-3 py-1.5 rounded text-xs transition">
                                    📝 Rekam Medis Sesi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-500 text-sm">
                                Belum ada data sesi konsultasi. <a href="{{ route('sessions.create') }}" class="text-teal-400 underline">Buat Sesi Baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
