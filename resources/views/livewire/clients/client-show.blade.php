<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">{{ $client->full_name }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/10 text-teal-300 border border-teal-500/30">
                    {{ $client->client_code }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Terdaftar sejak: <span class="text-slate-300 font-mono">{{ $client->created_at->format('d M Y') }}</span></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('clients.index') }}" class="px-3.5 py-2 bg-spruce-900/80 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-medium rounded-xl transition text-xs sm:text-sm">
                Kembali
            </a>
            <a href="{{ route('clients.edit', $client->id) }}" class="px-3.5 py-2 bg-spruce-900/80 hover:bg-spruce-800 border border-teal-500/20 text-teal-300 hover:text-teal-200 font-semibold rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-sm">
                ✏️ <span>Edit Profil</span>
            </a>
            <a href="{{ Route::has('cases.create') ? route('cases.create', ['client_id' => $client->id]) : '#' }}" class="px-4 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition text-xs sm:text-sm shadow-lg shadow-teal-950/60 transform hover:-translate-y-0.5">
                + Buat Kasus Baru
            </a>
        </div>
    </div>

    <!-- Client Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Detail Klien -->
        <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-teal-500/15">
            <h2 class="font-serif text-base font-semibold text-white border-b border-teal-500/10 pb-3 flex items-center justify-between">
                <span>Informasi Demografi</span>
                <a href="{{ route('clients.edit', $client->id) }}" class="text-xs text-teal-400 hover:underline font-mono">
                    Edit
                </a>
            </h2>
            
            <div class="space-y-3.5 text-xs sm:text-sm">
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">NIK KTP</span>
                    <span class="text-slate-200 font-mono">{{ $client->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Jenis Kelamin</span>
                    <span class="text-slate-200 capitalize">{{ $client->gender == 'female' ? 'Perempuan' : ($client->gender == 'male' ? 'Laki-Laki' : ($client->gender ?? '-')) }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Tanggal Lahir & Usia</span>
                    <span class="text-slate-200">
                        {{ $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('d M Y') : '-' }}
                        @if($client->date_of_birth)
                            <span class="font-mono text-teal-300">({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} Tahun)</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Pendidikan Terakhir</span>
                    <span class="text-slate-200">{{ $client->last_education ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Urutan Kelahiran</span>
                    <span class="text-slate-200">{{ $client->sibling_info }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block mb-1">Status Disabilitas</span>
                    @if($client->is_disabled)
                        <div class="space-y-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                ♿ Penyandang Disabilitas (Difabel)
                            </span>
                            @if(!empty($client->disability_description))
                                <p class="text-xs text-amber-200/90 italic pl-1">
                                    "{{ $client->disability_description }}"
                                </p>
                            @endif
                        </div>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-spruce-900 text-slate-400 border border-teal-500/10">
                            Non-Difabel
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">WhatsApp / HP</span>
                    <span class="text-slate-200 font-mono">{{ $client->phone_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Email</span>
                    <span class="text-slate-200">{{ $client->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Pekerjaan</span>
                    <span class="text-slate-200">{{ $client->occupation ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 uppercase font-mono tracking-wider block">Alamat Lengkap</span>
                    <span class="text-slate-300 leading-relaxed">{{ $client->address ?? '-' }}</span>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="pt-4 border-t border-teal-500/10">
                <h3 class="text-[11px] font-mono font-semibold uppercase text-rose-300 tracking-wider mb-2">Kontak Darurat</h3>
                <p class="text-sm font-semibold text-white">{{ $client->emergency_contact_name ?? '-' }}</p>
                <p class="text-xs text-slate-400">{{ $client->emergency_relation ?? '-' }} • <span class="font-mono">{{ $client->emergency_contact_phone ?? '-' }}</span></p>
            </div>
        </div>

        <!-- Main History: Cases & Sessions -->
        <div class="md:col-span-2 space-y-4">
            <h2 class="font-serif text-xl font-semibold text-white flex items-center justify-between">
                <span>Riwayat Kasus & Rekam Medis</span>
                <span class="text-xs font-mono font-normal px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/20">
                    {{ $client->cases->count() }} Kasus
                </span>
            </h2>

            @forelse ($client->cases as $case)
                <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-teal-500/15">
                    <div class="flex items-start justify-between border-b border-teal-500/10 pb-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-spruce-900 border border-teal-500/20 text-teal-300">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                                <h3 class="text-base font-bold text-white">{{ $case->title }}</h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Status: <span class="text-teal-300 font-semibold uppercase font-mono">{{ $case->status }}</span></p>
                        </div>
                        <a href="{{ Route::has('cases.show') ? route('cases.show', $case->id) : '#' }}" class="text-xs font-semibold text-teal-300 hover:text-teal-200 hover:underline">
                            Lihat Kasus &rarr;
                        </a>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-300/90 leading-relaxed">{{ Str::limit($case->complaint, 180) }}</p>

                    <!-- Daftar Sesi Singkat -->
                    <div class="bg-spruce-950/80 rounded-xl p-3.5 border border-teal-500/15">
                        <span class="text-xs font-semibold text-slate-400 block mb-2 font-mono">Riwayat Sesi Terjadwal:</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($case->sessions as $session)
                                <a href="{{ route('sessions.show', $session->id) }}" class="px-2.5 py-1 bg-spruce-900 hover:bg-spruce-800 border border-teal-500/20 hover:border-teal-400/40 rounded-lg text-xs text-slate-300 hover:text-teal-300 transition">
                                    Sesi #{{ $session->session_number }} ({{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }})
                                </a>
                            @empty
                                <span class="text-xs text-slate-500 italic">Belum ada sesi yang dijadwalkan.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="sanctuary-glass-card rounded-2xl p-8 text-center text-slate-400 border border-teal-500/15">
                    <p class="mb-3">Klien ini belum memiliki riwayat kasus.</p>
                    <a href="{{ Route::has('cases.create') ? route('cases.create', ['client_id' => $client->id]) : '#' }}" class="inline-flex items-center gap-1.5 text-teal-300 font-semibold underline hover:text-teal-200">
                        + Buat Kasus Baru Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
