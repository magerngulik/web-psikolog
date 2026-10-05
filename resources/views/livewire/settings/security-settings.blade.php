<div class="max-w-xl mx-auto p-6 space-y-6">
    <div class="bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-white">Pengaturan PIN & Keamanan</h2>
            <p class="text-sm text-slate-400">Ubah PIN 6-Digit untuk memproteksi rekam medis pasien.</p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 text-sm rounded-xl font-medium">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePin" class="space-y-4 mb-6">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">PIN Saat Ini</label>
                <input type="password" maxlength="6" wire:model="current_pin" placeholder="••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:border-teal-500 transition">
                @error('current_pin') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">PIN Baru (6 Digit)</label>
                <input type="password" maxlength="6" wire:model="new_pin" placeholder="••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:border-teal-500 transition">
                @error('new_pin') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Konfirmasi PIN Baru</label>
                <input type="password" maxlength="6" wire:model="new_pin_confirmation" placeholder="••••••" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:border-teal-500 transition">
                @error('new_pin_confirmation') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold py-2.5 rounded-xl text-sm transition shadow-lg shadow-teal-500/20">
                Simpan PIN Baru
            </button>
        </form>

        <hr class="border-slate-700">

        <div class="flex items-center justify-between pt-2">
            <div>
                <h4 class="text-sm font-bold text-white">Kunci Aplikasi Sekarang</h4>
                <p class="text-xs text-slate-400 mt-0.5">Langsung kembali ke Lock Screen PIN.</p>
            </div>
            <button wire:click="lockNow" type="button" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold rounded-xl transition shadow-lg shadow-rose-500/20 flex items-center gap-1.5">
                <span>🔒</span> Kunci Layar
            </button>
        </div>
    </div>
</div>
