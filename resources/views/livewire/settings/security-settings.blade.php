<div class="max-w-xl mx-auto space-y-6">
    <div class="sanctuary-glass-card rounded-3xl shadow-2xl border border-teal-500/15 p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-teal-300">Security Vault</span>
            </div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight mt-1">Pengaturan PIN & Keamanan</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Ubah PIN 6-Digit untuk memproteksi kerahasiaan rekam medis pasien.</p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between">
                <span>✓ {{ session('message') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="updatePin" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">PIN Saat Ini</label>
                <input type="password" maxlength="6" wire:model="current_pin" placeholder="••••••" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                @error('current_pin') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">PIN Baru (6 Digit)</label>
                <input type="password" maxlength="6" wire:model="new_pin" placeholder="••••••" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                @error('new_pin') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Konfirmasi PIN Baru</label>
                <input type="password" maxlength="6" wire:model="new_pin_confirmation" placeholder="••••••" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                @error('new_pin_confirmation') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl text-xs sm:text-sm transition shadow-lg shadow-teal-950/60 transform hover:-translate-y-0.5">
                Simpan PIN Baru
            </button>
        </form>

        <div class="pt-4 border-t border-teal-500/10 flex items-center justify-between">
            <div>
                <h2 class="font-serif text-sm font-semibold text-white">Kunci Aplikasi Sekarang</h2>
                <p class="text-xs text-slate-400 font-sans mt-0.5">Langsung amankan sesi dan kembali ke Lock Screen PIN.</p>
            </div>
            <button wire:click="lockNow" type="button" class="px-4 py-2 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white text-xs font-bold rounded-xl transition shadow-md shadow-rose-950/50 flex items-center gap-1.5">
                <span>🔒</span> <span>Kunci Layar</span>
            </button>
        </div>
    </div>
</div>
