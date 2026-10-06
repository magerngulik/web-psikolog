<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-300">
                    {{ $case->category ?? 'Umum' }}
                </span>
                <h1 class="text-2xl font-bold text-white">{{ $case->title }}</h1>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Klien: <a href="{{ route('clients.show', $case->client->id) }}" class="text-teal-400 font-semibold hover:underline">{{ $case->client->full_name }}</a>
                ({{ $case->client->client_code }})
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('cases.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
                Kembali
            </a>
            <!-- Direct Shortcut ke Modul 5: Jadwal Sesi -->
            <a href="{{ Route::has('sessions.create') ? route('sessions.create', ['case_id' => $case->id]) : '#' }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition text-sm shadow-lg shadow-teal-500/20">
                + Jadwalkan Sesi Baru
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Info Kasus & Status -->
        <div class="space-y-6">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3">Ringkasan Kasus</h3>
                
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block mb-1">Status Kasus</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach (['active', 'on_hold', 'completed', 'cancelled'] as $st)
                            <button wire:click="updateStatus('{{ $st }}')" type="button" class="px-2.5 py-1 rounded-lg text-xs font-semibold capitalize transition {{ $case->status === $st ? 'bg-teal-500 text-slate-900 shadow-md' : 'bg-slate-700 text-slate-400 hover:bg-slate-600' }}">
                                {{ $st }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Keluhan Utama Klien</span>
                    <p class="text-sm text-slate-300 mt-1 bg-slate-900/50 p-3 rounded-xl border border-slate-700/50">{{ $case->subjective_complaint ?: ($case->complaint ?: 'Tidak ada catatan keluhan utama.') }}</p>
                </div>

                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Pokok Masalah / Pemicu</span>
                    <p class="text-sm text-slate-300 mt-1 bg-slate-900/50 p-3 rounded-xl border border-slate-700/50">{{ $case->subjective_problem ?: ($case->goal ?: 'Tidak ada catatan pokok masalah/pemicu.') }}</p>
                </div>
            </div>

            <!-- Form Progress Note Global -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white">Evaluasi Perkembangan Global</h3>
                <textarea wire:model="progressNote" rows="5" placeholder="Catat ringkasan kemajuan/progres klien dari seluruh rangkaian terapi di sini..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition"></textarea>
                <button wire:click="updateProgress" type="button" class="w-full py-2 bg-slate-700 hover:bg-slate-600 text-teal-400 font-semibold rounded-xl text-sm transition">
                    Simpan Evaluasi
                </button>
            </div>
        </div>

        <!-- Main Timeline Sesi Konseling -->
        <div class="md:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Timeline Sesi Konseling ({{ $case->sessions->count() }})</h3>
            </div>

            <div class="space-y-4">
                @forelse ($case->sessions as $session)
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl transition hover:border-slate-600 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 font-bold flex items-center justify-center text-sm">
                                    #{{ $session->session_number }}
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-white">Sesi Ke-{{ $session->session_number }}</h4>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }} • {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-slate-700 text-slate-300">
                                    {{ $session->status }}
                                </span>
                                @if($session->is_locked)
                                    <span class="text-teal-400 text-xs flex items-center gap-1 font-medium bg-teal-500/10 px-2 py-1 rounded-lg border border-teal-500/20">
                                        🔒 Locked
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Summary Catatan Klinis Singkat (Dinamika Psikologis) -->
                        @php
                            $dynamicSummary = $session->dynamic_notes ?: ($session->sessionNote?->objective ?: '');
                        @endphp
                        @if(!empty(trim($dynamicSummary)))
                            <div class="text-sm text-slate-300">
                                <span class="text-xs font-semibold text-slate-500 uppercase block">Ringkasan Sesi:</span>
                                {{ Str::limit($dynamicSummary, 160) }}
                            </div>
                        @endif

                        <div class="flex justify-end pt-2">
                            <a href="{{ Route::has('sessions.show') ? route('sessions.show', $session->id) : '#' }}" class="text-xs text-teal-400 hover:underline font-semibold flex items-center gap-1">
                                Kelola Catatan & Biaya Sesi &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center text-slate-500">
                        Belum ada sesi konsultasi yang dijadwalkan untuk kasus ini. Klik tombol "+ Jadwalkan Sesi Baru" di kanan atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
