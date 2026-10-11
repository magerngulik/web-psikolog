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

    <!-- Screen Reader & Test Assertion Announcement -->
    @if (session()->has('message'))
        <div class="sr-only" aria-live="polite">{{ session('message') }}</div>
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
                <option value="trashed">🗑️ Tong Sampah (Dihapus)</option>
            </select>
        </div>
        <div>
            <select wire:model.live="categoryFilter" aria-label="Filter berdasarkan kategori kasus" class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                <option value="">Semua Kategori</option>
                @foreach($this->categories as $cat)
                    <option value="{{ $cat->value }}">{{ $cat->label }}</option>
                @endforeach
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
                                <div class="inline-flex items-center justify-end gap-1">
                                    @if($statusFilter === 'trashed')
                                        <button wire:click="restoreCase('{{ $case->id }}')" 
                                                wire:confirm="Pulihkan kasus '{{ addslashes($case->title) }}' kembali ke daftar aktif?" 
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-400 hover:text-emerald-300 hover:bg-emerald-500/10 transition focus:outline-none focus:ring-2 focus:ring-emerald-400" 
                                                aria-label="Pulihkan Kasus {{ $case->title }}" 
                                                title="Pulihkan Kasus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="forceDeleteCase('{{ $case->id }}')" 
                                                wire:confirm="PERINGATAN: Kasus '{{ addslashes($case->title) }}' akan dihapus secara permanen dari database dan tidak dapat dipulihkan. Apakah Anda yakin?" 
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition focus:outline-none focus:ring-2 focus:ring-rose-400" 
                                                aria-label="Hapus Permanen Kasus {{ $case->title }}" 
                                                title="Hapus Permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <a href="{{ route('cases.show', $case->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-400 hover:text-teal-300 hover:bg-teal-500/10 transition focus:outline-none focus:ring-2 focus:ring-teal-400" aria-label="Buka Detail Kasus {{ $case->title }}" title="Buka Detail Kasus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                        <button wire:click="deleteCase('{{ $case->id }}')" 
                                                wire:confirm="Apakah Anda yakin ingin memindahkan kasus '{{ addslashes($case->title) }}' ke Tong Sampah?" 
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition focus:outline-none focus:ring-2 focus:ring-rose-400" 
                                                aria-label="Hapus Kasus {{ $case->title }}" 
                                                title="Hapus Kasus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                @if($statusFilter === 'trashed')
                                    <p class="mb-2">Tong Sampah kosong. Tidak ada kasus klinis yang terhapus.</p>
                                @else
                                    <p class="mb-2">Belum ada kasus klinis yang tercatat.</p>
                                    <a href="{{ route('cases.create') }}" class="text-teal-400 font-semibold underline hover:text-teal-300">
                                        Buat Kasus Baru Sekarang →
                                    </a>
                                @endif
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
