<div class="p-6 sm:p-8 max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blush-100 text-blush-700 border border-blush-200 uppercase tracking-wider font-mono">
                    {{ $case->category ?? 'Umum' }}
                </span>
                <span class="text-xs font-mono text-ink-500">{{ $case->case_code }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-ink-900 tracking-tight mt-2">{{ $case->title }}</h1>
            <p class="text-sm text-ink-500 mt-1.5">
                Klien: 
                @if($case->client)
                    <a href="{{ route('clients.show', $case->client->id) }}" class="text-blush-600 font-semibold hover:text-blush-800 underline underline-offset-4 transition">
                        {{ $case->client->full_name }}
                        @if(method_exists($case->client, 'trashed') && $case->client->trashed())
                            <span class="text-xs text-rose-700 font-sans ml-1 font-normal">(Dihapus)</span>
                        @endif
                    </a>
                    <span class="font-mono text-xs text-ink-500 ml-1">({{ $case->client->client_code }})</span>
                @else
                    <span class="text-ink-500 italic">Klien tidak ditemukan</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('cases.index') }}" class="sanctuary-glass border border-blush-200 hover:border-blush-200 text-ink-700 hover:text-ink-900 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button wire:click="deleteCase" 
                    wire:confirm="Apakah Anda yakin ingin memindahkan kasus '{{ addslashes($case->title) }}' ke Tong Sampah?" 
                    type="button" 
                    class="sanctuary-glass border border-rose-200 hover:border-rose-200 hover:bg-rose-500/10 text-rose-700 hover:text-rose-700 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2" 
                    title="Hapus Kasus">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Kasus
            </button>
            <!-- Direct Shortcut ke Jadwal Sesi -->
            <a href="{{ Route::has('sessions.create') ? route('sessions.create', ['case_id' => $case->id]) : '#' }}" class="px-5 py-2.5 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold rounded-xl transition text-sm shadow-lg shadow-teal-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Jadwalkan Sesi Baru
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-200 text-emerald-700 rounded-2xl text-sm font-medium flex items-center gap-3 shadow-lg shadow-blush-200/50" role="alert">
            <svg class="w-5 h-5 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar Info Kasus & Status -->
        <div class="lg:col-span-4 space-y-6">
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-blush-200 space-y-6 shadow-2xl">
                <div class="border-b border-blush-200 pb-4">
                    <h2 class="font-serif text-lg font-bold text-ink-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                        Ringkasan Kasus
                    </h2>
                </div>
                
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-ink-500 block mb-2 font-mono">Status Kasus</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach (['active', 'on_hold', 'completed', 'cancelled'] as $st)
                            <button wire:click="updateStatus('{{ $st }}')" type="button" class="px-3 py-2 rounded-xl text-xs font-semibold capitalize transition text-center {{ $case->status === $st ? 'bg-blush-500 text-white font-bold shadow-md shadow-teal-500/25 ring-2 ring-teal-400/40' : 'bg-blush-50 text-ink-500 hover:text-white hover:bg-blush-100 border border-blush-200' }}">
                                {{ $st }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-ink-500 block mb-1.5 font-mono">Keluhan Utama Klien</span>
                    <div class="text-sm text-ink-700 bg-blush-50 p-4 rounded-2xl border border-blush-200 leading-relaxed font-sans">
                        {{ $case->subjective_complaint ?: ($case->complaint ?: 'Tidak ada catatan keluhan utama.') }}
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-ink-500 block mb-1.5 font-mono">Pokok Masalah / Pemicu</span>
                    <div class="text-sm text-ink-700 bg-blush-50 p-4 rounded-2xl border border-blush-200 leading-relaxed font-sans">
                        {{ $case->subjective_problem ?: ($case->goal ?: 'Tidak ada catatan pokok masalah/pemicu.') }}
                    </div>
                </div>
            </div>

            <!-- Form Progress Note Global -->
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-blush-200 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-blush-200 pb-3">
                    <h2 class="font-serif text-lg font-bold text-ink-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Evaluasi Perkembangan
                    </h2>
                    <span class="text-xs text-blush-600 font-mono">Global</span>
                </div>
                <p class="text-xs text-ink-500 leading-relaxed">Catat sintesis kemajuan kognitif & emosional klien dari seluruh rangkaian sesi di sini.</p>
                <textarea wire:model="progressNote" id="progressNote" rows="5" placeholder="Catat ringkasan kemajuan/progres klien dari seluruh rangkaian terapi di sini..." class="w-full bg-blush-50 border border-blush-200 focus:border-blush-200 focus:ring-1 focus:ring-blush-400 rounded-2xl p-4 text-sm text-ink-900 placeholder-ink-400 transition leading-relaxed"></textarea>
                <button wire:click="updateProgress" type="button" class="w-full py-2.5 bg-white hover:bg-blush-100 text-blush-700 hover:text-blush-800 border border-blush-200 font-bold rounded-xl text-sm transition shadow-sm hover:shadow-teal-500/10">
                    Simpan Evaluasi
                </button>
            </div>
        </div>

        <!-- Main Timeline Sesi Konseling -->
        <div class="lg:col-span-8 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="font-serif text-2xl font-bold text-ink-900 tracking-tight">Timeline Sesi Konseling</h2>
                    <span class="px-3 py-1 rounded-full bg-blush-100 border border-blush-200 text-blush-700 font-mono text-xs font-bold">
                        {{ $case->sessions->count() }} Sesi
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($case->sessions as $session)
                    <div class="sanctuary-glass-card rounded-3xl p-6 border border-blush-200 hover:border-blush-200 transition-all duration-300 space-y-4 shadow-xl group">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-blush-200 pb-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-2xl bg-blush-100 border border-blush-200 text-blush-700 font-bold font-mono text-base flex items-center justify-center shadow-inner shrink-0">
                                    #{{ $session->session_number }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-ink-900 group-hover:text-blush-800 transition">Sesi Ke-{{ $session->session_number }}</h3>
                                    </div>
                                    <p class="text-xs text-ink-500 mt-1 flex flex-wrap items-center gap-2 font-mono">
                                        <span>📅 {{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                                        <span>•</span>
                                        <span>⏰ {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider font-mono {{ $session->status === 'completed' ? 'bg-emerald-500/10 text-emerald-700 border border-emerald-200' : ($session->status === 'cancelled' ? 'bg-rose-500/10 text-rose-700 border border-rose-200' : 'bg-white text-ink-700 border border-blush-200') }}">
                                    {{ $session->status }}
                                </span>
                            </div>
                        </div>

                        <!-- Dynamic Notes Summary -->
                        @php
                            $dynamicSummary = $session->dynamic_notes ?: ($session->sessionNote?->objective ?: '');
                        @endphp
                        @if(!empty(trim($dynamicSummary)))
                            <div class="bg-blush-50 p-4 rounded-2xl border border-blush-200 text-sm text-ink-700 space-y-1">
                                <span class="text-[11px] font-bold text-blush-600 uppercase tracking-wider font-mono block">Ringkasan Sesi / Dinamika Psikologis:</span>
                                <p class="leading-relaxed line-clamp-3">{{ $dynamicSummary }}</p>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                            <div class="text-xs font-mono text-ink-500 flex items-center gap-2">
                                <span>Biaya:</span>
                                <span class="text-ink-900 font-semibold">Rp {{ number_format($session->fee, 0, ',', '.') }}</span>
                                @if($session->payment_status === 'paid')
                                    <span class="text-emerald-700 font-bold bg-emerald-500/10 border border-emerald-200 px-2 py-0.5 rounded-md text-[11px]">✓ Lunas</span>
                                @else
                                    <span class="text-amber-800 font-medium bg-amber-500/10 border border-amber-200 px-2 py-0.5 rounded-md text-[11px]">Belum Lunas</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <button wire:click="deleteSession('{{ $session->id }}')" 
                                        wire:confirm="Apakah Anda yakin ingin memindahkan Sesi Ke-{{ $session->session_number }} ke Tong Sampah?" 
                                        type="button" 
                                        class="text-xs text-rose-700 hover:text-rose-700 hover:underline flex items-center gap-1 transition" 
                                        title="Hapus Sesi Ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                                <a href="{{ Route::has('sessions.show') ? route('sessions.show', $session->id) : '#' }}" class="text-xs text-blush-600 hover:text-blush-800 font-bold flex items-center gap-1.5 group-hover:translate-x-1 transition">
                                    <span>Kelola Catatan SOAP & Biaya</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sanctuary-glass-card rounded-3xl p-12 text-center border border-blush-200 space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-blush-100 border border-blush-200 text-blush-600 flex items-center justify-center mx-auto text-2xl">
                            📅
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-base font-serif font-bold text-ink-900">Belum Ada Sesi Konsultasi</h3>
                            <p class="text-xs text-ink-500 max-w-sm mx-auto">Belum ada sesi konsultasi yang dijadwalkan untuk kasus ini. Klik tombol di bawah untuk membuat sesi baru.</p>
                        </div>
                        <div class="pt-2">
                            <a href="{{ Route::has('sessions.create') ? route('sessions.create', ['case_id' => $case->id]) : '#' }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-teal-500/20">
                                + Jadwalkan Sesi Baru
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
