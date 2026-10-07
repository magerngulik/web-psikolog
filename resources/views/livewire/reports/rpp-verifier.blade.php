<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center">
    <div class="max-w-lg w-full sanctuary-glass-card rounded-3xl p-8 sm:p-10 border border-teal-500/20 shadow-2xl text-center">
        @if($isValid)
            <div class="w-20 h-20 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-lg shadow-emerald-950/40">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/25 uppercase tracking-wider font-mono mb-3">
                <span>Terotentikasi</span>
                <span>•</span>
                <span>Integritas Terjamin</span>
            </div>

            <h1 class="text-2xl font-serif font-bold text-white tracking-tight uppercase">DOKUMEN VALID DAN TERVERIFIKASI</h1>
            <p class="text-xs text-slate-400 mt-1 mb-6">Keabsahan Rekam Pemeriksaan Psikologis (RPP) Resmi</p>

            <div class="bg-spruce-950/70 p-5 rounded-2xl text-left text-sm space-y-3.5 border border-teal-500/15 mb-6">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-mono">Nama Klien</span>
                    <span class="font-bold text-white text-base">{{ $note->appointment?->patient?->name ?? 'Klien' }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-mono">No. Rekam Medis</span>
                    <span class="font-mono text-teal-300 font-semibold text-sm">{{ $note->appointment?->patient?->medical_record_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-mono">Diagnosa (ICD-10)</span>
                    <span class="font-medium text-slate-200">{{ $note->icd10_code }} - {{ $note->icd10_description }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-mono">Psikolog Penanggung Jawab</span>
                    <span class="font-medium text-slate-200">{{ $note->appointment?->psychologist?->name ?? 'Psikolog Klinis' }}</span>
                </div>
                <div class="pt-2 border-t border-teal-500/10">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider font-mono">Security Token QR</span>
                    <span class="font-mono text-xs text-teal-400/90 break-all select-all">{{ $note->qr_code_token }}</span>
                </div>
            </div>

            <div class="text-xs text-slate-400 flex items-center justify-center gap-2">
                <span>🔒 Dokumen ini diterbitkan secara sah oleh Sistem Rekam Medis Psikologi Klinik.</span>
            </div>
        @else
            <div class="w-20 h-20 bg-rose-500/10 text-rose-400 border border-rose-500/30 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-lg shadow-rose-950/40">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-300 border border-rose-500/25 uppercase tracking-wider font-mono mb-3">
                <span>Otentikasi Gagal</span>
            </div>

            <h1 class="text-2xl font-serif font-bold text-white tracking-tight uppercase">DOKUMEN TIDAK VALID</h1>
            <p class="text-xs text-rose-400/90 font-mono mt-1 mb-4">Token Keamanan Tidak Ditemukan</p>
            <p class="text-sm text-slate-300 mb-6 leading-relaxed">QR Code token ini tidak terdaftar di dalam database rekam medis resmi. Dokumen mungkin telah dimodifikasi, kedaluwarsa, atau tidak diterbitkan secara sah.</p>
        @endif

        <div class="mt-6 pt-5 border-t border-teal-500/10">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs text-teal-400 hover:text-teal-300 font-bold transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Dashboard Sistem
            </a>
        </div>
    </div>
</div>
