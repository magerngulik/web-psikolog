<div class="p-6 space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Klien</h1>
            <p class="text-sm text-slate-400">Kelola data profil dan direktori klien Anda</p>
        </div>
        <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Klien Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, kode klien, atau nomor HP..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <select wire:model.live="genderFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Jenis Kelamin</option>
                <option value="male">Laki-Laki</option>
                <option value="female">Perempuan</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Kode & Nama Klien</th>
                        <th class="p-4">Kontak</th>
                        <th class="p-4">Gender / Umur</th>
                        <th class="p-4">Total Kasus</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-4">
                                <a href="{{ route('clients.show', $client->id) }}" class="font-semibold text-teal-400 hover:underline block">
                                    {{ $client->full_name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $client->client_code ?? 'NO-CODE' }}</span>
                            </td>
                            <td class="p-4 text-slate-300">
                                <div>{{ $client->phone_number ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $client->email ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-300 capitalize">
                                {{ $client->gender ?? '-' }} 
                                @if($client->date_of_birth)
                                    <span class="text-slate-500 text-xs">({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} th)</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-300 border border-slate-600">
                                    {{ $client->cases_count }} Kasus
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('clients.show', $client->id) }}" class="p-2 text-slate-400 hover:text-teal-400 inline-block transition" title="Lihat Profil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button wire:click="deleteClient('{{ $client->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus klien ini?" class="p-2 text-slate-400 hover:text-rose-400 inline-block transition" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                Belum ada data klien yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-700">
            {{ $clients->links() }}
        </div>
    </div>
</div>

