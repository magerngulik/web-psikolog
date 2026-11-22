<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Web Psikolog' }} — Clinical Practice & Records</title>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="blush-bg text-ink-900 selection:bg-blush-200 selection:text-blush-900 font-sans antialiased min-h-screen"
          x-data="{
              sidebarOpen: false,
              privacyMode: localStorage.getItem('wp_privacy_mode') === 'true',
              togglePrivacy() {
                  this.privacyMode = !this.privacyMode;
                  localStorage.setItem('wp_privacy_mode', this.privacyMode ? 'true' : 'false');
              }
          }"
          @keydown.window.escape="sidebarOpen = false"
          @keydown.window.alt.p.prevent="togglePrivacy()">
        <!-- Skip to Main Content Link (WCAG 2.4.1) -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:px-4 focus:py-2.5 focus:bg-blush-500 focus:text-white focus:font-bold focus:rounded-xl focus:shadow-lg focus:ring-4 focus:ring-blush-300">
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
             class="fixed inset-0 z-40 bg-ink-900/40 lg:hidden"
             aria-hidden="true"></div>

        <!-- Sidebar Navigation Drawer / Pinned Aside (Cozy Pastel Pink + Clean SVG Icons) -->
        <aside id="sanctuary-sidebar"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col blush-sidebar shadow-xl lg:shadow-none transition-transform duration-300 ease-in-out"
               aria-label="Navigasi Utama">
            
            <!-- Sidebar Header / Brand Mark -->
            <div class="h-20 px-5 flex items-center justify-between border-b border-blush-300/50">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-blush-500 rounded-2xl p-1 transition" aria-label="Web Psikolog - Beranda">
                    <div class="relative w-10 h-10 rounded-2xl bg-white border border-blush-200 flex items-center justify-center shadow-xs text-blush-600 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-white"></div>
                    </div>
                    <div>
                        <span class="font-serif text-lg font-bold tracking-tight text-ink-900 group-hover:text-blush-700 transition-colors">
                            Web Psikolog
                        </span>
                        <span class="block text-[10px] font-mono tracking-widest uppercase text-blush-700 -mt-0.5">
                            Clinical Sanctuary
                        </span>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button type="button" 
                        @click="sidebarOpen = false" 
                        class="lg:hidden p-2 rounded-xl text-ink-700 hover:text-ink-900 hover:bg-white/70 border border-blush-300/60 transition focus:outline-none focus:ring-2 focus:ring-blush-500"
                        aria-label="Tutup menu navigasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links Scroll Area -->
            <nav class="flex-1 overflow-y-auto px-3.5 py-5 space-y-6 scrollbar-none" aria-label="Menu Utama">
                
                <!-- Category 1: Layanan Klinis -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-mono font-bold uppercase tracking-wider text-blush-900/80">
                        Layanan Klinis
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('dashboard*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('dashboard*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('dashboard*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span>Dashboard</span>
                                </div>
                                @if(request()->routeIs('dashboard*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('clients.index') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('clients.*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('clients.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('clients.*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Data Klien</span>
                                </div>
                                @if(request()->routeIs('clients.*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cases.index') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('cases.*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('cases.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('cases.*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Kasus Medis</span>
                                </div>
                                @if(request()->routeIs('cases.*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sessions.index') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('sessions.*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('sessions.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('sessions.*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Sesi Konseling</span>
                                </div>
                                @if(request()->routeIs('sessions.*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 2: Profesi & Komunitas -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-mono font-bold uppercase tracking-wider text-blush-900/80">
                        Profesi & Komunitas
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('activities.index') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('activities.*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('activities.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('activities.*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                    <span>Kegiatan & Seminar</span>
                                </div>
                                @if(request()->routeIs('activities.*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('organizations.index') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('organizations.*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('organizations.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('organizations.*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Mitra Organisasi</span>
                                </div>
                                @if(request()->routeIs('organizations.*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.logbook') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('reports.*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('reports.*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('reports.*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <span>Logbook SKP</span>
                                </div>
                                @if(request()->routeIs('reports.*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Category 3: Pengaturan & Sistem -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-mono font-bold uppercase tracking-wider text-blush-900/80">
                        Pengaturan & Sistem
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('settings.references') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('settings.references*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('settings.references*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('settings.references*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span>Master Referensi</span>
                                </div>
                                @if(request()->routeIs('settings.references*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.profile') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('settings.profile*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('settings.profile*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('settings.profile*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Profil Praktik</span>
                                </div>
                                @if(request()->routeIs('settings.profile*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.security') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('settings.security*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('settings.security*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('settings.security*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    <span>Keamanan PIN</span>
                                </div>
                                @if(request()->routeIs('settings.security*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.backup') }}"
                               class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500 {{ request()->routeIs('settings.backup*') ? 'bg-white text-blush-700 font-bold shadow-sm' : 'text-ink-700 hover:text-ink-900 hover:bg-white/55 font-medium' }}"
                               {!! request()->routeIs('settings.backup*') ? 'aria-current="page"' : '' !!}>
                                <div class="flex items-center gap-3">
                                    <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('settings.backup*') ? 'text-blush-500' : 'text-blush-700/70 group-hover:text-blush-600' }}" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                    </svg>
                                    <span>Backup Database</span>
                                </div>
                                @if(request()->routeIs('settings.backup*'))
                                    <span class="w-2 h-2 rounded-full bg-blush-500"></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer: Cozy Status Card, Privacy Mode & Quick Lock -->
            <div class="p-3.5 border-t border-blush-300/50 space-y-2">
                <div class="flex items-center justify-between p-3 rounded-2xl bg-white/95 border border-white shadow-xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="relative flex h-2.5 w-2.5 shrink-0">
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 ring-2 ring-emerald-200"></span>
                        </span>
                        <div class="min-w-0 truncate">
                            <div class="text-xs font-bold text-ink-900 truncate">Praktik Aktif</div>
                            <div class="text-[10px] font-mono truncate" :class="privacyMode ? 'text-blush-700 font-bold' : 'text-ink-500'" x-text="privacyMode ? 'Mode Privasi Aktif' : 'Sesi Terenkripsi'"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button"
                                @click="togglePrivacy()"
                                :title="privacyMode ? 'Matikan Mode Privasi (Alt+P)' : 'Aktifkan Mode Privasi — Samarkan Nama Klien (Alt+P)'"
                                :aria-label="privacyMode ? 'Matikan Mode Privasi' : 'Aktifkan Mode Privasi'"
                                :aria-pressed="privacyMode"
                                class="p-2 rounded-xl border transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500"
                                :class="privacyMode ? 'bg-blush-500 text-white border-blush-500 shadow-2xs' : 'text-ink-700 hover:text-blush-700 hover:bg-blush-100 border-transparent hover:border-blush-200'">
                            <svg x-show="!privacyMode" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="privacyMode" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                        <a href="{{ route('lock-screen') }}"
                           class="p-2 rounded-xl text-ink-700 hover:text-blush-700 hover:bg-blush-100 border border-transparent hover:border-blush-200 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blush-500"
                           title="Kunci Sesi Klinis"
                           aria-label="Kunci Sesi Klinis">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Offset Container (lg:pl-72) -->
        <div class="lg:pl-72 flex flex-col min-h-screen">
            
            <!-- Mobile Top Header Bar -->
            <header class="lg:hidden sticky top-0 z-30 bg-white/95 px-4 py-3 flex items-center justify-between border-b border-blush-200">
                <div class="flex items-center gap-3">
                    <button type="button"
                            @click="sidebarOpen = true"
                            class="p-2 rounded-xl text-ink-700 hover:text-ink-900 hover:bg-blush-100 border border-blush-200 focus:outline-none focus:ring-2 focus:ring-blush-400"
                            aria-label="Buka menu navigasi"
                            :aria-expanded="sidebarOpen">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-blush-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="font-serif text-base font-bold text-ink-900">Web Psikolog</span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="togglePrivacy()"
                            class="p-1.5 rounded-lg border transition"
                            :class="privacyMode ? 'bg-blush-500 text-white border-blush-500' : 'text-ink-700 hover:text-blush-700 hover:bg-blush-100 border-blush-200'"
                            title="Mode Privasi">
                        <svg x-show="!privacyMode" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="privacyMode" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                    <a href="{{ route('lock-screen') }}"
                       class="p-1.5 rounded-lg text-ink-700 hover:text-blush-700 hover:bg-blush-100 transition"
                       title="Kunci Sesi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- Main Content Canvas -->
            <main id="main-content" class="flex-1 py-7 px-4 sm:px-6 lg:px-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
