<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight">Penjadwalan & Sesi Konseling</h1>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">Kelola agenda konsultasi, rekam medis klinis, dan administrasi tarif sesi</p>
        </div>
        <a href="{{ route('sessions.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-semibold text-xs sm:text-sm rounded-xl transition shadow-lg shadow-blush-200/50 gap-2 transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Jadwalkan Sesi Baru</span>
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" aria-label="Cari nama klien atau judul kasus" type="text" placeholder="Cari nama klien, kode klien, atau judul kasus..." class="w-full bg-white border border-blush-200 rounded-xl pl-10 pr-4 py-2.5 text-ink-900 placeholder-ink-400 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
        </div>
        <div>
            <x-date-picker wire:model.live="dateFilter" placeholder="Filter tanggal sesi..." />
        </div>
        <div>
            <select wire:model.live="statusFilter" aria-label="Filter berdasarkan status sesi" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                <option value="">Semua Status Sesi</option>
                <option value="scheduled">Scheduled (Terjadwal)</option>
                <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                <option value="in_progress">In Progress (Berlangsung)</option>
                <option value="done">Done (Selesai)</option>
                <option value="cancelled">Cancelled (Batal)</option>
                <option value="no_show">No Show (Tidak Hadir)</option>
                <option value="trashed">🗑️ Tong Sampah (Dihapus)</option>
            </select>
        </div>
    </div>

    <!-- Table Container -->
    <div class="sanctuary-glass-card rounded-2xl overflow-hidden border border-blush-200 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blush-50 border-b border-blush-200 text-blush-700 text-[11px] font-mono uppercase tracking-wider">
                        <th class="p-4">Sesi & Klien</th>
                        <th class="p-4">Kasus / Masalah</th>
                        <th class="p-4">Waktu Konseling</th>
                        <th class="p-4">Status & Rekam Medis</th>
                        <th class="p-4">Administrasi Tarif</th>
                        <th class="p-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blush-100 text-xs sm:text-sm">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-blush-50/70 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blush-100 border border-blush-200 text-blush-700 font-mono font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        #{{ $session->session_number }}
                                    </span>
                                    <div>
                                        @if($session->medicalCase?->client)
                                            <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="font-semibold text-ink-900 hover:text-blush-800 transition block text-sm">
                                                {{ $session->medicalCase->client->full_name }}
                                                @if(method_exists($session->medicalCase->client, 'trashed') && $session->medicalCase->client->trashed())
                                                    <span class="text-[10px] text-rose-700 font-sans ml-1 font-normal">(Dihapus)</span>
                                                @endif
                                            </a>
                                            <span class="text-[11px] text-blush-600 font-mono">{{ $session->medicalCase->client->client_code }}</span>
                                        @else
                                            <span class="font-semibold text-ink-500 block text-sm italic">
                                                {{ $session->medicalCase?->client?->full_name ?? 'Klien Tidak Ditemukan' }}
                                            </span>
                                            <span class="text-[11px] text-ink-500 font-mono">-</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($session->medicalCase)
                                    <a href="{{ route('cases.show', $session->medicalCase->id) }}" class="font-semibold text-ink-700 hover:text-blush-800 transition block text-xs sm:text-sm">
                                        {{ $session->medicalCase->title }}
                                    </a>
                                    <span class="text-[11px] text-ink-500">{{ $session->medicalCase->category ?? 'Konseling Umum' }}</span>
                                @else
                                    <span class="font-semibold text-ink-500 block text-xs sm:text-sm italic">
                                        Kasus Tidak Ditemukan
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-xs">
                                <span class="text-ink-900 font-medium block">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                                <span class="text-[11px] text-ink-500 font-mono">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @if($session->status == 'done' || $session->status == 'Completed')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Selesai
                                        </span>
                                    @elseif($session->status == 'scheduled')
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                            Terjadwal
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white text-ink-700 border border-blush-200">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $paymentClasses = [
                                        'unpaid' => 'text-amber-800 bg-amber-50 border-amber-200 font-semibold',
                                        'paid' => 'text-emerald-700 bg-emerald-50 border-emerald-200 font-semibold',
                                        'waived' => 'text-slate-700 bg-slate-100 border-slate-200',
                                    ];
                                @endphp
                                <span class="inline-block text-[11px] px-2 py-0.5 rounded-md border capitalize {{ $paymentClasses[$session->payment_status] ?? 'text-ink-500' }}">
                                    ● {{ $session->payment_status }}
                                </span>
                                <span class="block text-xs font-mono text-ink-700 mt-0.5">Rp {{ number_format($session->fee, 0, ',', '.') }}</span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    @if($statusFilter === 'trashed')
                                        <button wire:click="restoreSession('{{ $session->id }}')" 
                                                wire:confirm="Pulihkan Sesi Ke-{{ $session->session_number }} kembali ke agenda aktif?" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-ink-500 hover:text-emerald-700 hover:bg-emerald-500/10 transition focus:outline-none focus:ring-2 focus:ring-emerald-400" 
                                                aria-label="Pulihkan Sesi Ke-{{ $session->session_number }}" 
                                                title="Pulihkan Sesi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                        <button wire:click="forceDeleteSession('{{ $session->id }}')" 
                                                wire:confirm="PERINGATAN: Sesi Ke-{{ $session->session_number }} akan dihapus secara permanen dari database dan tidak dapat dipulihkan. Apakah Anda yakin?" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-ink-500 hover:text-rose-700 hover:bg-rose-500/10 transition focus:outline-none focus:ring-2 focus:ring-rose-400" 
                                                aria-label="Hapus Permanen Sesi Ke-{{ $session->session_number }}" 
                                                title="Hapus Permanen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <a href="{{ route('sessions.show', $session->id) }}" 
                                           class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-blush-100 hover:bg-blush-200/80 text-blush-700 border border-blush-200 text-xs font-medium transition shadow-sm" 
                                           title="Buka Rekam Medis Sesi">
                                            <span>📝</span>
                                            <span>Rekam Medis</span>
                                        </a>
                                        <button wire:click="deleteSession('{{ $session->id }}')" 
                                                wire:confirm="Apakah Anda yakin ingin memindahkan Sesi Ke-{{ $session->session_number }} ke Tong Sampah?" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-ink-500 hover:text-rose-700 hover:bg-rose-500/10 transition focus:outline-none focus:ring-2 focus:ring-rose-400" 
                                                aria-label="Hapus Sesi Ke-{{ $session->session_number }}" 
                                                title="Hapus Sesi">
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
                            <td colspan="6" class="p-8 text-center text-ink-500">
                                @if($statusFilter === 'trashed')
                                    <p class="mb-2">Tong Sampah kosong. Tidak ada sesi konseling yang terhapus.</p>
                                @else
                                    <p class="mb-2">Belum ada agenda sesi konsultasi.</p>
                                    <a href="{{ route('sessions.create') }}" class="text-blush-600 font-semibold underline hover:text-blush-800">
                                        Jadwalkan Sesi Baru Sekarang →
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-blush-200">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
