<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Web Psikolog' }} — Clinical Practice & Records</title>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="sanctuary-bg font-sans text-slate-200 antialiased min-h-screen selection:bg-teal-500/30 selection:text-teal-200"
          x-data="{ sidebarOpen: false }"
          @keydown.window.escape="sidebarOpen = false">
        <!-- Skip to Main Content Link (WCAG 2.4.1) -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2.5 focus:bg-teal-400 focus:text-slate-950 focus:font-bold focus:rounded-xl focus:shadow-2xl focus:ring-4 focus:ring-teal-400/30">
            Lewati ke konten utama
        </a>

        <!-- Global Toast Notification Container (Top-Right) -->
        <x-toast-notification />

        <!-- Mobile Drawer Backdrop Overlay -->
        <div x-show="sidebarOpen" 
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden"
             aria-hidden="true"></div>

        <!-- Sidebar Navigation Drawer / Pinned Aside -->
        <aside id="sanctuary-sidebar"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col bg-spruce-950/95 lg:bg-spruce-950/90 backdrop-blur-xl border-r border-teal-500/15 shadow-2xl lg:shadow-none transition-transform duration-300 ease-in-out"
               aria-label="Navigasi Utama">
            
            <!-- Sidebar Header / Brand Mark -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-teal-500/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-teal-400/80 rounded-xl p-1 transition" aria-label="Web Psikolog - Beranda">
                    <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500/25 to-emerald-700/30 border border-teal-400/30 flex items-center justify-center shadow-lg shadow-teal-950/60 group-hover:border-teal-400/50 group-hover:scale-105 transition-all duration-300">
                        <span class="text-xl filter drop-shadow">🧠</span>
                        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-slate-950"></div>
                    </div>
                    <div>
                        <span class="font-serif text-lg font-semibold tracking-tight text-white group-hover:text-teal-200 transition-colors">
                            Web Psikolog
                        </span>
                        <span class="block text-[10px] font-mono tracking-widest uppercase text-teal-400/70 -mt-0.5">
                            Clinical Sanctuary
                        </span>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button type="button" 
                        @click="sidebarOpen = false" 
                        class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 border border-teal-500/10 transition focus:outline-none focus:ring-2 focus:ring-teal-400"
                        aria-label="Tutup menu navigasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links Scroll Area -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-7 scrollbar-none" aria-label="Menu Utama">
                
                <!-- Category 1: Layanan Klinis -->
                <div>
                    <div class="px-3 mb-2 flex items-center justify-between text-[11px] font-mono font-semibold uppercase tracking-wider text-teal-400/70">
                        <span>Layanan Klinis</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-teal-500/10 border border-teal-500/20 text-teal-300">Inti</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('dashboard*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('dashboard*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('dashboard*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">📊</span>
                                    <span>Dashboard</span>
                                </div>
                                @if(request()->routeIs('dashboard*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('clients.index') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('clients.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('clients.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('clients.*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">👥</span>
                                    <span>Data Klien</span>
                                </div>
                                @if(request()->routeIs('clients.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cases.index') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('cases.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('cases.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('cases.*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">📋</span>
                                    <span>Kasus Medis</span>
                                </div>
                                @if(request()->routeIs('cases.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sessions.index') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('sessions.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('sessions.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('sessions.*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">📅</span>
                                    <span>Sesi Konseling</span>
                                </div>
                                @if(request()->routeIs('sessions.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 2: Profesi & Komunitas -->
                <div>
                    <div class="px-3 mb-2 flex items-center justify-between text-[11px] font-mono font-semibold uppercase tracking-wider text-teal-400/70">
                        <span>Profesi & Komunitas</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20 text-emerald-300">Kiprah</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('activities.index') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('activities.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('activities.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('activities.*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">🎤</span>
                                    <span>Kegiatan & Seminar</span>
                                </div>
                                @if(request()->routeIs('activities.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('organizations.index') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('organizations.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('organizations.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('organizations.*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">🏛️</span>
                                    <span>Mitra Organisasi</span>
                                </div>
                                @if(request()->routeIs('organizations.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.logbook') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('reports.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('reports.*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">📈</span>
                                    <span>Logbook SKP</span>
                                </div>
                                @if(request()->routeIs('reports.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 3: Pengaturan & Sistem -->
                <div>
                    <div class="px-3 mb-2 flex items-center justify-between text-[11px] font-mono font-semibold uppercase tracking-wider text-teal-400/70">
                        <span>Pengaturan & Sistem</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-amber-300">Admin</span>
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('settings.references') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('settings.references*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('settings.references*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('settings.references*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">🗂️</span>
                                    <span>Master Referensi</span>
                                </div>
                                @if(request()->routeIs('settings.references*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.profile') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('settings.profile*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('settings.profile*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('settings.profile*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">👤</span>
                                    <span>Profil Praktik</span>
                                </div>
                                @if(request()->routeIs('settings.profile*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.security') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('settings.security*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('settings.security*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('settings.security*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">🔑</span>
                                    <span>Keamanan PIN</span>
                                </div>
                                @if(request()->routeIs('settings.security*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.backup') }}"
                               class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-400/60 {{ request()->routeIs('settings.backup*') ? 'bg-gradient-to-r from-teal-500/20 to-emerald-500/10 text-teal-200 border-l-2 border-teal-400 font-semibold shadow-inner' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent' }}"
                               {!! request()->routeIs('settings.backup*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <span class="text-base {{ request()->routeIs('settings.backup*') ? 'scale-110' : 'group-hover:scale-110' }} transition-transform">💾</span>
                                    <span>Backup Database</span>
                                </div>
                                @if(request()->routeIs('settings.backup*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer: Practice Status & Quick Lock -->
            <div class="p-4 border-t border-teal-500/10 bg-spruce-950/60">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-spruce-900/60 border border-teal-500/15">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="relative flex h-2.5 w-2.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <div class="min-w-0 truncate">
                            <div class="text-xs font-semibold text-slate-200 truncate">Praktik Aktif</div>
                            <div class="text-[10px] text-teal-400/70 font-mono truncate">Sesi Terenkripsi</div>
                        </div>
                    </div>
                    <a href="{{ route('lock-screen') }}"
                       class="shrink-0 p-2 rounded-lg text-slate-400 hover:text-amber-300 hover:bg-amber-400/10 border border-transparent hover:border-amber-400/20 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-400/60"
                       title="Kunci Sesi Klinis"
                       aria-label="Kunci Sesi Klinis">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Offset Container (lg:pl-72) -->
        <div class="lg:pl-72 flex flex-col min-h-screen">
            
            <!-- Mobile Top Header Bar -->
            <header class="lg:hidden sticky top-0 z-30 sanctuary-glass px-4 py-3 flex items-center justify-between border-b border-teal-500/15">
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="sidebarOpen = true"
                            class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 border border-teal-500/15 focus:outline-none focus:ring-2 focus:ring-teal-400"
                            aria-label="Buka menu navigasi"
                            :aria-expanded="sidebarOpen">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="text-lg">🧠</span>
                        <span class="font-serif text-base font-semibold text-white">Web Psikolog</span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-spruce-900/80 border border-teal-500/20 text-[11px] text-slate-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Aktif</span>
                    </div>
                    <a href="{{ route('lock-screen') }}"
                       class="p-1.5 rounded-lg text-slate-400 hover:text-amber-300 hover:bg-amber-400/10 transition"
                       title="Kunci Sesi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- Main Content Canvas -->
            <main id="main-content" class="flex-1 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
