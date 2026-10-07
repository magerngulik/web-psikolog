<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Web Psikolog' }} — Clinical Practice & Records</title>
        
        <!-- Google Fonts: Fraunces (Editorial Display) + Plus Jakarta Sans (Humanist Body) + JetBrains Mono (Clinical Data) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            serif: ['Fraunces', 'Georgia', 'serif'],
                            sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                            mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                        },
                        colors: {
                            sanctuary: {
                                50: '#f0fdf9',
                                100: '#ccfbef',
                                200: '#9af5df',
                                300: '#5ee7cb',
                                400: '#2cd0b2',
                                500: '#13b499',
                                600: '#0b907c',
                                700: '#0c7365',
                                800: '#0e5c52',
                                900: '#104c44',
                                950: '#052b27',
                            },
                            spruce: {
                                700: '#1d323a',
                                800: '#15252c',
                                850: '#0f1c22',
                                900: '#0b161b',
                                950: '#070f13',
                            },
                        },
                    },
                },
            }
        </script>
        <style>
            [x-cloak] { display: none !important; }
            /* Atmospheric Sanctuary Ambient Background */
            .sanctuary-bg {
                background-color: #080f14;
                background-image: 
                    radial-gradient(circle 800px at 5% 0%, rgba(19, 180, 153, 0.09), transparent 60%),
                    radial-gradient(circle 600px at 95% 20%, rgba(45, 212, 191, 0.05), transparent 50%),
                    radial-gradient(circle 700px at 80% 95%, rgba(217, 119, 6, 0.04), transparent 55%),
                    linear-gradient(to bottom, rgba(8, 15, 20, 0.8), rgba(8, 15, 20, 0.98));
                background-attachment: fixed;
            }
            .sanctuary-glass {
                background: rgba(15, 28, 34, 0.72);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(45, 212, 191, 0.12);
            }
            .sanctuary-glass:hover {
                border-color: rgba(45, 212, 191, 0.22);
            }
            .sanctuary-glass-card {
                background: linear-gradient(135deg, rgba(21, 37, 44, 0.75) 0%, rgba(15, 28, 34, 0.85) 100%);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(94, 231, 203, 0.12);
                box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.04);
            }
        </style>
        @livewireStyles
    </head>
    <body class="sanctuary-bg font-sans text-slate-200 antialiased min-h-screen selection:bg-teal-500/30 selection:text-teal-200">
        <!-- Skip to Main Content Link (WCAG 2.4.1) -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2.5 focus:bg-teal-400 focus:text-slate-950 focus:font-bold focus:rounded-xl focus:shadow-2xl focus:ring-4 focus:ring-teal-400/30">
            Lewati ke konten utama
        </a>

        <!-- Global Sanctuary Header -->
        <header class="sanctuary-glass sticky top-0 z-50 px-4 sm:px-6 py-3 border-b border-teal-500/10 transition-all duration-300">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <!-- Brand Mark -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-teal-400/80 rounded-xl p-1 transition" aria-label="Web Psikolog - Beranda">
                        <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500/25 to-emerald-700/30 border border-teal-400/30 flex items-center justify-center shadow-lg shadow-teal-950/60 group-hover:border-teal-400/50 group-hover:scale-105 transition-all duration-300">
                            <span class="text-xl filter drop-shadow">🧠</span>
                            <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-slate-950"></div>
                        </div>
                        <div>
                            <span class="font-serif text-lg sm:text-xl font-semibold tracking-tight text-white group-hover:text-teal-200 transition-colors">
                                Web Psikolog
                            </span>
                            <span class="block text-[10px] font-mono tracking-widest uppercase text-teal-400/70 -mt-0.5">
                                Clinical Sanctuary
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav aria-label="Navigasi Utama" class="hidden md:flex items-center gap-1 text-xs font-medium p-1 rounded-2xl bg-spruce-950/60 border border-teal-500/10">
                    <a href="{{ route('dashboard') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('dashboard') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">📊</span> <span>Dashboard</span>
                    </a>
                    <a href="{{ route('clients.index') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('clients.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('clients.*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">👥</span> <span>Klien</span>
                    </a>
                    <a href="{{ route('cases.index') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('cases.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('cases.*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">📋</span> <span>Kasus</span>
                    </a>
                    <a href="{{ route('sessions.index') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('sessions.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('sessions.*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">📅</span> <span>Sesi</span>
                    </a>
                    <a href="{{ route('activities.index') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('activities.*') || request()->routeIs('organizations.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('activities.*') || request()->routeIs('organizations.*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">🎤</span> <span>Kegiatan & Seminar</span>
                    </a>
                    <a href="{{ route('reports.logbook') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('reports.*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">📈</span> <span>Logbook SKP</span>
                    </a>
                    <a href="{{ route('settings.references') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('settings.references*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('settings.references*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">🗂️</span> <span>Referensi</span>
                    </a>
                    <a href="{{ route('settings.profile') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('settings.profile*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('settings.profile*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">👤</span> <span>Profil</span>
                    </a>
                    <a href="{{ route('settings.security') }}" 
                       class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 {{ request()->routeIs('settings.security*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/20 text-teal-300 border border-teal-500/30 font-semibold shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}"
                       {!! request()->routeIs('settings.security*') ? 'aria-current="page"' : '' !!}>
                        <span aria-hidden="true" class="text-xs">🔑</span> <span>PIN</span>
                    </a>
                </nav>

                <!-- Practice Pill -->
                <div class="flex items-center gap-2">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-spruce-900/80 border border-teal-500/20 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" aria-hidden="true"></span>
                        <span class="text-slate-300 font-medium">Praktik Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Subnav Bar -->
            <div class="flex md:hidden items-center justify-between gap-1 overflow-x-auto pt-3 pb-1 border-t border-teal-500/10 text-xs font-medium scrollbar-none">
                <a href="{{ route('dashboard') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Dashboard</a>
                <a href="{{ route('clients.index') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('clients.*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Klien</a>
                <a href="{{ route('cases.index') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('cases.*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Kasus</a>
                <a href="{{ route('sessions.index') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('sessions.*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Sesi</a>
                <a href="{{ route('activities.index') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('activities.*') || request()->routeIs('organizations.*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Kegiatan</a>
                <a href="{{ route('reports.logbook') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">SKP</a>
                <a href="{{ route('settings.references') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('settings.references*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Ref</a>
                <a href="{{ route('settings.profile') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('settings.profile*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">Profil</a>
                <a href="{{ route('settings.security') }}" class="px-2.5 py-1 rounded-lg {{ request()->routeIs('settings.security*') ? 'bg-teal-500/20 text-teal-300 font-semibold' : 'text-slate-400' }}">PIN</a>
            </div>
        </header>

        <main id="main-content" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
