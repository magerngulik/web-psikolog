<div class="p-6 sm:p-8 max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono mb-2">
                <span>Portofolio & Pengabdian</span>
                <span>•</span>
                <span>Kegiatan & Seminar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">Kegiatan, Seminar, & Psikoedukasi</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola agenda narasumber, pelatihan sekolah & komunitas, surat undangan, dan dokumentasi kegiatan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.index') }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Mitra Organisasi
            </a>
            <a href="{{ route('activities.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition text-sm shadow-lg shadow-teal-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Jadwalkan Kegiatan Baru
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

    <!-- 3 Vitals Tiles -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-teal-400">Agenda Bulan Ini</span>
                <span class="p-2 rounded-xl bg-teal-500/10 text-teal-400">📅</span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl sm:text-4xl font-serif font-bold text-white font-mono">{{ $eventsThisMonth }}</span>
                <span class="text-xs text-slate-400 font-sans">kegiatan dijadwalkan</span>
            </div>
            <div class="mt-2 text-xs text-slate-500 font-sans">Seminar, workshop & psikoedukasi aktif</div>
        </div>

        <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">Peserta Teredukasi</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">👥</span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl sm:text-4xl font-serif font-bold text-emerald-300 font-mono">{{ number_format($audienceThisMonth, 0, ',', '.') }}</span>
                <span class="text-xs text-slate-400 font-sans">estimasi audiens</span>
            </div>
            <div class="mt-2 text-xs text-slate-500 font-sans">Jangkauan dampak psikoedukasi masyarakat</div>
        </div>

        <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 shadow-xl relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400">Honorarium Kegiatan (Lunas)</span>
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400">💰</span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-serif font-bold text-white font-mono">Rp {{ number_format($paidRevenueThisMonth, 0, ',', '.') }}</span>
            </div>
            <div class="mt-2 text-xs text-teal-400/90 font-mono">✓ Masuk ke akumulasi pendapatan Dashboard</div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="sanctuary-glass-card rounded-3xl p-5 border border-teal-500/15 shadow-xl space-y-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="w-full md:w-96 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari topik kegiatan, lokasi, kode, atau mitra..." class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-slate-500 font-sans">
            </div>

            <div class="w-full md:w-auto flex flex-wrap items-center gap-3">
                <!-- Filter Status -->
                <select wire:model.live="statusFilter" class="bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs font-mono focus:outline-none focus:border-teal-400">
                    <option value="all">Semua Status</option>
                    <option value="scheduled">Terjadwal</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>

                <!-- Filter Tipe Acara -->
                <select wire:model.live="typeFilter" class="bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs font-mono focus:outline-none focus:border-teal-400">
                    <option value="all">Semua Jenis Acara</option>
                    <option value="seminar">Seminar</option>
                    <option value="webinar">Webinar</option>
                    <option value="workshop">Workshop / Pelatihan</option>
                    <option value="psychoeducation">Psikoedukasi</option>
                    <option value="talkshow">Talkshow</option>
                    <option value="training">Training</option>
                </select>

                <!-- Filter Mitra -->
                <select wire:model.live="organization_id" class="bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs font-mono focus:outline-none focus:border-teal-400">
                    <option value="">Semua Mitra Organisasi</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Activity Cards / Grid -->
    <div class="space-y-4">
        @forelse ($activities as $act)
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 hover:border-teal-500/35 transition-all duration-300 shadow-xl space-y-4 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-teal-500/10 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-teal-300 font-bold font-mono text-sm flex flex-col items-center justify-center shadow-inner shrink-0 text-center leading-none">
                            <span>{{ \Carbon\Carbon::parse($act->start_date)->format('d') }}</span>
                            <span class="text-[10px] uppercase font-sans mt-0.5">{{ \Carbon\Carbon::parse($act->start_date)->format('M') }}</span>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold font-mono bg-teal-500/10 text-teal-300 border border-teal-500/25 uppercase">
                                    {{ $act->event_type_label }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold font-mono bg-spruce-900 text-slate-300 border border-teal-500/10">
                                    {{ $act->delivery_mode_label }}
                                </span>
                                <span class="text-xs font-mono text-slate-500">{{ $act->activity_code }}</span>
                            </div>
                            <a href="{{ route('activities.show', $act->id) }}" class="block text-lg sm:text-xl font-serif font-bold text-white group-hover:text-teal-300 transition mt-1">
                                {{ $act->title }}
                            </a>
                            <div class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-2 font-sans">
                                <span>Penyelenggara / Mitra:</span>
                                @if($act->organization)
                                    <a href="{{ route('activities.index', ['organization_id' => $act->organization->id]) }}" class="text-teal-400 font-semibold hover:underline">
                                        {{ $act->organization->name }}
                                    </a>
                                @else
                                    <span class="text-slate-300 font-medium">{{ $act->organizer_name ?: 'Mandiri / Umum' }}</span>
                                @endif
                                <span>•</span>
                                <span class="font-mono">⏰ {{ $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') : '' }} {{ $act->end_time ? '- ' . \Carbon\Carbon::parse($act->end_time)->format('H:i') . ' WIB' : '' }}</span>
                                @if($act->location_venue)
                                    <span>•</span>
                                    <span>📍 {{ $act->location_venue }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="text-right">
                            <div class="text-xs font-mono text-slate-400">Honorarium:</div>
                            <div class="text-sm font-bold font-mono text-white">
                                Rp {{ number_format($act->fee, 0, ',', '.') }}
                            </div>
                            <div>
                                @if($act->payment_status === 'paid')
                                    <span class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-md inline-block font-mono">✓ Lunas</span>
                                @elseif($act->payment_status === 'waived_pro_bono')
                                    <span class="text-[11px] font-medium text-slate-400 bg-slate-800 border border-slate-700 px-2 py-0.5 rounded-md inline-block font-mono">Pro Bono</span>
                                @else
                                    <span class="text-[11px] font-medium text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-md inline-block font-mono">Belum Lunas</span>
                                @endif
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider font-mono {{ match($act->status) {
                            'completed' => 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20',
                            'cancelled' => 'bg-rose-500/10 text-rose-300 border border-rose-500/20',
                            default => 'bg-teal-500/10 text-teal-300 border border-teal-500/20',
                        } }}">
                            {{ $act->status }}
                        </span>
                    </div>
                </div>

                <!-- Footer Info & Attachments Preview -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-400">
                    <div class="flex flex-wrap items-center gap-3 font-mono">
                        <span class="text-teal-400">Peran: <strong class="text-white">{{ $act->role_label }}</strong></span>
                        @if($act->estimated_audience)
                            <span>•</span>
                            <span>Target: <strong class="text-slate-300">{{ $act->estimated_audience }} Peserta</strong> ({{ $act->target_audience ?: 'Umum' }})</span>
                        @endif
                        @if($act->skp_points)
                            <span>•</span>
                            <span class="text-amber-400">SKP: <strong class="text-amber-300">{{ $act->skp_points }}</strong></span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5 font-mono text-[11px]">
                            @if($act->invitationLetters->count() > 0)
                                <span class="text-teal-300 bg-teal-500/10 px-2 py-0.5 rounded border border-teal-500/20">📄 Surat</span>
                            @endif
                            @if($act->certificates->count() > 0)
                                <span class="text-emerald-300 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">🎖️ Sertifikat</span>
                            @endif
                            @if($act->photos->count() > 0)
                                <span class="text-amber-300 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">📸 {{ $act->photos->count() }} Foto</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('activities.edit', $act->id) }}" class="p-2 rounded-xl bg-spruce-950/80 hover:bg-spruce-900 text-slate-400 hover:text-teal-300 border border-teal-500/20 hover:border-teal-500/40 transition" title="Edit Kegiatan" aria-label="Edit Kegiatan">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button wire:click="delete('{{ $act->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus kegiatan '{{ addslashes($act->title) }}'?" type="button" class="p-2 rounded-xl bg-spruce-950/80 hover:bg-rose-950/60 text-slate-400 hover:text-rose-400 border border-teal-500/20 hover:border-rose-500/30 transition" title="Hapus Kegiatan" aria-label="Hapus Kegiatan">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            <a href="{{ route('activities.show', $act->id) }}" class="text-teal-400 hover:text-teal-300 font-bold flex items-center gap-1 group-hover:translate-x-1 transition font-sans ml-1 text-xs">
                                <span>Detail</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="sanctuary-glass-card rounded-3xl p-12 text-center border border-teal-500/15 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mx-auto text-2xl">
                    🎤
                </div>
                <h3 class="text-base font-serif font-bold text-white">Belum Ada Kegiatan atau Seminar</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Catat agenda seminar di sekolah, workshop komunitas, dan psikoedukasi organisasi Anda secara rapi.</p>
                <div class="pt-2">
                    <a href="{{ route('activities.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl text-xs transition shadow-lg shadow-teal-500/20">
                        + Jadwalkan Kegiatan Baru
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if ($activities->hasPages())
        <div class="sanctuary-glass-card rounded-2xl p-4 border border-teal-500/15">
            {{ $activities->links() }}
        </div>
    @endif
</div>

