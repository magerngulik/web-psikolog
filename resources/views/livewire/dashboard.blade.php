<div class="space-y-8">
    <!-- Sanctuary Hero Header -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 sanctuary-glass-card border border-teal-500/15">
        <!-- Ambient decorative aura -->
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-gradient-to-br from-teal-500/15 to-emerald-500/5 blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-gradient-to-tr from-amber-500/10 to-transparent blur-3xl pointer-events-none" aria-hidden="true"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-400/25 text-teal-300 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-mono uppercase tracking-wider text-[11px]">Dashboard Analytics & Rekam Medis</span>
                    <span class="text-teal-500">•</span>
                    <span class="font-mono text-teal-200/90">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl text-white font-medium tracking-tight">
                    Selamat Datang, {{ $psychologist?->formatted_name ?? 'Rekan Sejawat' }}
                </h1>
                <p class="text-sm text-slate-300/80 leading-relaxed font-sans">
                    {{ $psychologist?->practice_name ? 'Praktik ' . $psychologist->practice_name : 'Praktik Mandiri' }}
                    @if($psychologist?->practice_city)
                        • {{ $psychologist->practice_city }}
                    @endif
                    @if($psychologist?->sipa_number)
                        • <span class="font-mono text-teal-300/90">SIPA: {{ $psychologist->sipa_number }}</span>
                    @endif
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                <a href="{{ route('sessions.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-semibold text-xs sm:text-sm shadow-lg shadow-teal-950/50 hover:shadow-teal-500/20 transition-all duration-200 transform hover:-translate-y-0.5">
                    <span class="text-base">📅</span>
                    <span>Catat Sesi Baru</span>
                </a>
                <a href="{{ route('clients.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-spruce-900/90 hover:bg-spruce-800 text-teal-200 border border-teal-500/30 hover:border-teal-400/50 font-semibold text-xs sm:text-sm shadow-sm transition-all duration-200">
                    <span class="text-base">👥</span>
                    <span>Klien Baru</span>
                </a>
                <a href="{{ route('reports.logbook') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-spruce-900/90 hover:bg-spruce-800 text-slate-200 border border-teal-500/20 hover:border-teal-400/40 font-medium text-xs sm:text-sm transition-all duration-200">
                    <span class="text-base">📈</span>
                    <span>Logbook SKP</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Unclosed Notes (Priority Ethical Care Banner) -->
    @if($unclosedNotesCount > 0)
        <div class="relative overflow-hidden rounded-2xl p-5 border border-amber-500/30 bg-gradient-to-r from-amber-950/70 via-spruce-900/85 to-amber-950/40 shadow-xl shadow-amber-950/30">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-amber-300 text-2xl flex-shrink-0 shadow-inner">
                        ⚠️
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-400/20 border border-amber-400/40 text-amber-300 font-mono text-xs font-semibold">
                                Etika Rekam Medis
                            </span>
                            <h2 class="font-serif text-lg font-semibold text-amber-200">
                                {{ $unclosedNotesCount }} Catatan Sesi Belum Dikunci (Draft Notes)
                            </h2>
                        </div>
                        <p class="text-xs text-amber-200/80 leading-relaxed max-w-2xl">
                            Demi kepatuhan etika klinis dan validasi kredit SKP, pastikan rekam medis sesi yang telah selesai segera dilengkapi, diverifikasi, dan dikunci secara permanen.
                        </p>
                    </div>
                </div>
                <a href="{{ route('sessions.index') }}" 
                   class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md shadow-amber-950/40 transition flex items-center gap-2 flex-shrink-0">
                    <span>Tinjau & Kunci Sesi</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Metric Cards Grid (Sanctuary Vitals) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Sesi Bulan Ini -->
        <div class="sanctuary-glass-card rounded-2xl p-5 flex flex-col justify-between hover:border-teal-400/30 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Sesi Bulan Ini</span>
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    📅
                </div>
            </div>
            <div class="space-y-1">
                <div class="font-serif text-4xl font-semibold text-white tracking-tight">
                    {{ $totalSessionsMonth }}
                </div>
                <div class="flex items-center justify-between text-xs pt-2 border-t border-teal-500/10">
                    <span class="text-slate-400">Konsultasi Terjadwal</span>
                    <span class="text-teal-300 font-medium">{{ $totalActiveCases }} Kasus Aktif</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Unclosed Notes -->
        <div class="sanctuary-glass-card rounded-2xl p-5 flex flex-col justify-between hover:border-amber-400/30 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Draft Belum Dikunci</span>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    📝
                </div>
            </div>
            <div class="space-y-1">
                <div class="font-serif text-4xl font-semibold {{ $unclosedNotesCount > 0 ? 'text-amber-300' : 'text-slate-200' }} tracking-tight">
                    {{ $unclosedNotesCount }}
                </div>
                <div class="flex items-center justify-between text-xs pt-2 border-t border-teal-500/10">
                    <span class="text-slate-400">Status Keamanan</span>
                    @if($unclosedNotesCount == 0)
                        <span class="text-emerald-400 font-medium">Semua Terkunci ✓</span>
                    @else
                        <span class="text-amber-400 font-medium">Perlu Divalidasi</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Pendapatan Bulanan -->
        <div class="sanctuary-glass-card rounded-2xl p-5 flex flex-col justify-between hover:border-emerald-400/30 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Penerimaan Bulan Ini</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    💵
                </div>
            </div>
            <div class="space-y-1">
                <div class="font-mono text-2xl lg:text-3xl font-bold text-emerald-300 tracking-tight">
                    Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
                </div>
                <div class="flex items-center justify-between text-[11px] pt-2 border-t border-teal-500/10">
                    <span class="text-slate-400">Konseling: <strong class="text-white">Rp {{ number_format($monthlySessionRevenue, 0, ',', '.') }}</strong></span>
                    <span class="text-teal-400">Seminar: <strong class="text-emerald-300">Rp {{ number_format($monthlyActivityRevenue, 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Card 4: Pendapatan Tahunan -->
        <div class="sanctuary-glass-card rounded-2xl p-5 flex flex-col justify-between hover:border-teal-400/30 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Akumulasi {{ now()->year }}</span>
                <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-300 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    📊
                </div>
            </div>
            <div class="space-y-1">
                <div class="font-mono text-2xl lg:text-3xl font-bold text-teal-300 tracking-tight">
                    Rp {{ number_format($annualRevenue, 0, ',', '.') }}
                </div>
                <div class="flex items-center justify-between text-xs pt-2 border-t border-teal-500/10">
                    <span class="text-slate-400">Tahun Berjalan</span>
                    <span class="text-teal-300/80 font-medium">Rekap Keuangan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinical Analytics Section: ICD-10 Distribution & Workload Flow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 ICD-10 Diagnoses -->
        <div class="sanctuary-glass-card rounded-2xl p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-teal-500/10 pb-4">
                <div>
                    <h2 class="font-serif text-xl font-semibold text-white flex items-center gap-2">
                        <span>🩺</span>
                        <span>Distribusi Diagnosa Klinis</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Top 5 klasifikasi ICD-10 kasus yang ditangani</p>
                </div>
                <span class="text-[11px] font-mono font-medium px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/20">
                    ICD-10 / PPDGJ
                </span>
            </div>
            
            @if(count($icd10Distribution) > 0)
                <div class="space-y-4">
                    @php
                        $max = max(collect($icd10Distribution)->pluck('total')->toArray() ?: [1]);
                    @endphp
                    @foreach($icd10Distribution as $index => $item)
                        @php
                            $percentage = round(($item->total / $max) * 100);
                        @endphp
                        <div class="space-y-1.5 p-2.5 rounded-xl bg-spruce-950/40 border border-teal-500/5 hover:border-teal-500/20 transition">
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center gap-2 min-w-0 pr-2">
                                    <span class="w-5 h-5 rounded-md bg-spruce-900 text-teal-300 text-[10px] font-mono font-bold flex items-center justify-center flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="bg-teal-500/15 text-teal-300 font-mono px-2 py-0.5 rounded text-[11px] font-semibold border border-teal-500/30 flex-shrink-0">
                                        {{ $item->icd10_code }}
                                    </span>
                                    <span class="text-slate-200 font-medium truncate">
                                        {{ $item->icd10_description ?? 'Diagnosa Klinis' }}
                                    </span>
                                </div>
                                <span class="font-mono font-semibold text-teal-300 text-xs flex-shrink-0">
                                    {{ $item->total }} kasus
                                </span>
                            </div>
                            <div class="w-full bg-spruce-950 rounded-full h-2 overflow-hidden border border-teal-500/10">
                                <div class="bg-gradient-to-r from-teal-500 to-emerald-400 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-slate-500 space-y-2">
                    <span class="text-3xl">📭</span>
                    <p class="text-sm">Belum ada data diagnosa ICD-10 yang tercatat.</p>
                    <p class="text-xs text-slate-600">Diagnosa akan otomatis terkumpul saat Anda melengkapi form rekam medis sesi.</p>
                </div>
            @endif
        </div>

        <!-- Weekly Workload Rhythm -->
        <div class="sanctuary-glass-card rounded-2xl p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-teal-500/10 pb-4">
                <div>
                    <h2 class="font-serif text-xl font-semibold text-white flex items-center gap-2">
                        <span>📈</span>
                        <span>Ritme Beban Kerja Sesi</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Distribusi intensitas sesi per hari kerja minggu ini</p>
                </div>
                <span class="text-[11px] font-mono font-medium px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/20">
                    Minggu Ini
                </span>
            </div>

            <div class="space-y-2.5">
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
                    $todayStr = now()->format('Y-m-d');
                    $weeklyTotal = array_sum($workloadData);
                @endphp
                @foreach($days as $dateStr => $dayName)
                    @php
                        $count = $workloadData[$dateStr] ?? 0;
                        $isToday = ($dateStr === $todayStr);
                    @endphp
                    <div class="flex items-center justify-between p-2.5 rounded-xl border transition-all duration-200 {{ $isToday ? 'bg-teal-500/10 border-teal-400/40 shadow-sm' : 'bg-spruce-950/40 border-teal-500/5 hover:border-teal-500/20' }}">
                        <div class="flex items-center gap-2 w-28">
                            <span class="text-xs font-semibold {{ $isToday ? 'text-teal-300' : 'text-slate-300' }}">
                                {{ $dayName }}
                            </span>
                            @if($isToday)
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-teal-500/20 text-teal-300 border border-teal-400/30">
                                    Hari Ini
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 mx-3 bg-spruce-950 h-2 rounded-full overflow-hidden border border-teal-500/10">
                            <div class="bg-gradient-to-r from-teal-500 to-emerald-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $count * 20) }}%"></div>
                        </div>
                        <span class="font-mono text-xs font-bold {{ $count > 0 ? 'text-teal-300' : 'text-slate-500' }} w-16 text-right">
                            {{ $count }} Sesi
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-teal-500/10 flex items-center justify-between text-xs text-slate-400">
                <span>Total Minggu Ini: <strong class="text-teal-300 font-mono">{{ $weeklyTotal }} Sesi</strong></span>
                <span class="text-slate-500">Kapasitas terjaga secara optimal</span>
            </div>
        </div>
    </div>

    <!-- Upcoming Activities & Seminars -->
    <div class="sanctuary-glass-card rounded-2xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-teal-500/10 pb-4">
            <div>
                <h2 class="font-serif text-xl font-semibold text-white flex items-center gap-2">
                    <span>🎤</span>
                    <span>Kegiatan & Seminar Mendatang</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Jadwal narasumber, pelatihan sekolah, psikoedukasi, dan acara komunitas</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('activities.create') }}" class="px-3 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/25 text-xs font-bold transition flex items-center gap-1.5">
                    <span>+ Jadwalkan Kegiatan</span>
                </a>
                <a href="{{ route('activities.index') }}" class="text-xs font-semibold text-teal-400 hover:text-teal-300 transition flex items-center gap-1.5">
                    <span>Lihat Semua</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        @if($upcomingActivities->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($upcomingActivities as $act)
                    <div class="p-4 rounded-2xl bg-spruce-950/70 border border-teal-500/15 hover:border-teal-500/35 transition space-y-2.5 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-teal-500/10 text-teal-300 border border-teal-500/20">
                                    {{ $act->event_type_label }}
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">
                                    {{ \Carbon\Carbon::parse($act->start_date)->format('d M Y') }}
                                </span>
                            </div>
                            <a href="{{ route('activities.show', $act->id) }}" class="font-serif font-bold text-white text-sm line-clamp-2 mt-1.5 group-hover:text-teal-300 transition">
                                {{ $act->title }}
                            </a>
                            <div class="text-xs text-slate-400 mt-1 truncate">
                                🏛️ {{ $act->organization?->name ?: ($act->organizer_name ?: 'Mandiri / Umum') }}
                            </div>
                        </div>

                        <div class="pt-2 border-t border-teal-500/10 flex items-center justify-between text-[11px]">
                            <span class="text-slate-400 font-mono">{{ $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') . ' WIB' : 'Waktu fleksibel' }}</span>
                            <a href="{{ route('activities.show', $act->id) }}" class="text-teal-400 hover:underline font-semibold">
                                Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-slate-400 text-xs">
                <span>Belum ada agenda seminar atau kegiatan yang dijadwalkan dalam waktu dekat.</span>
                <a href="{{ route('activities.create') }}" class="text-teal-400 font-semibold hover:underline ml-1">Jadwalkan kegiatan pertama →</a>
            </div>
        @endif
    </div>

    <!-- Recent Sessions & Medical Records Table -->
    <div class="sanctuary-glass-card rounded-2xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-teal-500/10 pb-4">
            <div>
                <h2 class="font-serif text-xl font-semibold text-white flex items-center gap-2">
                    <span>📋</span>
                    <span>Sesi & Rekam Medis Terbaru</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Akses langsung rekam medis sesi, diagnosis ICD-10, dan dokumen RPP</p>
            </div>
            <a href="{{ route('sessions.index') }}" class="text-xs font-semibold text-teal-400 hover:text-teal-300 transition flex items-center gap-1.5 self-start sm:self-auto">
                <span>Lihat Semua Sesi Konsultasi</span>
                <span>→</span>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-teal-500/10">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-spruce-950/80 text-teal-300/80 text-[11px] uppercase font-mono tracking-wider border-b border-teal-500/15">
                        <th class="p-3.5">Tanggal & Waktu</th>
                        <th class="p-3.5">Klien</th>
                        <th class="p-3.5">Status Sesi</th>
                        <th class="p-3.5">Rekam Medis</th>
                        <th class="p-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10 text-xs">
                    @forelse($recentAppointments as $session)
                        <tr class="hover:bg-teal-500/[0.04] transition">
                            <td class="p-3.5 text-slate-200">
                                <span class="font-medium font-sans block text-sm">{{ $session->session_date }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $session->start_time }} - {{ $session->end_time }}</span>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-teal-500/15 border border-teal-500/25 flex items-center justify-center font-serif text-teal-300 font-bold text-xs flex-shrink-0">
                                        {{ mb_substr($session->medicalCase?->client?->full_name ?? 'K', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-white block text-sm">
                                            {{ $session->medicalCase?->client?->full_name ?? 'Klien' }}
                                        </span>
                                        <span class="text-[11px] font-mono text-teal-400/80">
                                            {{ $session->medicalCase?->client?->client_code ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                @if($session->status == 'done' || $session->status == 'Completed')
                                    <span class="bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 text-[11px] px-2.5 py-0.5 rounded-full font-medium inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Selesai
                                    </span>
                                @elseif($session->status == 'scheduled')
                                    <span class="bg-blue-950/80 text-blue-300 border border-blue-500/30 text-[11px] px-2.5 py-0.5 rounded-full font-medium inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                        Terjadwal
                                    </span>
                                @else
                                    <span class="bg-slate-800 text-slate-300 border border-slate-700 text-[11px] px-2.5 py-0.5 rounded-full font-medium">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($session->is_locked)
                                    <span class="bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 text-[11px] px-2.5 py-1 rounded-lg font-medium inline-flex items-center gap-1.5">
                                        <span>🔒</span>
                                        <span>Terkunci (Final)</span>
                                    </span>
                                @else
                                    <span class="bg-amber-950/80 text-amber-300 border border-amber-500/30 text-[11px] px-2.5 py-1 rounded-lg font-medium inline-flex items-center gap-1.5">
                                        <span class="animate-pulse">✏️</span>
                                        <span>Draft (Terbuka)</span>
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('sessions.show', $session->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 font-medium text-xs transition shadow-sm">
                                    <span>📝</span>
                                    <span>Buka Rekam Medis</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 text-sm">
                                <p class="mb-2">Belum ada sesi konsultasi yang terjadwal.</p>
                                <a href="{{ route('sessions.create') }}" class="text-teal-400 font-semibold underline hover:text-teal-300">
                                    Buat Sesi Konsultasi Baru →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
