<div class="max-w-xl mx-auto space-y-6">
    <div class="sanctuary-glass-card rounded-3xl shadow-2xl border border-blush-200 p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blush-500"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-blush-700">Security Vault</span>
            </div>
            <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight mt-1">Pengaturan PIN & Keamanan</h1>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">Ubah PIN 6-Digit untuk memproteksi kerahasiaan rekam medis pasien.</p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-blush-100 border border-blush-200 text-blush-700 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between">
                <span>✓ {{ session('message') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="updatePin" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">PIN Saat Ini</label>
                <input type="password" maxlength="6" wire:model="current_pin" placeholder="••••••" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                @error('current_pin') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">PIN Baru (6 Digit)</label>
                <input type="password" maxlength="6" wire:model="new_pin" placeholder="••••••" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                @error('new_pin') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Konfirmasi PIN Baru</label>
                <input type="password" maxlength="6" wire:model="new_pin_confirmation" placeholder="••••••" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                @error('new_pin_confirmation') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold py-2.5 rounded-xl text-xs sm:text-sm transition shadow-lg shadow-blush-200/50 transform hover:-translate-y-0.5">
                Simpan PIN Baru
            </button>
        </form>

        <div class="pt-4 border-t border-blush-200 flex items-center justify-between">
            <div>
                <h2 class="font-serif text-sm font-semibold text-ink-900">Kunci Aplikasi Sekarang</h2>
                <p class="text-xs text-ink-500 font-sans mt-0.5">Langsung amankan sesi dan kembali ke Lock Screen PIN.</p>
            </div>
            <button wire:click="lockNow" type="button" class="px-4 py-2 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white text-xs font-bold rounded-xl transition shadow-md shadow-blush-200/50 flex items-center gap-1.5">
                <span>🔒</span> <span>Kunci Layar</span>
            </button>
        </div>
    </div>
</div>
