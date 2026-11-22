@php
    $hour = now()->hour;
    $greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));

    $fullPsychologistName = $psychologist?->formatted_name ?? 'Rekan Sejawat';
    // Prevent orphan degree words in hero heading by showing primary name on H1 and full credentials in metadata below
    $shortPsychologistName = trim(explode(',', $fullPsychologistName)[0]);

    $formatCompactRp = function ($amount): string {
        $val = (float) $amount;
        if ($val >= 1000000000) {
            return 'Rp ' . rtrim(rtrim(number_format($val / 1000000000, 1, ',', '.'), '0'), ',') . ' M';
        }
        if ($val >= 10000000) {
            return 'Rp ' . rtrim(rtrim(number_format($val / 1000000, 1, ',', '.'), '0'), ',') . ' jt';
        }
        return 'Rp ' . number_format($val, 0, ',', '.');
    };
@endphp

<div class="space-y-6">
    <!-- Row 1: Balanced Top Bento (Left 2/3: Hero + 4 Neutral Clinical KPIs | Right 1/3: Interactive Schedule Calendar) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-stretch">
        <!-- Left Column (2/3) -->
        <div class="xl:col-span-2 flex flex-col justify-between gap-5">
            <!-- Cozy Pastel Welcome Hero Banner -->
            <div class="relative overflow-hidden rounded-3xl p-6 sm:p-7 pastel-hero">
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                    <div class="space-y-3 max-w-xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/90 border border-blush-200 text-blush-800 text-xs font-semibold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                <span class="font-mono uppercase tracking-wider text-[10px]">Ringkasan Praktik</span>
                                <span class="text-blush-300">•</span>
                                <span class="font-mono text-ink-700 text-[11px]">{{ now()->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <button type="button"
                                    x-show="privacyMode"
                                    x-cloak
                                    @click="togglePrivacy()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blush-500 text-white text-[11px] font-bold shadow-2xs hover:bg-blush-600 transition-colors"
                                    title="Klik untuk menonaktifkan Mode Privasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <span>Mode Privasi Aktif</span>
                            </button>
                        </div>

                        <div>
                            <h1 class="font-serif text-2xl sm:text-3xl text-ink-900 font-bold tracking-tight">
                                {{ $greeting }}, {{ $shortPsychologistName }}
                            </h1>
                            <p class="text-xs sm:text-sm text-ink-700 leading-relaxed font-sans mt-1">
                                <span class="font-semibold text-ink-900">{{ $fullPsychologistName }}</span>
                                <span class="mx-1 text-blush-400">•</span>
                                {{ $psychologist?->practice_name ? 'Praktik ' . $psychologist->practice_name : 'Praktik Mandiri' }}
                                @if($psychologist?->practice_city)
                                    • {{ $psychologist->practice_city }}
                                @endif
                                @if($psychologist?->sipa_number)
                                    • <span class="font-mono text-blush-800 font-semibold">SIPPK: {{ $psychologist->sipa_number }}</span>
                                @endif
                            </p>
                        </div>

                        <!-- Single Clear Primary CTA + Privacy Quick Toggle -->
                        <div class="flex flex-wrap items-center gap-2.5 pt-1">
                            <a href="{{ route('clients.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-blush-500 hover:bg-blush-600 text-white font-bold text-xs sm:text-sm shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Klien Baru</span>
                            </a>
                            <button type="button"
                                    @click="togglePrivacy()"
                                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl border font-bold text-xs sm:text-sm transition-colors shadow-2xs"
                                    :class="privacyMode ? 'bg-blush-600 text-white border-blush-600 hover:bg-blush-700' : 'bg-white hover:bg-blush-50 text-ink-700 border-blush-200'">
                                <svg x-show="!privacyMode" class="w-4 h-4 text-blush-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="privacyMode" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <span x-text="privacyMode ? 'Tampilkan Nama' : 'Mode Privasi'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Lightweight Inline SVG Cozy Clinical Illustration -->
                    <div class="hidden sm:flex items-center justify-center shrink-0" aria-hidden="true">
                        <svg class="w-36 h-28" viewBox="0 0 220 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="115" cy="78" r="58" fill="#FFFFFF" fill-opacity="0.72"/>
                            <circle cx="166" cy="40" r="18" fill="#E8DCFB" fill-opacity="0.85"/>
                            <circle cx="52" cy="110" r="14" fill="#D5F2E3" fill-opacity="0.9"/>
                            <circle cx="178" cy="112" r="10" fill="#FDE6B8" fill-opacity="0.95"/>
                            <rect x="64" y="34" width="84" height="92" rx="16" fill="#FFFFFF" stroke="#F0C7D8" stroke-width="2"/>
                            <rect x="78" y="50" width="38" height="8" rx="4" fill="#F8C6D8"/>
                            <rect x="78" y="66" width="56" height="6" rx="3" fill="#EDE4FA"/>
                            <rect x="78" y="79" width="46" height="6" rx="3" fill="#EDE4FA"/>
                            <rect x="78" y="96" width="28" height="14" rx="7" fill="#D95D88"/>
                            <path d="M148 92C148 72 162 58 180 58C180 76 166 92 148 92Z" fill="#A9E3C5"/>
                            <path d="M148 92C155 82 165 72 176 63" stroke="#3F9A6E" stroke-width="2" stroke-linecap="round"/>
                            <path d="M132 48C132 45.2 134.2 43 137 43C138.8 43 140.4 44 141.2 45.5C142 44 143.6 43 145.4 43C148.2 43 150.4 45.2 150.4 48C150.4 53.2 141.2 58 141.2 58C141.2 58 132 53.2 132 48Z" fill="#E387A9"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 4 Clean Neutral Clinical KPI Cards (Crisp White Base + Soft Pastel Icon Accents) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Card 1: Total Sesi Bulan Ini -->
                <div class="pastel-card-white rounded-3xl p-4 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-ink-700 leading-tight">Sesi Bulan Ini</span>
                        <div class="w-8 h-8 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="font-serif text-3xl font-bold text-ink-900 tracking-tight tabular-nums">
                            {{ $totalSessionsMonth }}
                        </div>
                        <div class="text-[11px] pt-1.5 border-t border-blush-100 text-ink-700 font-semibold truncate">
                            {{ $totalActiveCases }} Kasus Aktif
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pembayaran Belum Lunas (Clickable — replaces redundant yellow Alert Banner) -->
                <a href="{{ route('sessions.index') }}"
                   class="pastel-card-white rounded-3xl p-4 flex flex-col justify-between hover:border-blush-400 transition-colors group"
                   title="Klik untuk melihat dan memperbarui pembayaran sesi yang belum lunas">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-ink-700 leading-tight">Belum Lunas</span>
                        <div class="w-8 h-8 rounded-xl {{ $unpaidSessionsCount > 0 ? 'bg-blush-100 text-blush-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="font-serif text-3xl font-bold {{ $unpaidSessionsCount > 0 ? 'text-blush-700' : 'text-ink-900' }} tracking-tight tabular-nums">
                            {{ $unpaidSessionsCount }}
                        </div>
                        <div class="text-[11px] pt-1.5 border-t border-blush-100 flex items-center justify-between gap-1">
                            @if($unpaidSessionsCount == 0)
                                <span class="text-emerald-700 font-bold truncate">Semua Lunas</span>
                            @else
                                <span class="text-blush-800 font-mono font-bold truncate tabular-nums" title="Rp {{ number_format($unpaidSessionsTotal, 0, ',', '.') }}">
                                    {{ $formatCompactRp($unpaidSessionsTotal) }}
                                </span>
                                <span class="text-blush-600 font-bold group-hover:translate-x-0.5 transition-transform shrink-0">→</span>
                            @endif
                        </div>
                    </div>
                </a>

                <!-- Card 3: Pemasukan Bulan Ini -->
                <div class="pastel-card-white rounded-3xl p-4 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-ink-700 leading-tight">Pemasukan Bulan Ini</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="font-mono text-lg sm:text-xl font-bold text-emerald-800 tracking-tight truncate tabular-nums" title="Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}">
                            {{ $formatCompactRp($monthlyRevenue) }}
                        </div>
                        <div class="text-[11px] pt-1.5 border-t border-blush-100 text-ink-700 font-semibold truncate">
                            Sesi & Seminar
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Pemasukan Tahunan -->
                <div class="pastel-card-white rounded-3xl p-4 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-ink-700 leading-tight">Total Pemasukan {{ now()->year }}</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="font-mono text-lg sm:text-xl font-bold text-ink-900 tracking-tight truncate tabular-nums" title="Rp {{ number_format($annualRevenue, 0, ',', '.') }}">
                            {{ $formatCompactRp($annualRevenue) }}
                        </div>
                        <div class="text-[11px] pt-1.5 border-t border-blush-100 text-ink-700 font-semibold truncate">
                            Akumulasi Tahunan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (1/3): Interactive Schedule Calendar Widget -->
        <div class="pastel-card-white rounded-3xl overflow-hidden flex flex-col h-full">
            <!-- Pastel Calendar Header -->
            <div class="bg-gradient-to-r from-blush-500 to-blush-400 px-5 py-4 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-serif text-base font-bold leading-tight">Jadwal Praktik</h2>
                        <p class="text-[11px] text-white/90 font-medium">{{ $calendarMonthLabel }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button wire:click="previousWeek" type="button" title="Minggu Sebelumnya" aria-label="Minggu Sebelumnya"
                            class="w-7 h-7 rounded-lg bg-white/15 hover:bg-white/30 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button wire:click="goToToday" type="button"
                            class="px-2 py-1 rounded-lg bg-white/15 hover:bg-white/30 text-[10px] font-bold transition-colors">
                        Hari Ini
                    </button>
                    <button wire:click="nextWeek" type="button" title="Minggu Berikutnya" aria-label="Minggu Berikutnya"
                            class="w-7 h-7 rounded-lg bg-white/15 hover:bg-white/30 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 7-Day Interactive Week Strip -->
            <div class="p-3.5 border-b border-blush-100 shrink-0">
                <div class="grid grid-cols-7 gap-1 text-center">
                    @foreach($calendarDays as $day)
                        <button wire:click="selectDate('{{ $day['date'] }}')"
                                type="button"
                                class="flex flex-col items-center py-2 px-1 rounded-2xl transition-all {{ $day['isSelected'] ? 'bg-blush-500 text-white shadow-sm' : ($day['isToday'] ? 'bg-blush-100 text-blush-800 hover:bg-blush-200/70' : 'text-ink-700 hover:bg-blush-50') }}">
                            <span class="text-[10px] font-bold uppercase {{ $day['isSelected'] ? 'text-white/90' : 'text-ink-500' }}">
                                {{ $day['dayShort'] }}
                            </span>
                            <span class="font-serif text-sm font-bold mt-0.5 tabular-nums">
                                {{ $day['dayNum'] }}
                            </span>
                            <span class="mt-1 h-1.5 w-1.5 rounded-full {{ $day['eventCount'] > 0 ? ($day['isSelected'] ? 'bg-white' : 'bg-blush-500') : 'bg-transparent' }}"></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Selected Date Timeline List -->
            <div class="p-4 flex-1 flex flex-col justify-between gap-3">
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-blush-800">
                            {{ $selectedDateFormatted }}
                        </span>
                        <span class="text-[11px] font-semibold text-ink-500">
                            {{ count($selectedDateSchedule) }} agenda
                        </span>
                    </div>

                    @if($selectedDateSchedule->isNotEmpty())
                        <div class="divide-y divide-dashed divide-blush-200/80 max-h-44 overflow-y-auto scrollbar-none">
                            @foreach($selectedDateSchedule as $item)
                                <a href="{{ $item['url'] }}" class="py-2.5 first:pt-1 last:pb-1 flex items-start gap-3 group hover:bg-blush-50/60 -mx-2 px-2 rounded-xl transition-colors">
                                    <span class="font-mono text-xs font-semibold text-ink-700 w-11 shrink-0 pt-0.5 tabular-nums">
                                        {{ $item['time'] }}
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full {{ $item['dotClass'] }} mt-1.5 shrink-0 ring-4 ring-blush-50"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-ink-900 group-hover:text-blush-700 transition-colors truncate">
                                            <span x-show="!privacyMode">{{ $item['title'] }}</span>
                                            <span x-show="privacyMode" x-cloak class="font-mono text-blush-800">{{ $item['maskedTitle'] }}</span>
                                        </p>
                                        <p class="text-[11px] text-ink-700 truncate mt-0.5">
                                            <span x-show="!privacyMode">{{ $item['subtitle'] }}</span>
                                            <span x-show="privacyMode" x-cloak class="italic text-ink-500">{{ $item['maskedSubtitle'] }}</span>
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="py-6 text-center space-y-1.5">
                            <div class="w-9 h-9 rounded-2xl bg-blush-50 text-blush-500 flex items-center justify-center mx-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-ink-700">Tidak ada jadwal di tanggal ini</p>
                            <p class="text-[11px] text-ink-500">Klik tanggal bertitik pink untuk melihat jadwal.</p>
                        </div>
                    @endif
                </div>

                <div class="pt-2 border-t border-blush-100">
                    <a href="{{ route('sessions.create') }}"
                       class="w-full py-2 rounded-2xl border border-dashed border-blush-300 hover:border-blush-500 hover:bg-blush-50 text-blush-800 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Jadwalkan Sesi Baru</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2 (Full-Width — Directly Above the Fold!): Recent Sessions & Medical Records Table -->
    <div class="pastel-card-white rounded-3xl p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blush-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blush-100 text-blush-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-serif text-lg font-bold text-ink-900">Sesi & Rekam Medis Terbaru</h2>
                    <p class="text-xs text-ink-700">Diurutkan dari sesi yang paling baru disimpan atau diperbarui agar langsung bisa diakses</p>
                </div>
            </div>
            <a href="{{ route('sessions.index') }}" class="text-xs font-bold text-blush-800 hover:text-blush-900 transition flex items-center gap-1 self-start sm:self-auto bg-blush-50 hover:bg-blush-100 px-3.5 py-1.5 rounded-full border border-blush-200">
                <span>Lihat Semua Sesi</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-blush-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blush-50 text-blush-900 text-[11px] uppercase font-mono tracking-wider border-b border-blush-200/80">
                        <th class="p-3.5 font-bold">Tanggal & Waktu</th>
                        <th class="p-3.5 font-bold">Klien</th>
                        <th class="p-3.5 font-bold">Status Sesi</th>
                        <th class="p-3.5 font-bold">Catatan & Pembayaran</th>
                        <th class="p-3.5 text-right font-bold">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blush-100 text-xs bg-white">
                    @php
                        $avatarTints = [
                            'bg-blush-100 text-blush-800 border-blush-200',
                            'bg-violet-100 text-violet-800 border-violet-200',
                            'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'bg-amber-100 text-amber-900 border-amber-200',
                        ];
                    @endphp
                    @forelse($recentAppointments as $idx => $session)
                        <tr class="hover:bg-blush-50/50 transition-colors">
                            <td class="p-3.5 text-ink-900 whitespace-nowrap">
                                <span class="font-bold block text-sm">
                                    {{ \Carbon\Carbon::parse($session->session_date)->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-[11px] text-ink-700 font-mono tabular-nums">{{ $session->start_time }} - {{ $session->end_time }}</span>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl border flex items-center justify-center font-serif font-bold text-xs flex-shrink-0 {{ $avatarTints[$idx % count($avatarTints)] }}">
                                        <span x-show="!privacyMode">{{ mb_substr($session->medicalCase?->client?->full_name ?? 'K', 0, 1) }}</span>
                                        <svg x-show="privacyMode" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-bold text-ink-900 block text-sm truncate">
                                            <span x-show="!privacyMode">{{ $session->medicalCase?->client?->full_name ?? 'Klien' }}</span>
                                            <span x-show="privacyMode" x-cloak class="font-mono text-blush-800">Klien Terlindung</span>
                                        </span>
                                        <span class="text-[11px] font-mono text-ink-700 block truncate">
                                            {{ $session->medicalCase?->client?->client_code ?? '-' }}
                                            @if($session->medicalCase?->title)
                                                <span x-show="!privacyMode">• {{ \Illuminate\Support\Str::limit($session->medicalCase->title, 36) }}</span>
                                                <span x-show="privacyMode" x-cloak class="italic text-ink-500">• Keluhan disembunyikan</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if($session->status == 'done' || $session->status == 'Completed')
                                    <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] px-2.5 py-1 rounded-full font-bold inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Selesai
                                    </span>
                                @elseif($session->status == 'scheduled')
                                    <span class="bg-violet-50 text-violet-800 border border-violet-200 text-[11px] px-2.5 py-1 rounded-full font-bold inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-violet-600"></span>
                                        Terjadwal
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-800 border border-slate-300 text-[11px] px-2.5 py-1 rounded-full font-bold">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @php
                                        $hasNotes = $session->sessionNote !== null
                                            || !empty(trim((string) $session->subjective_complaint))
                                            || !empty(trim((string) $session->dynamic_notes));
                                    @endphp
                                    @if($hasNotes)
                                        <span class="bg-blush-100 text-blush-900 border border-blush-200 text-[11px] px-2.5 py-0.5 rounded-full font-bold">
                                            Catatan Tersimpan
                                        </span>
                                    @else
                                        <span class="bg-slate-100 text-slate-700 border border-slate-300 text-[11px] px-2.5 py-0.5 rounded-full font-semibold">
                                            Belum Diisi
                                        </span>
                                    @endif

                                    @if($session->payment_status === 'paid')
                                        <span class="bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] px-2.5 py-0.5 rounded-full font-bold">
                                            Lunas
                                        </span>
                                    @elseif($session->payment_status === 'waived')
                                        <span class="bg-slate-100 text-slate-800 border border-slate-300 text-[11px] px-2.5 py-0.5 rounded-full font-semibold">
                                            Bebas Biaya
                                        </span>
                                    @else
                                        <span class="bg-amber-50 text-amber-900 border border-amber-300 text-[11px] px-2.5 py-0.5 rounded-full font-bold">
                                            Belum Lunas
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('sessions.show', $session->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blush-50 hover:bg-blush-500 text-blush-800 hover:text-white border border-blush-200 hover:border-blush-500 font-bold text-xs transition-colors">
                                    <span>Buka Rekam Medis</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-ink-700 text-sm">
                                <p class="mb-2">Belum ada sesi konsultasi yang terjadwal.</p>
                                <a href="{{ route('sessions.create') }}" class="text-blush-700 font-bold underline hover:text-blush-900">
                                    Buat Sesi Konsultasi Baru →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Row 3 (Full-Width 2 Columns): ICD-10 Distribution & Vertical Weekly Workload Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 ICD-10 Diagnoses -->
        <div class="pastel-card-white rounded-3xl p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-blush-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blush-100 text-blush-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-ink-900">Distribusi Diagnosa Klinis</h2>
                        <p class="text-xs text-ink-700">Top 5 klasifikasi ICD-10 kasus yang ditangani</p>
                    </div>
                </div>
                <span class="text-[11px] font-mono font-bold px-3 py-1 rounded-full bg-blush-100 text-blush-800 border border-blush-200">
                    ICD-10
                </span>
            </div>
            
            @if(count($icd10Distribution) > 0)
                @php
                    $max = max(collect($icd10Distribution)->pluck('total')->toArray() ?: [1]);
                    $barColors = [
                        'bg-blush-500',
                        'bg-violet-400',
                        'bg-emerald-400',
                        'bg-amber-400',
                        'bg-sky-400',
                    ];
                @endphp
                <div class="space-y-3.5">
                    @foreach($icd10Distribution as $index => $item)
                        @php
                            $percentage = round(($item->total / $max) * 100);
                        @endphp
                        <div class="space-y-1.5 p-3 rounded-2xl blush-surface-soft">
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center gap-2 min-w-0 pr-2">
                                    <span class="w-5 h-5 rounded-lg bg-white border border-blush-200 text-blush-800 text-[10px] font-mono font-bold flex items-center justify-center flex-shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="bg-blush-100 text-blush-900 font-mono px-2 py-0.5 rounded-md text-[11px] font-bold flex-shrink-0">
                                        {{ $item->icd10_code }}
                                    </span>
                                    <span class="text-ink-900 font-semibold truncate">
                                        {{ $item->icd10_description ?? 'Diagnosa Klinis' }}
                                    </span>
                                </div>
                                <span class="font-mono font-bold text-blush-800 text-xs flex-shrink-0 tabular-nums">
                                    {{ $item->total }} kasus
                                </span>
                            </div>
                            <div class="w-full bg-white rounded-full h-2.5 overflow-hidden border border-blush-100">
                                <div class="{{ $barColors[$index % count($barColors)] }} h-2.5 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-ink-700 space-y-2">
                    <div class="w-11 h-11 rounded-2xl bg-blush-50 text-blush-500 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-ink-900">Belum ada data diagnosa ICD-10 yang tercatat.</p>
                    <p class="text-xs text-ink-500">Diagnosa akan otomatis terkumpul saat Anda melengkapi rekam medis sesi.</p>
                </div>
            @endif
        </div>

        <!-- Weekly Workload Rhythm (Vertical Pill Bar Chart) -->
        <div class="pastel-card-white rounded-3xl p-6 flex flex-col justify-between space-y-5">
            <div class="flex items-center justify-between border-b border-blush-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-ink-900">Ritme Beban Kerja Sesi</h2>
                        <p class="text-xs text-ink-700">Distribusi intensitas sesi per hari minggu ini</p>
                    </div>
                </div>
                <span class="text-[11px] font-mono font-bold px-3 py-1 rounded-full bg-violet-100 text-violet-800 border border-violet-200">
                    Minggu Ini
                </span>
            </div>

            @php
                $days = [
                    now()->startOfWeek()->format('Y-m-d') => 'Sen',
                    now()->startOfWeek()->addDay()->format('Y-m-d') => 'Sel',
                    now()->startOfWeek()->addDays(2)->format('Y-m-d') => 'Rab',
                    now()->startOfWeek()->addDays(3)->format('Y-m-d') => 'Kam',
                    now()->startOfWeek()->addDays(4)->format('Y-m-d') => 'Jum',
                    now()->startOfWeek()->addDays(5)->format('Y-m-d') => 'Sab',
                    now()->startOfWeek()->addDays(6)->format('Y-m-d') => 'Min',
                ];
                $todayStr = now()->format('Y-m-d');
                $weeklyTotal = array_sum($workloadData);
                $maxDayCount = max(1, max(array_values($workloadData) ?: [1]));
            @endphp

            <div class="grid grid-cols-7 gap-2 sm:gap-3 items-end pt-4 pb-2 h-52">
                @foreach($days as $dateStr => $dayShort)
                    @php
                        $count = $workloadData[$dateStr] ?? 0;
                        $isToday = ($dateStr === $todayStr);
                        $heightPct = $count > 0 ? max(18, round(($count / $maxDayCount) * 100)) : 8;
                    @endphp
                    <div class="flex flex-col items-center justify-end h-full gap-2">
                        <span class="font-mono text-[11px] font-bold tabular-nums {{ $isToday ? 'text-blush-800 bg-blush-100 px-2 py-0.5 rounded-full' : ($count > 0 ? 'text-ink-900' : 'text-ink-500') }}">
                            {{ $count }}
                        </span>
                        <div class="w-full max-w-[30px] flex-1 bg-blush-50 rounded-full p-1 flex items-end border {{ $isToday ? 'border-blush-300' : 'border-blush-100' }}">
                            <div class="w-full rounded-full transition-all duration-300 {{ $isToday ? 'bg-gradient-to-t from-blush-500 to-blush-400' : ($count > 0 ? 'bg-gradient-to-t from-violet-400 to-blush-300' : 'bg-blush-200/50') }}"
                                 style="height: {{ $heightPct }}%"></div>
                        </div>
                        <span class="text-[11px] font-bold {{ $isToday ? 'text-blush-800' : 'text-ink-700' }}">
                            {{ $dayShort }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-blush-100 flex items-center justify-between text-xs text-ink-700">
                <span>Total Minggu Ini: <strong class="text-blush-800 font-mono">{{ $weeklyTotal }} Sesi</strong></span>
                <span class="text-ink-500">Kapasitas terjaga optimal</span>
            </div>
        </div>
    </div>

    <!-- Row 4 (Full-Width): Upcoming Activities & Seminars -->
    <div class="pastel-card-white rounded-3xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blush-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-serif text-lg font-bold text-ink-900">Kegiatan & Seminar Mendatang</h2>
                    <p class="text-xs text-ink-700">Jadwal narasumber, pelatihan sekolah, psikoedukasi, dan acara komunitas</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('activities.create') }}" class="px-3.5 py-1.5 rounded-full bg-blush-100 hover:bg-blush-200 text-blush-900 border border-blush-200 text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Jadwalkan Kegiatan</span>
                </a>
                <a href="{{ route('activities.index') }}" class="text-xs font-bold text-blush-800 hover:text-blush-900 transition flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        @if($upcomingActivities->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($upcomingActivities as $act)
                    <div class="p-4 rounded-2xl blush-surface-soft hover:border-blush-300 transition-colors space-y-2.5 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-white text-blush-800 border border-blush-200">
                                    {{ $act->event_type_label }}
                                </span>
                                <span class="text-[11px] font-mono text-ink-700">
                                    {{ \Carbon\Carbon::parse($act->start_date)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            <a href="{{ route('activities.show', $act->id) }}" class="font-serif font-bold text-ink-900 text-sm line-clamp-2 mt-2 group-hover:text-blush-700 transition-colors">
                                {{ $act->title }}
                            </a>
                            <div class="text-xs text-ink-700 mt-1 truncate">
                                {{ $act->organization?->name ?: ($act->organizer_name ?: 'Mandiri / Umum') }}
                            </div>
                        </div>

                        <div class="pt-2 border-t border-blush-200/70 flex items-center justify-between text-[11px]">
                            <span class="text-ink-700 font-mono">{{ $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') . ' WIB' : 'Waktu fleksibel' }}</span>
                            <a href="{{ route('activities.show', $act->id) }}" class="text-blush-800 hover:underline font-bold">
                                Detail →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-ink-700 text-xs">
                <span>Belum ada agenda seminar atau kegiatan yang dijadwalkan dalam waktu dekat.</span>
                <a href="{{ route('activities.create') }}" class="text-blush-800 font-bold hover:underline ml-1">Jadwalkan kegiatan pertama →</a>
            </div>
        @endif
    </div>
</div>
