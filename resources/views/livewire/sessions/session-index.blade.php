<div class="p-6 space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Penjadwalan & Sesi Konseling</h1>
            <p class="text-sm text-slate-400">Kelola jadwal konsultasi, rekam medis sesi, dan status pembayaran</p>
        </div>
        <a href="{{ route('sessions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Jadwalkan Sesi Baru
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
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama klien, ID klien, atau judul kasus..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <input wire:model.live="dateFilter" type="date" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <select wire:model.live="statusFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Status Sesi</option>
                <option value="scheduled">Scheduled (Terjadwal)</option>
                <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                <option value="in_progress">In Progress (Berlangsung)</option>
                <option value="done">Done (Selesai)</option>
                <option value="cancelled">Cancelled (Batal)</option>
                <option value="no_show">No Show (Klien Tidak Hadir)</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Sesi & Klien</th>
                        <th class="p-4">Kasus / Topik</th>
                        <th class="p-4">Waktu Konseling</th>
                        <th class="p-4">Status Sesi</th>
                        <th class="p-4">Pembayaran</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-teal-500/10 border border-teal-500/30 text-teal-400 font-bold flex items-center justify-center text-xs">
                                        #{{ $session->session_number }}
                                    </span>
                                    <div>
                                        <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="font-semibold text-teal-400 hover:underline block">
                                            {{ $session->medicalCase->client->full_name }}
                                        </a>
                                        <span class="text-xs text-slate-500 font-mono">{{ $session->medicalCase->client->client_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('cases.show', $session->medicalCase->id) }}" class="font-semibold text-white hover:text-teal-400 block">
                                    {{ $session->medicalCase->title }}
                                </a>
                                <span class="text-xs text-slate-400">{{ $session->medicalCase->category ?? 'Umum' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-white font-medium block">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                                </span>
                            </td>
                            <td class="p-4">
                                @php
                                    $statusClasses = [
                                        'scheduled' => 'bg-slate-700 text-slate-300 border-slate-600',
                                        'confirmed' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'in_progress' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                                        'done' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                        'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'no_show' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                    ];
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border uppercase {{ $statusClasses[$session->status] ?? 'bg-slate-700 text-slate-300' }}">
                                        {{ $session->status }}
                                    </span>
                                    @if($session->is_locked)
                                        <span title="Rekam medis terkunci">🔒</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $paymentClasses = [
                                        'unpaid' => 'text-rose-400',
                                        'paid' => 'text-teal-400 font-semibold',
                                        'waived' => 'text-slate-400',
                                    ];
                                @endphp
                                <span class="text-xs capitalize {{ $paymentClasses[$session->payment_status] ?? 'text-slate-400' }}">
                                    ● {{ $session->payment_status }}
                                </span>
                                <span class="block text-xs font-mono text-slate-300">Rp {{ number_format($session->fee, 0, ',', '.') }}</span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('sessions.show', $session->id) }}" class="p-2 text-slate-400 hover:text-teal-400 inline-block transition" title="Buka Detail Sesi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Belum ada jadwal sesi konsultasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-700">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
