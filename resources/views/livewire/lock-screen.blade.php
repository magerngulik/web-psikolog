<div x-data @keydown.window="if ($event.key >= '0' && $event.key <= '9') { $wire.addNumber($event.key) } else if ($event.key === 'Backspace') { $wire.deleteNumber() }" class="min-h-screen sanctuary-bg flex flex-col items-center justify-center p-4 font-sans relative overflow-hidden" role="region" aria-label="Layar Kunci PIN Keamanan">
    <!-- Ambient Atmospheric Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative z-10 w-full max-w-sm sanctuary-glass-card border border-teal-500/20 rounded-3xl p-7 sm:p-8 shadow-2xl shadow-teal-950/80 text-center">
        <!-- Sanctuary Security Emblem -->
        <div class="mb-6 space-y-3">
            <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500/25 to-emerald-700/20 border border-teal-400/30 flex items-center justify-center mx-auto shadow-lg shadow-teal-950/60" aria-hidden="true">
                <svg class="w-8 h-8 text-teal-300 filter drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-emerald-400 border-2 border-spruce-950"></div>
            </div>
            <div>
                <h1 class="font-serif text-2xl font-semibold text-white tracking-tight">Web Psikolog</h1>
                <p class="text-xs text-slate-400 font-sans mt-0.5">Clinical Sanctuary • Private Vault</p>
                <p class="text-[11px] font-mono text-teal-300/80 mt-1">Ketik langsung via keyboard atau keypad layar</p>
            </div>
        </div>

        <!-- Indicator Bulat PIN -->
        <div class="flex justify-center gap-3.5 mb-6" role="status" aria-label="Status PIN: {{ strlen($pin) }} dari 6 digit terisi">
            @for ($i = 0; $i < 6; $i++)
                <div class="w-4 h-4 rounded-full border-2 transition-all duration-300 flex items-center justify-center {{ strlen($pin) > $i ? 'bg-gradient-to-br from-teal-300 to-emerald-400 border-teal-300 shadow-md shadow-teal-400/60 scale-110' : 'border-teal-500/30 bg-spruce-950/80' }}" aria-hidden="true"></div>
            @endfor
        </div>

        <!-- Error Alert -->
        @if ($errorMessage)
            <div class="mb-5 text-xs font-semibold text-rose-300 bg-rose-500/15 border border-rose-500/30 py-2.5 px-3 rounded-xl" role="alert" aria-live="assertive">
                {{ $errorMessage }}
            </div>
        @endif

        <!-- Keypad Virtual Taktil -->
        <div class="grid grid-cols-3 gap-3 mb-5" role="group" aria-label="Keypad Angka PIN">
            @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                <button wire:click="addNumber({{ $num }})" type="button" aria-label="Angka {{ $num }}" class="h-14 font-mono text-xl font-semibold text-white bg-spruce-900/70 hover:bg-spruce-800 border border-teal-500/15 hover:border-teal-400/40 rounded-2xl transition-all duration-150 active:scale-95 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                    {{ $num }}
                </button>
            @endforeach
            <div class="h-14" aria-hidden="true"></div>
            <button wire:click="addNumber(0)" type="button" aria-label="Angka 0" class="h-14 font-mono text-xl font-semibold text-white bg-spruce-900/70 hover:bg-spruce-800 border border-teal-500/15 hover:border-teal-400/40 rounded-2xl transition-all duration-150 active:scale-95 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                0
            </button>
            <button wire:click="deleteNumber" type="button" aria-label="Hapus digit terakhir (Backspace)" class="h-14 font-mono text-sm font-semibold text-rose-400 bg-spruce-900/70 hover:bg-rose-500/20 border border-teal-500/15 hover:border-rose-400/40 rounded-2xl transition-all duration-150 active:scale-95 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-rose-400">
                ⌫
            </button>
        </div>

        <p class="text-[11px] text-slate-500 font-sans">
            Default PIN awal: <span class="text-teal-300 font-mono font-medium">123456</span>
        </p>
    </div>
</div>
