<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">Direktori & Profil Klien</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Kelola data rekam identitas, demografi, dan kontak darurat pasien</p>
        </div>
        <a href="{{ route('clients.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-semibold text-xs sm:text-sm rounded-xl transition shadow-lg shadow-teal-950/40 gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Klien Baru</span>
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" aria-label="Cari klien berdasarkan nama, kode, atau nomor HP" type="text" placeholder="Cari nama lengkap, kode klien, atau WhatsApp/HP..." class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
        </div>
        <div>
            <select wire:model.live="genderFilter" aria-label="Filter berdasarkan jenis kelamin" class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                <option value="">Semua Jenis Kelamin</option>
                <option value="female">Perempuan</option>
                <option value="male">Laki-Laki</option>
                <option value="other">Lainnya</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="sanctuary-glass-card rounded-2xl overflow-hidden border border-teal-500/15 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-spruce-950/80 border-b border-teal-500/15 text-teal-300/80 text-[11px] font-mono uppercase tracking-wider">
                        <th class="p-4">Identitas & Kode Klien</th>
                        <th class="p-4">Kontak Darurat / HP</th>
                        <th class="p-4">Gender & Usia</th>
                        <th class="p-4">Kasus Aktif</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10 text-xs sm:text-sm">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-teal-500/[0.04] transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-teal-500/15 border border-teal-500/25 flex items-center justify-center font-serif text-teal-300 font-bold text-sm flex-shrink-0">
                                        {{ mb_substr($client->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('clients.show', $client->id) }}" class="font-semibold text-white hover:text-teal-300 transition block text-sm">
                                            {{ $client->full_name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[11px] text-teal-400/80 font-mono">{{ $client->client_code ?? 'NO-CODE' }}</span>
                                            @if($client->is_disabled)
                                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-amber-500/15 text-amber-300 border border-amber-500/30 font-medium">
                                                    Difabel
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-slate-300">
                                <div class="font-mono text-xs">{{ $client->phone_number ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $client->email ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-300 capitalize text-xs">
                                <span>{{ $client->gender == 'female' ? 'Perempuan' : ($client->gender == 'male' ? 'Laki-Laki' : ($client->gender ?? '-')) }}</span>
                                @if($client->date_of_birth)
                                    <span class="text-slate-400 block text-[11px] font-mono">({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} tahun)</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-spruce-900 border border-teal-500/20 text-teal-300">
                                    {{ $client->cases_count }} Kasus
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <a href="{{ route('clients.show', $client->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-teal-300 hover:bg-teal-500/10 transition focus:outline-none focus:ring-2 focus:ring-teal-400" aria-label="Lihat Profil Klien {{ $client->full_name }}" title="Lihat Profil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('clients.edit', $client->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-amber-300 hover:bg-amber-500/10 transition focus:outline-none focus:ring-2 focus:ring-amber-400" aria-label="Edit Profil Klien {{ $client->full_name }}" title="Edit Profil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <button wire:click="deleteClient('{{ $client->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus data klien ini?" class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition focus:outline-none focus:ring-2 focus:ring-rose-400" aria-label="Hapus Klien {{ $client->full_name }}" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                <p class="mb-2">Belum ada data klien yang terdaftar.</p>
                                <a href="{{ route('clients.create') }}" class="text-teal-400 font-semibold underline hover:text-teal-300">
                                    Tambah Klien Baru Sekarang →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-teal-500/10">
            {{ $clients->links() }}
        </div>
    </div>
</div>
