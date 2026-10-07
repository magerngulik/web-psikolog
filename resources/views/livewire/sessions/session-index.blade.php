<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">Penjadwalan & Sesi Konseling</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Kelola agenda konsultasi, rekam medis klinis, dan administrasi tarif sesi</p>
        </div>
        <a href="{{ route('sessions.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-semibold text-xs sm:text-sm rounded-xl transition shadow-lg shadow-teal-950/40 gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Jadwalkan Sesi Baru</span>
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
            <input wire:model.live.debounce.300ms="search" aria-label="Cari nama klien atau judul kasus" type="text" placeholder="Cari nama klien, kode klien, atau judul kasus..." class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
        </div>
        <div>
            <input wire:model.live="dateFilter" aria-label="Filter berdasarkan tanggal sesi" type="date" class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
        </div>
        <div>
            <select wire:model.live="statusFilter" aria-label="Filter berdasarkan status sesi" class="w-full bg-spruce-900/80 border border-teal-500/15 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                <option value="">Semua Status Sesi</option>
                <option value="scheduled">Scheduled (Terjadwal)</option>
                <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                <option value="in_progress">In Progress (Berlangsung)</option>
                <option value="done">Done (Selesai)</option>
                <option value="cancelled">Cancelled (Batal)</option>
                <option value="no_show">No Show (Tidak Hadir)</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="sanctuary-glass-card rounded-2xl overflow-hidden border border-teal-500/15 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-spruce-950/80 border-b border-teal-500/15 text-teal-300/80 text-[11px] font-mono uppercase tracking-wider">
                        <th class="p-4">Sesi & Klien</th>
                        <th class="p-4">Kasus / Masalah</th>
                        <th class="p-4">Waktu Konseling</th>
                        <th class="p-4">Status & Rekam Medis</th>
                        <th class="p-4">Administrasi Tarif</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10 text-xs sm:text-sm">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-teal-500/[0.04] transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-teal-500/15 border border-teal-500/30 text-teal-300 font-mono font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        #{{ $session->session_number }}
                                    </span>
                                    <div>
                                        <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="font-semibold text-white hover:text-teal-300 transition block text-sm">
                                            {{ $session->medicalCase->client->full_name }}
                                        </a>
                                        <span class="text-[11px] text-teal-400/80 font-mono">{{ $session->medicalCase->client->client_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('cases.show', $session->medicalCase->id) }}" class="font-semibold text-slate-200 hover:text-teal-300 transition block text-xs sm:text-sm">
                                    {{ $session->medicalCase->title }}
                                </a>
                                <span class="text-[11px] text-slate-400">{{ $session->medicalCase->category ?? 'Konseling Umum' }}</span>
                            </td>
                            <td class="p-4 text-xs">
                                <span class="text-white font-medium block">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @if($session->status == 'done' || $session->status == 'Completed')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Selesai
                                        </span>
                                    @elseif($session->status == 'scheduled')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-950/80 text-blue-300 border border-blue-500/30 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                            Terjadwal
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-spruce-900 text-slate-300 border border-teal-500/20">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    @endif

                                    @if($session->is_locked)
                                        <span class="text-[10px] text-emerald-400/90 font-mono font-medium flex items-center gap-1">
                                            <span>🔒</span> Terkunci (Final)
                                        </span>
                                    @else
                                        <span class="text-[10px] text-amber-400/90 font-mono font-medium flex items-center gap-1">
                                            <span>✏️</span> Draft Terbuka
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $paymentClasses = [
                                        'unpaid' => 'text-rose-400 bg-rose-500/10 border-rose-500/20',
                                        'paid' => 'text-emerald-300 bg-emerald-500/10 border-emerald-500/20 font-semibold',
                                        'waived' => 'text-slate-400 bg-slate-800 border-slate-700',
                                    ];
                                @endphp
                                <span class="inline-block text-[11px] px-2 py-0.5 rounded-md border capitalize {{ $paymentClasses[$session->payment_status] ?? 'text-slate-400' }}">
                                    ● {{ $session->payment_status }}
                                </span>
                                <span class="block text-xs font-mono text-slate-300 mt-0.5">Rp {{ number_format($session->fee, 0, ',', '.') }}</span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('sessions.show', $session->id) }}" 
                                   class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 text-xs font-medium transition shadow-sm" 
                                   title="Buka Rekam Medis Sesi">
                                    <span>📝</span>
                                    <span>Rekam Medis</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <p class="mb-2">Belum ada agenda sesi konsultasi.</p>
                                <a href="{{ route('sessions.create') }}" class="text-teal-400 font-semibold underline hover:text-teal-300">
                                    Jadwalkan Sesi Baru Sekarang →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-teal-500/10">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
