<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">Manajemen Kasus Klinis</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Daftar rekam medis, episode penanganan, dan program intervensi psikologis klien</p>
        </div>
        <a href="{{ route('cases.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-semibold text-xs sm:text-sm rounded-xl transition shadow-lg shadow-teal-950/40 gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Buat Kasus Baru</span>
        </a>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 rounded-2xl text-xs sm:text-sm font-medium flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" aria-label="Cari kasus berdasarkan judul, keluhan, atau nama klien" type="text" placeholder="Cari judul kasus, keluhan utama, atau nama klien..." class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
        </div>
        <div>
            <select wire:model.live="statusFilter" aria-label="Filter berdasarkan status kasus" class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                <option value="">Semua Status Kasus</option>
                <option value="active">Active (Aktif)</option>
                <option value="on_hold">On Hold (Ditunda)</option>
                <option value="completed">Completed (Selesai)</option>
                <option value="cancelled">Cancelled (Dibatalkan)</option>
            </select>
        </div>
        <div>
            <select wire:model.live="categoryFilter" aria-label="Filter berdasarkan kategori kasus" class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                <option value="">Semua Kategori</option>
                <option value="Anxiety">Anxiety / Kecemasan</option>
                <option value="Depression">Depresi</option>
                <option value="Relationship">Hubungan / Pasangan</option>
                <option value="Family">Keluarga</option>
                <option value="Work & Career">Karir / Pekerjaan</option>
                <option value="Personal Growth">Personal Growth</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="sanctuary-glass-card rounded-2xl overflow-hidden border border-teal-500/15 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-spruce-950/80 border-b border-teal-500/15 text-teal-300/80 text-[11px] font-mono uppercase tracking-wider">
                        <th class="p-4">Klien</th>
                        <th class="p-4">Judul Kasus & Kategori</th>
                        <th class="p-4">Status Kasus</th>
                        <th class="p-4">Total Sesi</th>
                        <th class="p-4">Tanggal Pembukaan</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10 text-xs sm:text-sm">
                    @forelse ($cases as $case)
                        <tr class="hover:bg-teal-500/[0.04] transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-teal-500/15 border border-teal-500/25 flex items-center justify-center font-serif text-teal-300 font-bold text-sm flex-shrink-0">
                                        {{ mb_substr($case->client->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('clients.show', $case->client->id) }}" class="font-semibold text-white hover:text-teal-300 transition block text-sm">
                                            {{ $case->client->full_name }}
                                        </a>
                                        <span class="text-[11px] text-teal-400/80 font-mono">{{ $case->client->client_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('cases.show', $case->id) }}" class="font-semibold text-slate-200 hover:text-teal-300 transition block text-sm">
                                    {{ $case->title }}
                                </a>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-medium bg-spruce-900 border border-teal-500/20 text-teal-300">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($case->status == 'active')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Aktif
                                    </span>
                                @elseif($case->status == 'completed')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-950/80 text-sky-300 border border-sky-500/30 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                        Selesai
                                    </span>
                                @elseif($case->status == 'on_hold')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-950/80 text-amber-300 border border-amber-500/30 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Ditunda
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-950/80 text-rose-300 border border-rose-500/30 inline-flex items-center gap-1.5">
                                        {{ ucfirst($case->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-slate-300 text-xs">
                                <span class="font-mono font-bold text-white text-sm">{{ $case->sessions_count }}</span> Sesi
                            </td>
                            <td class="p-4 text-slate-400 text-xs font-mono">
                                {{ $case->created_at->format('d M Y') }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('cases.show', $case->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-teal-300 hover:bg-teal-500/10 transition focus:outline-none focus:ring-2 focus:ring-teal-400" aria-label="Buka Detail Kasus {{ $case->title }}" title="Buka Detail Kasus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <p class="mb-2">Belum ada kasus klinis yang tercatat.</p>
                                <a href="{{ route('cases.create') }}" class="text-teal-400 font-semibold underline hover:text-teal-300">
                                    Buat Kasus Baru Sekarang →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-teal-500/10">
            {{ $cases->links() }}
        </div>
    </div>
</div>
