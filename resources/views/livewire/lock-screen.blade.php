<div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center p-4 font-sans">
    <div class="w-full max-w-sm bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-2xl text-center">
        <!-- Header / Logo -->
        <div class="mb-6">
            <div class="w-16 h-16 bg-teal-500/10 border border-teal-500/30 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Web Psikolog</h2>
            <p class="text-sm text-slate-400">Masukkan 6-Digit PIN Security Key</p>
        </div>

        <!-- Indicator Bulat PIN -->
        <div class="flex justify-center gap-3 mb-6">
            @for ($i = 0; $i < 6; $i++)
                <div class="w-4 h-4 rounded-full border-2 border-slate-600 flex items-center justify-center transition-all duration-200 {{ strlen($pin) > $i ? 'bg-teal-400 border-teal-400 shadow-lg shadow-teal-500/50' : '' }}"></div>
            @endfor
        </div>

        <!-- Error Alert -->
        @if ($errorMessage)
            <div class="mb-4 text-xs font-semibold text-rose-400 bg-rose-500/10 border border-rose-500/20 py-2 px-3 rounded-lg animate-bounce">
                {{ $errorMessage }}
            </div>
        @endif

        <!-- Keypad Virtual -->
        <div class="grid grid-cols-3 gap-3 mb-4">
            @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                <button wire:click="addNumber({{ $num }})" type="button" class="h-14 text-xl font-semibold text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 rounded-xl transition active:scale-95">
                    {{ $num }}
                </button>
            @endforeach
            <div class="h-14"></div>
            <button wire:click="addNumber(0)" type="button" class="h-14 text-xl font-semibold text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 rounded-xl transition active:scale-95">
                0
            </button>
            <button wire:click="deleteNumber" type="button" class="h-14 text-sm font-semibold text-rose-400 bg-slate-700/50 hover:bg-rose-500/20 border border-slate-600/50 rounded-xl transition active:scale-95 flex items-center justify-center">
                ⌫
            </button>
        </div>

        <p class="text-xs text-slate-500 mt-4">Default PIN awal: <span class="text-slate-300 font-mono">123456</span></p>
    </div>
</div>

