<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard Praktik Psikologi</h1>
            <p class="text-sm text-slate-400">Ringkasan aktivitas klinis, statistik klien, dan agenda konseling hari ini</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-teal-400 font-semibold rounded-xl transition text-sm">
                + Klien Baru
            </a>
            <a href="{{ route('cases.create') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-teal-400 font-semibold rounded-xl transition text-sm">
                + Kasus Baru
            </a>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-bold rounded-xl transition text-sm shadow-lg shadow-teal-500/20">
                + Jadwal Sesi
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Total Klien Terdaftar</span>
                <span class="p-2 rounded-xl bg-teal-500/10 text-teal-400 text-lg">👥</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-white">{{ $totalClients }}</span>
                <span class="text-xs text-slate-400 block mt-1">Klien terdaftar di sistem</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Kasus Aktif</span>
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 text-lg">📋</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-white">{{ $activeCases }}</span>
                <span class="text-xs text-slate-400 block mt-1">Program intervensi aktif</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Sesi Hari Ini</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-400 text-lg">📅</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-white">{{ $todaySessions->count() }}</span>
                <span class="text-xs text-slate-400 block mt-1">Jadwal konseling hari ini</span>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase">Sesi Mendatang</span>
                <span class="p-2 rounded-xl bg-sky-500/10 text-sky-400 text-lg">⏳</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-extrabold text-white">{{ $upcomingSessionsCount }}</span>
                <span class="text-xs text-slate-400 block mt-1">Terjadwal & dikonfirmasi</span>
            </div>
        </div>
    </div>

    <!-- Agenda Sesi Hari Ini -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-700 pb-3">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span>🗓️</span> Agenda Konseling Hari Ini ({{ now()->format('d M Y') }})
            </h3>
            <a href="{{ route('sessions.index') }}" class="text-xs text-teal-400 font-semibold hover:underline">
                Lihat Semua Sesi &rarr;
            </a>
        </div>

        <div class="space-y-3">
            @forelse ($todaySessions as $session)
                <div class="bg-slate-900/60 border border-slate-700/60 rounded-xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:border-slate-600 transition">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 font-bold flex items-center justify-center text-sm">
                            #{{ $session->session_number }}
                        </span>
                        <div>
                            <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="font-bold text-white hover:text-teal-400 block">
                                {{ $session->medicalCase->client->full_name }}
                            </a>
                            <span class="text-xs text-slate-400">{{ $session->medicalCase->title }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-mono text-slate-300 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                            🕒 {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                        </span>
                        <a href="{{ route('sessions.show', $session->id) }}" class="px-3 py-1.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-lg text-xs transition">
                            Kelola Sesi
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-sm">
                    Tidak ada jadwal konseling untuk hari ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
