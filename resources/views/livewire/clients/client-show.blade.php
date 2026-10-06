<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white">{{ $client->full_name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                    {{ $client->client_code }}
                </span>
            </div>
            <p class="text-sm text-slate-400">Pendaftaran: {{ $client->created_at->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
                Kembali
            </a>
            <a href="{{ route('clients.edit', $client->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-teal-400 hover:text-teal-300 font-semibold rounded-xl transition text-sm flex items-center gap-1.5">
                ✏️ Edit Profil
            </a>
            <a href="{{ Route::has('cases.create') ? route('cases.create', ['client_id' => $client->id]) : '#' }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition text-sm shadow-lg shadow-teal-500/20">
                + Buat Kasus Baru
            </a>
        </div>
    </div>

    <!-- Client Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Detail Klien -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3 flex items-center justify-between">
                <span>Informasi Detail</span>
                <a href="{{ route('clients.edit', $client->id) }}" class="text-xs text-teal-400 hover:underline font-normal">
                    Edit
                </a>
            </h3>
            
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">NIK KTP</span>
                    <span class="text-slate-200 font-mono">{{ $client->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Jenis Kelamin</span>
                    <span class="text-slate-200 capitalize">{{ $client->gender ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Tanggal Lahir / Umur</span>
                    <span class="text-slate-200">
                        {{ $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('d M Y') : '-' }}
                        @if($client->date_of_birth)
                            ({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} Tahun)
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Pendidikan Terakhir</span>
                    <span class="text-slate-200">{{ $client->last_education ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Urutan Kelahiran</span>
                    <span class="text-slate-200">{{ $client->sibling_info }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block mb-1">Status Difabel</span>
                    @if($client->is_disabled)
                        <div class="space-y-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                ♿ Penyandang Disabilitas (Difabel)
                            </span>
                            @if(!empty($client->disability_description))
                                <p class="text-xs text-amber-300/90 italic pl-1">
                                    "{{ $client->disability_description }}"
                                </p>
                            @endif
                        </div>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-400">
                            Non-Difabel
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Telepon / WhatsApp</span>
                    <span class="text-slate-200">{{ $client->phone_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Email</span>
                    <span class="text-slate-200">{{ $client->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Pekerjaan</span>
                    <span class="text-slate-200">{{ $client->occupation ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Alamat</span>
                    <span class="text-slate-200">{{ $client->address ?? '-' }}</span>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="pt-4 border-t border-slate-700">
                <h4 class="text-xs font-semibold uppercase text-rose-400 tracking-wider mb-2">Kontak Darurat</h4>
                <p class="text-sm font-semibold text-white">{{ $client->emergency_contact_name ?? '-' }}</p>
                <p class="text-xs text-slate-400">{{ $client->emergency_relation ?? '-' }} • {{ $client->emergency_contact_phone ?? '-' }}</p>
            </div>
        </div>

        <!-- Main History: Cases & Sessions -->
        <div class="md:col-span-2 space-y-4">
            <h3 class="text-lg font-semibold text-white">Riwayat Kasus / Rekam Medis ({{ $client->cases->count() }})</h3>

            @forelse ($client->cases as $case)
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                    <div class="flex items-start justify-between border-b border-slate-700/60 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-700 text-slate-300">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                                <h4 class="text-base font-bold text-white">{{ $case->title }}</h4>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Status: <span class="text-teal-400 font-semibold uppercase">{{ $case->status }}</span></p>
                        </div>
                        <a href="{{ Route::has('cases.show') ? route('cases.show', $case->id) : '#' }}" class="text-xs text-teal-400 hover:underline">
                            Lihat Kasus &rarr;
                        </a>
                    </div>

                    <p class="text-sm text-slate-300">{{ Str::limit($case->complaint, 150) }}</p>

                    <!-- Daftar Sesi Singkat -->
                    <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-700/40">
                        <span class="text-xs font-semibold text-slate-400 block mb-2">Riwayat Sesi Konseling:</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($case->sessions as $session)
                                <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-lg text-xs text-slate-300">
                                    Sesi #{{ $session->session_number }} ({{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }})
                                </span>
                            @empty
                                <span class="text-xs text-slate-500 italic">Belum ada sesi yang dijadwalkan.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center text-slate-500">
                    Klien ini belum memiliki riwayat kasus. Klik tombol "+ Buat Kasus Baru" untuk memulai.
                </div>
            @endforelse
        </div>
    </div>
</div>

