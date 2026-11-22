<div x-data="{
        localPin: '',
        submitting: false,
        addDigit(d) {
            if (this.submitting || this.localPin.length >= 6) return;
            this.localPin += String(d);
            if (this.localPin.length === 6) {
                this.submitting = true;
                $wire.submitPin(this.localPin).then(() => {
                    this.localPin = '';
                    this.submitting = false;
                });
            }
        },
        backspace() {
            if (this.submitting) return;
            this.localPin = this.localPin.slice(0, -1);
        }
    }"
    @keydown.window="if ($event.key >= '0' && $event.key <= '9') { addDigit($event.key) } else if ($event.key === 'Backspace') { backspace() }"
    class="min-h-screen blush-bg flex flex-col items-center justify-center p-4 font-sans relative overflow-hidden" role="region" aria-label="Layar Kunci PIN Keamanan">

    <div class="relative z-10 w-full max-w-sm blush-card rounded-3xl overflow-hidden text-center">
        <!-- Soft Pastel Accent Top Bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-blush-300 via-blush-500 to-rose-300" aria-hidden="true"></div>

        <div class="p-7 sm:p-8">
            <!-- Sanctuary Security Emblem -->
            <div class="mb-6 space-y-3">
                <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-blush-100 to-blush-200 border border-blush-300/60 flex items-center justify-center mx-auto shadow-sm" aria-hidden="true">
                    <svg class="w-8 h-8 text-blush-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-white"></div>
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-semibold text-ink-900 tracking-tight">Web Psikolog</h1>
                    <p class="text-xs text-ink-500 font-sans mt-0.5">Clinical Sanctuary • Private Vault</p>
                    <p class="text-[11px] font-mono text-blush-600 mt-1.5 bg-blush-50 inline-block px-2.5 py-0.5 rounded-full border border-blush-200/80">Ketik via keyboard atau keypad layar</p>
                </div>
            </div>

            <!-- Indicator Bulat PIN -->
            <div class="flex justify-center gap-3.5 mb-6" role="status" :aria-label="'Status PIN: ' + localPin.length + ' dari 6 digit terisi'">
                @for ($i = 0; $i < 6; $i++)
                    <div class="w-4 h-4 rounded-full border-2 transition-all duration-150 flex items-center justify-center"
                         :class="localPin.length > {{ $i }} ? 'bg-blush-500 border-blush-500 shadow-sm scale-110' : 'border-blush-300 bg-blush-50'"
                         aria-hidden="true"></div>
                @endfor
            </div>

            <!-- Error Alert -->
            @if ($errorMessage)
                <div class="mb-5 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 py-2.5 px-3 rounded-xl" role="alert" aria-live="assertive">
                    {{ $errorMessage }}
                </div>
            @endif

            <!-- Keypad Virtual Taktil -->
            <div class="grid grid-cols-3 gap-3 mb-5" role="group" aria-label="Keypad Angka PIN">
                @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                    <button @click="addDigit({{ $num }})" :disabled="submitting" type="button" aria-label="Angka {{ $num }}" class="h-14 font-mono text-xl font-semibold text-ink-900 bg-blush-50/70 hover:bg-blush-100 border border-blush-200 hover:border-blush-400 rounded-2xl transition-colors duration-150 active:scale-95 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blush-400 disabled:opacity-50">
                        {{ $num }}
                    </button>
                @endforeach
                <div class="h-14" aria-hidden="true"></div>
                <button @click="addDigit(0)" :disabled="submitting" type="button" aria-label="Angka 0" class="h-14 font-mono text-xl font-semibold text-ink-900 bg-blush-50/70 hover:bg-blush-100 border border-blush-200 hover:border-blush-400 rounded-2xl transition-colors duration-150 active:scale-95 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blush-400 disabled:opacity-50">
                    0
                </button>
                <button @click="backspace()" :disabled="submitting" type="button" aria-label="Hapus digit terakhir (Backspace)" class="h-14 font-mono text-sm font-semibold text-rose-600 bg-rose-50/80 hover:bg-rose-100 border border-rose-200 hover:border-rose-300 rounded-2xl transition-colors duration-150 active:scale-95 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-rose-400 disabled:opacity-50">
                    ⌫
                </button>
            </div>

            <p class="text-[11px] text-ink-400 font-sans">
                Default PIN awal: <span class="text-blush-700 font-mono font-semibold">123456</span>
            </p>
        </div>
    </div>
</div>
