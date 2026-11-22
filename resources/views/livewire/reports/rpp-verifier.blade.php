<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center">
    <div class="max-w-lg w-full sanctuary-glass-card rounded-3xl p-8 sm:p-10 border border-blush-200 shadow-2xl text-center">
        @if($isValid)
            <div class="w-20 h-20 bg-emerald-500/10 text-emerald-700 border border-emerald-200 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-lg shadow-blush-200/50">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-200 uppercase tracking-wider font-mono mb-3">
                <span>Terotentikasi</span>
                <span>•</span>
                <span>Integritas Terjamin</span>
            </div>

            <h1 class="text-2xl font-serif font-bold text-ink-900 tracking-tight uppercase">DOKUMEN VALID DAN TERVERIFIKASI</h1>
            <p class="text-xs text-ink-500 mt-1 mb-6">Keabsahan Rekam Pemeriksaan Psikologis (RPP) Resmi</p>

            <div class="bg-blush-50 p-5 rounded-2xl text-left text-sm space-y-3.5 border border-blush-200 mb-6">
                <div>
                    <span class="text-[11px] font-bold text-ink-500 block uppercase tracking-wider font-mono">Nama Klien</span>
                    <span class="font-bold text-ink-900 text-base">{{ $note->appointment?->patient?->name ?? 'Klien' }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-ink-500 block uppercase tracking-wider font-mono">No. Rekam Medis</span>
                    <span class="font-mono text-blush-700 font-semibold text-sm">{{ $note->appointment?->patient?->medical_record_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-ink-500 block uppercase tracking-wider font-mono">Diagnosa (ICD-10)</span>
                    <span class="font-medium text-ink-700">{{ $note->icd10_code }} - {{ $note->icd10_description }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-ink-500 block uppercase tracking-wider font-mono">Psikolog Penanggung Jawab</span>
                    <span class="font-medium text-ink-700">{{ $note->appointment?->psychologist?->name ?? 'Psikolog Klinis' }}</span>
                </div>
                <div class="pt-2 border-t border-blush-200">
                    <span class="text-[11px] font-bold text-ink-500 block uppercase tracking-wider font-mono">Security Token QR</span>
                    <span class="font-mono text-xs text-blush-600 break-all select-all">{{ $note->qr_code_token }}</span>
                </div>
            </div>

            <div class="text-xs text-ink-500 flex items-center justify-center gap-2">
                <span>🔒 Dokumen ini diterbitkan secara sah oleh Sistem Rekam Medis Psikologi Klinik.</span>
            </div>
        @else
            <div class="w-20 h-20 bg-rose-500/10 text-rose-700 border border-rose-200 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-lg shadow-blush-200/50">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-700 border border-rose-200 uppercase tracking-wider font-mono mb-3">
                <span>Otentikasi Gagal</span>
            </div>

            <h1 class="text-2xl font-serif font-bold text-ink-900 tracking-tight uppercase">DOKUMEN TIDAK VALID</h1>
            <p class="text-xs text-rose-700 font-mono mt-1 mb-4">Token Keamanan Tidak Ditemukan</p>
            <p class="text-sm text-ink-700 mb-6 leading-relaxed">QR Code token ini tidak terdaftar di dalam database rekam medis resmi. Dokumen mungkin telah dimodifikasi, kedaluwarsa, atau tidak diterbitkan secara sah.</p>
        @endif

        <div class="mt-6 pt-5 border-t border-blush-200">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs text-blush-600 hover:text-blush-800 font-bold transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard Sistem
            </a>
        </div>
    </div>
</div>
