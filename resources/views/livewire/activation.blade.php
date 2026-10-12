<div class="min-h-screen sanctuary-bg flex flex-col items-center justify-center p-4 sm:p-6 font-sans relative overflow-hidden" 
     x-data="{ copied: false, pasting: false }">
    
    <!-- Ambient Atmospheric Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative z-10 w-full max-w-2xl sanctuary-glass-card border border-teal-500/20 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-teal-950/80">
        
        <!-- Header Emblem & Brand -->
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-teal-500/15">
            <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500/25 to-emerald-700/20 border border-teal-400/30 flex items-center justify-center shadow-lg shadow-teal-950/60 shrink-0">
                <svg class="w-7 h-7 text-teal-300 filter drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-emerald-400 border-2 border-spruce-950"></div>
            </div>
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-teal-500/15 border border-teal-400/30 text-teal-300 text-[11px] font-mono tracking-wide uppercase mb-1">
                    <span>Hardware-Locked Security</span>
                    <span>•</span>
                    <span>Beli Putus</span>
                </div>
                <h1 class="font-serif text-2xl font-semibold text-white tracking-tight">Aktivasi Lisensi Web Psikolog</h1>
                <p class="text-xs text-slate-400 font-sans mt-0.5">Satu lisensi sah seumur hidup yang terkunci aman ke perangkat laptop ini.</p>
            </div>
        </div>

        <!-- Error Alert -->
        @if ($errorMessage)
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-200 text-xs flex items-start gap-3" role="alert">
                <span class="text-lg">⚠️</span>
                <div class="leading-relaxed">
                    <span class="font-bold block text-rose-300">Aktivasi Tidak Berhasil</span>
                    {{ $errorMessage }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Left Side: Machine ID & WhatsApp QR (7 Cols) -->
            <div class="md:col-span-7 space-y-4">
                <div class="bg-spruce-900/60 border border-teal-500/15 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-teal-300 tracking-wide">1. KODE PERANGKAT LAPTOP ANDA</span>
                        <span class="text-[10px] font-mono text-slate-400">Machine ID</span>
                    </div>

                    <!-- Machine ID Display & Copy Button -->
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-spruce-950 border border-teal-500/25 rounded-xl px-3.5 py-2.5 font-mono text-sm sm:text-base font-bold text-teal-200 tracking-wider text-center select-all shadow-inner">
                            {{ $machineId }}
                        </div>
                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $machineId }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="px-3 py-2.5 bg-teal-500/20 hover:bg-teal-500/30 border border-teal-400/40 text-teal-200 rounded-xl text-xs font-semibold transition active:scale-95 flex items-center gap-1 shrink-0"
                                :class="{ 'bg-emerald-500/30 text-emerald-200 border-emerald-400': copied }">
                            <span x-show="!copied">📋 Salin</span>
                            <span x-show="copied" x-cloak class="text-emerald-300 font-bold">✓ Tersalin!</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-400 leading-relaxed font-sans">
                        Kirimkan kode perangkat di atas ke Admin untuk menerbitkan Kunci Lisensi Anda.
                    </p>
                </div>

                <!-- QR Code WhatsApp Box -->
                <div class="bg-spruce-900/40 border border-teal-500/15 rounded-2xl p-4 flex items-center gap-4">
                    <div class="p-2 bg-spruce-950 rounded-xl border border-teal-500/25 shrink-0 shadow-md">
                        {!! $qrSvg !!}
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-white block">Aktivasi Cepat via HP</span>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-sans">
                            Scan QR di samping menggunakan kamera smartphone / WhatsApp Anda untuk mengirim kode secara otomatis tanpa perlu mengetik ulang.
                        </p>
                        @if ($whatsAppUrl)
                            <a href="{{ $whatsAppUrl }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 hover:text-emerald-300 underline underline-offset-2">
                                <span>Buka WhatsApp di Browser</span>
                                <span>↗</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side: License Key Input & Submit (5 Cols) -->
            <div class="md:col-span-5 flex flex-col justify-between space-y-4">
                <form wire:submit.prevent="activate" class="space-y-4 h-full flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="licenseToken" class="text-xs font-semibold text-teal-300 tracking-wide block">
                                2. KUNCI LISENSI
                            </label>
                            <button type="button" 
                                    @click="navigator.clipboard.readText().then(text => { $wire.set('licenseToken', text) })"
                                    class="text-[11px] font-mono text-teal-400 hover:text-teal-200 transition underline underline-offset-2">
                                Tempel dari Clipboard
                            </button>
                        </div>

                        <textarea wire:model="licenseToken" 
                                  id="licenseToken"
                                  rows="6" 
                                  placeholder="Paste Kunci Lisensi resmi dari Admin di sini (awalan WP-LIC-...)" 
                                  class="w-full bg-spruce-950 border border-teal-500/25 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 rounded-xl p-3 font-mono text-xs text-white placeholder-slate-500 transition leading-relaxed resize-none shadow-inner"></textarea>
                        
                        @error('licenseToken')
                            <span class="text-[11px] text-rose-400 block font-medium">{{ $message }}</span>
                        @enderror

                        <!-- Or Upload File .lic -->
                        <div class="pt-1">
                            <label class="block text-[11px] text-slate-400 mb-1">Atau muat file lisensi (.lic):</label>
                            <input type="file" wire:model="licenseFile" accept=".lic,.txt" class="block w-full text-xs text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-teal-500/20 file:text-teal-200 hover:file:bg-teal-500/30 cursor-pointer">
                        </div>
                    </div>

                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold text-sm shadow-lg shadow-teal-950/60 hover:shadow-teal-500/20 transition-all duration-200 transform active:scale-98 flex items-center justify-center gap-2">
                        <span wire:loading.remove>Aktifkan Aplikasi Sekarang →</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memverifikasi Lisensi...</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Guarantees -->
        <div class="mt-6 pt-4 border-t border-teal-500/10 flex flex-wrap items-center justify-between text-[11px] text-slate-500 font-sans gap-2">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1 text-teal-400/80">
                    <span>🔒</span> Kriptografi Ed25519
                </span>
                <span>•</span>
                <span>100% Verifikasi Offline</span>
                <span>•</span>
                <span>Bebas Biaya Langganan</span>
            </div>
            <div>
                <span>Bantuan Teknis: WhatsApp Admin</span>
            </div>
        </div>

    </div>
</div>

