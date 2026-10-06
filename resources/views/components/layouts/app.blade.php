<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Web Psikolog' }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        @livewireStyles
    </head>
    <body class="bg-slate-900 font-sans text-white antialiased min-h-screen selection:bg-teal-500 selection:text-white">
        <nav class="bg-slate-800 border-b border-slate-700 px-6 py-4 flex items-center justify-between shadow-md sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-white flex items-center gap-2 hover:opacity-90 transition">
                    <span class="text-teal-400 text-2xl">🧠</span> <span>Web Psikolog</span>
                </a>
            </div>
            <div class="flex items-center gap-5 text-sm font-medium text-slate-300">
                <a href="{{ route('dashboard') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    📊 <span>Dashboard</span>
                </a>
                <a href="{{ route('clients.index') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    👥 <span>Klien</span>
                </a>
                <a href="{{ route('cases.index') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    📋 <span>Kasus</span>
                </a>
                <a href="{{ route('sessions.index') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    📅 <span>Sesi</span>
                </a>
                <a href="{{ route('reports.logbook') }}" class="hover:text-teal-400 transition flex items-center gap-1.5 text-teal-400 font-semibold">
                    📈 <span>Logbook SKP</span>
                </a>
                <a href="{{ route('settings.references') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    🗂️ <span>Referensi</span>
                </a>
                <a href="{{ route('settings.profile') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    👤 <span>Profil</span>
                </a>
                <a href="{{ route('settings.security') }}" class="hover:text-teal-400 transition flex items-center gap-1.5">
                    🔑 <span>PIN</span>
                </a>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
