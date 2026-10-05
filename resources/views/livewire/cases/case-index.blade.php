<div class="p-6 space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Kasus Klinis</h1>
            <p class="text-sm text-slate-400">Daftar rekam medis & program terapi aktif klien</p>
        </div>
        <a href="{{ route('cases.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Buat Kasus Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul kasus, keluhan, atau nama klien..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <select wire:model.live="statusFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Status</option>
                <option value="active">Active (Aktif)</option>
                <option value="on_hold">On Hold (Ditunda)</option>
                <option value="completed">Completed (Selesai)</option>
                <option value="cancelled">Cancelled (Dibatalkan)</option>
            </select>
        </div>
        <div>
            <select wire:model.live="categoryFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
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

    <!-- Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Klien</th>
                        <th class="p-4">Judul Kasus & Kategori</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Jumlah Sesi</th>
                        <th class="p-4">Tanggal Dibuat</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse ($cases as $case)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-4">
                                <a href="{{ route('clients.show', $case->client->id) }}" class="font-semibold text-teal-400 hover:underline block">
                                    {{ $case->client->full_name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $case->client->client_code }}</span>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('cases.show', $case->id) }}" class="font-semibold text-white hover:text-teal-400 block">
                                    {{ $case->title }}
                                </a>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium bg-slate-700 text-slate-300">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="p-4">
                                @php
                                    $statusClasses = [
                                        'active' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                        'on_hold' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'completed' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                        'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold border uppercase {{ $statusClasses[$case->status] ?? 'bg-slate-700 text-slate-300' }}">
                                    {{ $case->status }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-300">
                                <span class="font-semibold text-white">{{ $case->sessions_count }}</span> Sesi
                            </td>
                            <td class="p-4 text-slate-400 text-xs">
                                {{ $case->created_at->format('d M Y') }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('cases.show', $case->id) }}" class="p-2 text-slate-400 hover:text-teal-400 inline-block transition" title="Buka Detail Kasus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Belum ada data kasus medis yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-700">
            {{ $cases->links() }}
        </div>
    </div>
</div>
