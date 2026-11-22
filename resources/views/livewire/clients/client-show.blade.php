<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight">{{ $client->full_name }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-blush-100 text-blush-700 border border-blush-200">
                    {{ $client->client_code }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">Terdaftar sejak: <span class="text-ink-700 font-mono">{{ $client->created_at->format('d M Y') }}</span></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('clients.index') }}" class="px-3.5 py-2 bg-white hover:bg-blush-100 border border-blush-200 text-ink-700 font-medium rounded-xl transition text-xs sm:text-sm">
                Kembali
            </a>
            <a href="{{ route('clients.edit', $client->id) }}" class="px-3.5 py-2 bg-white hover:bg-blush-100 border border-blush-200 text-blush-700 hover:text-blush-800 font-semibold rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-sm">
                ✏️ <span>Edit Profil</span>
            </a>
            <a href="{{ Route::has('cases.create') ? route('cases.create', ['client_id' => $client->id]) : '#' }}" class="px-4 py-2 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold rounded-xl transition text-xs sm:text-sm shadow-lg shadow-blush-200/50 transform hover:-translate-y-0.5">
                + Buat Kasus Baru
            </a>
        </div>
    </div>

    <!-- Client Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Detail Klien -->
        <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-blush-200">
            <h2 class="font-serif text-base font-semibold text-ink-900 border-b border-blush-200 pb-3 flex items-center justify-between">
                <span>Informasi Demografi</span>
                <a href="{{ route('clients.edit', $client->id) }}" class="text-xs text-blush-600 hover:underline font-mono">
                    Edit
                </a>
            </h2>
            
            <div class="space-y-3.5 text-xs sm:text-sm">
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">NIK KTP</span>
                    <span class="text-ink-700 font-mono">{{ $client->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Jenis Kelamin</span>
                    <span class="text-ink-700 capitalize">{{ $client->gender == 'female' ? 'Perempuan' : ($client->gender == 'male' ? 'Laki-Laki' : ($client->gender ?? '-')) }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Tanggal Lahir & Usia</span>
                    <span class="text-ink-700">
                        {{ $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('d M Y') : '-' }}
                        @if($client->date_of_birth)
                            <span class="font-mono text-blush-700">({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} Tahun)</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Pendidikan Terakhir</span>
                    <span class="text-ink-700">{{ $client->last_education ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Urutan Kelahiran</span>
                    <span class="text-ink-700">{{ $client->sibling_info }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block mb-1">Status Disabilitas</span>
                    @if($client->is_disabled)
                        <div class="space-y-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-800 border border-amber-200">
                                ♿ Penyandang Disabilitas (Difabel)
                            </span>
                            @if(!empty($client->disability_description))
                                <p class="text-xs text-amber-800 italic pl-1">
                                    "{{ $client->disability_description }}"
                                </p>
                            @endif
                        </div>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white text-ink-500 border border-blush-200">
                            Non-Difabel
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">WhatsApp / HP</span>
                    <span class="text-ink-700 font-mono">{{ $client->phone_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Email</span>
                    <span class="text-ink-700">{{ $client->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Pekerjaan</span>
                    <span class="text-ink-700">{{ $client->occupation ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-ink-500 uppercase font-mono tracking-wider block">Alamat Lengkap</span>
                    <span class="text-ink-700 leading-relaxed">{{ $client->address ?? '-' }}</span>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="pt-4 border-t border-blush-200">
                <h3 class="text-[11px] font-mono font-semibold uppercase text-rose-700 tracking-wider mb-2">Kontak Darurat</h3>
                <p class="text-sm font-semibold text-ink-900">{{ $client->emergency_contact_name ?? '-' }}</p>
                <p class="text-xs text-ink-500">{{ $client->emergency_relation ?? '-' }} • <span class="font-mono">{{ $client->emergency_contact_phone ?? '-' }}</span></p>
            </div>
        </div>

        <!-- Main History: Cases & Sessions -->
        <div class="md:col-span-2 space-y-4">
            <h2 class="font-serif text-xl font-semibold text-ink-900 flex items-center justify-between">
                <span>Riwayat Kasus & Rekam Medis</span>
                <span class="text-xs font-mono font-normal px-2.5 py-1 rounded-full bg-blush-100 text-blush-700 border border-blush-200">
                    {{ $client->cases->count() }} Kasus
                </span>
            </h2>

            @forelse ($client->cases as $case)
                <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-blush-200">
                    <div class="flex items-start justify-between border-b border-blush-200 pb-3">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-blush-200 text-blush-700">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                                <h3 class="text-base font-bold text-ink-900">{{ $case->title }}</h3>
                            </div>
                            <p class="text-xs text-ink-500 mt-1">Status: <span class="text-blush-700 font-semibold uppercase font-mono">{{ $case->status }}</span></p>
                        </div>
                        <a href="{{ Route::has('cases.show') ? route('cases.show', $case->id) : '#' }}" class="text-xs font-semibold text-blush-700 hover:text-blush-800 hover:underline">
                            Lihat Kasus &rarr;
                        </a>
                    </div>

                    <p class="text-xs sm:text-sm text-ink-700 leading-relaxed">{{ Str::limit($case->complaint, 180) }}</p>

                    <!-- Daftar Sesi Singkat -->
                    <div class="bg-blush-50 rounded-xl p-3.5 border border-blush-200">
                        <span class="text-xs font-semibold text-ink-500 block mb-2 font-mono">Riwayat Sesi Terjadwal:</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($case->sessions as $session)
                                <a href="{{ route('sessions.show', $session->id) }}" class="px-2.5 py-1 bg-white hover:bg-blush-100 border border-blush-200 hover:border-blush-200 rounded-lg text-xs text-ink-700 hover:text-blush-800 transition">
                                    Sesi #{{ $session->session_number }} ({{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }})
                                </a>
                            @empty
                                <span class="text-xs text-ink-500 italic">Belum ada sesi yang dijadwalkan.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="sanctuary-glass-card rounded-2xl p-8 text-center text-ink-500 border border-blush-200">
                    <p class="mb-3">Klien ini belum memiliki riwayat kasus.</p>
                    <a href="{{ Route::has('cases.create') ? route('cases.create', ['client_id' => $client->id]) : '#' }}" class="inline-flex items-center gap-1.5 text-blush-700 font-semibold underline hover:text-blush-800">
                        + Buat Kasus Baru Sekarang
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
