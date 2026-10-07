<div class="p-6 sm:p-8 max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono">
                    {{ $case->category ?? 'Umum' }}
                </span>
                <span class="text-xs font-mono text-slate-400">{{ $case->case_code }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight mt-2">{{ $case->title }}</h1>
            <p class="text-sm text-slate-400 mt-1.5">
                Klien: 
                <a href="{{ route('clients.show', $case->client->id) }}" class="text-teal-400 font-semibold hover:text-teal-300 underline underline-offset-4 transition">
                    {{ $case->client->full_name }}
                </a>
                <span class="font-mono text-xs text-slate-400 ml-1">({{ $case->client->client_code }})</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cases.index') }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <!-- Direct Shortcut ke Jadwal Sesi -->
            <a href="{{ Route::has('sessions.create') ? route('sessions.create', ['case_id' => $case->id]) : '#' }}" class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition text-sm shadow-lg shadow-teal-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Jadwalkan Sesi Baru
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 rounded-2xl text-sm font-medium flex items-center gap-3 backdrop-blur-sm shadow-lg shadow-emerald-950/20" role="alert">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar Info Kasus & Status -->
        <div class="lg:col-span-4 space-y-6">
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 space-y-6 shadow-2xl">
                <div class="border-b border-teal-500/10 pb-4">
                    <h2 class="font-serif text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        Ringkasan Kasus
                    </h2>
                </div>
                
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2 font-mono">Status Kasus</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach (['active', 'on_hold', 'completed', 'cancelled'] as $st)
                            <button wire:click="updateStatus('{{ $st }}')" type="button" class="px-3 py-2 rounded-xl text-xs font-semibold capitalize transition text-center {{ $case->status === $st ? 'bg-teal-500 text-slate-950 font-bold shadow-md shadow-teal-500/25 ring-2 ring-teal-400/40' : 'bg-spruce-950/60 text-slate-400 hover:text-white hover:bg-spruce-900 border border-teal-500/10' }}">
                                {{ $st }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5 font-mono">Keluhan Utama Klien</span>
                    <div class="text-sm text-slate-200 bg-spruce-950/70 p-4 rounded-2xl border border-teal-500/10 leading-relaxed font-sans">
                        {{ $case->subjective_complaint ?: ($case->complaint ?: 'Tidak ada catatan keluhan utama.') }}
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5 font-mono">Pokok Masalah / Pemicu</span>
                    <div class="text-sm text-slate-200 bg-spruce-950/70 p-4 rounded-2xl border border-teal-500/10 leading-relaxed font-sans">
                        {{ $case->subjective_problem ?: ($case->goal ?: 'Tidak ada catatan pokok masalah/pemicu.') }}
                    </div>
                </div>
            </div>

            <!-- Form Progress Note Global -->
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-3">
                    <h2 class="font-serif text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Evaluasi Perkembangan
                    </h2>
                    <span class="text-xs text-teal-400 font-mono">Global</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">Catat sintesis kemajuan kognitif & emosional klien dari seluruh rangkaian sesi di sini.</p>
                <textarea wire:model="progressNote" id="progressNote" rows="5" placeholder="Catat ringkasan kemajuan/progres klien dari seluruh rangkaian terapi di sini..." class="w-full bg-spruce-950/80 border border-teal-500/20 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 rounded-2xl p-4 text-sm text-white placeholder-slate-500 transition leading-relaxed"></textarea>
                <button wire:click="updateProgress" type="button" class="w-full py-2.5 bg-spruce-900 hover:bg-spruce-800 text-teal-300 hover:text-teal-200 border border-teal-500/25 font-bold rounded-xl text-sm transition shadow-sm hover:shadow-teal-500/10">
                    Simpan Evaluasi
                </button>
            </div>
        </div>

        <!-- Main Timeline Sesi Konseling -->
        <div class="lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="font-serif text-2xl font-bold text-white tracking-tight">Timeline Sesi Konseling</h2>
                    <span class="px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-300 font-mono text-xs font-bold">
                        {{ $case->sessions->count() }} Sesi
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($case->sessions as $session)
                    <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 hover:border-teal-500/35 transition-all duration-300 space-y-4 shadow-xl group">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-teal-500/10 pb-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-teal-300 font-bold font-mono text-base flex items-center justify-center shadow-inner shrink-0">
                                    #{{ $session->session_number }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-white group-hover:text-teal-300 transition">Sesi Ke-{{ $session->session_number }}</h3>
                                        @if($session->is_locked)
                                            <span class="text-teal-300 text-[11px] font-semibold bg-teal-500/10 px-2.5 py-0.5 rounded-lg border border-teal-500/25 flex items-center gap-1 font-mono">
                                                🔒 Locked
                                            </span>
                                        @else
                                            <span class="text-amber-300 text-[11px] font-semibold bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-500/20 font-mono">
                                                ✎ Draft
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-2 font-mono">
                                        <span>📅 {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                                        <span>•</span>
                                        <span>⏰ {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider font-mono {{ $session->status === 'completed' ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : ($session->status === 'cancelled' ? 'bg-rose-500/10 text-rose-300 border border-rose-500/20' : 'bg-spruce-900 text-slate-300 border border-teal-500/10') }}">
                                    {{ $session->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Dynamic Notes Summary -->
                        @php
                            $dynamicSummary = $session->dynamic_notes ?: ($session->sessionNote?->objective ?: '');
                        @endphp
                        @if(!empty(trim($dynamicSummary)))
                            <div class="bg-spruce-950/60 p-4 rounded-2xl border border-teal-500/10 text-sm text-slate-300 space-y-1">
                                <span class="text-[11px] font-bold text-teal-400/80 uppercase tracking-wider font-mono block">Ringkasan Sesi / Dinamika Psikologis:</span>
                                <p class="leading-relaxed line-clamp-3">{{ $dynamicSummary }}</p>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                            <div class="text-xs font-mono text-slate-400 flex items-center gap-2">
                                <span>Biaya:</span>
                                <span class="text-white font-semibold">Rp {{ number_format($session->fee, 0, ',', '.') }}</span>
                                @if($session->payment_status === 'paid')
                                    <span class="text-emerald-400 font-bold bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-md text-[11px]">✓ Lunas</span>
                                @else
                                    <span class="text-amber-400 font-medium bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-md text-[11px]">Belum Lunas</span>
                                @endif
                            </div>
                            <a href="{{ Route::has('sessions.show') ? route('sessions.show', $session->id) : '#' }}" class="text-xs text-teal-400 hover:text-teal-300 font-bold flex items-center gap-1.5 group-hover:translate-x-1 transition">
                                <span>Kelola Catatan SOAP & Biaya</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="sanctuary-glass-card rounded-3xl p-12 text-center border border-teal-500/15 space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mx-auto text-2xl">
                            📅
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-base font-serif font-bold text-white">Belum Ada Sesi Konsultasi</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto">Belum ada sesi konsultasi yang dijadwalkan untuk kasus ini. Klik tombol di bawah untuk membuat sesi baru.</p>
                        </div>
                        <div class="pt-2">
                            <a href="{{ Route::has('sessions.create') ? route('sessions.create', ['case_id' => $case->id]) : '#' }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 rounded-xl text-xs font-bold transition shadow-lg shadow-teal-500/20">
                                + Jadwalkan Sesi Baru
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
