<div class="min-h-screen bg-slate-900 py-12 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center">
    <div class="max-w-md w-full bg-slate-800 rounded-2xl shadow-xl p-8 border border-slate-700 text-center text-white">
        @if($isValid)
            <div class="w-16 h-16 bg-emerald-950 text-emerald-400 border border-emerald-700/50 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                ✓
            </div>
            <h2 class="text-2xl font-extrabold text-white">DOKUMEN VALID DAN TERVERIFIKASI</h2>
            <p class="text-xs text-emerald-400 font-semibold uppercase tracking-widest mt-1 mb-6">Keabsahan Rekam Pemeriksaan Psikologis (RPP)</p>

            <div class="bg-slate-900/80 p-4 rounded-xl text-left text-sm space-y-2 border border-slate-700 mb-6">
                <div>
                    <span class="text-xs text-slate-400 block uppercase">Nama Klien</span>
                    <span class="font-bold text-teal-400 text-base">{{ $note->appointment?->patient?->name ?? 'Klien' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block uppercase">No. Rekam Medis</span>
                    <span class="font-semibold text-slate-200">{{ $note->appointment?->patient?->medical_record_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block uppercase">Diagnosa (ICD-10)</span>
                    <span class="font-semibold text-slate-200">{{ $note->icd10_code }} - {{ $note->icd10_description }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block uppercase">Psikolog Penanggung Jawab</span>
                    <span class="font-semibold text-slate-200">{{ $note->appointment?->psychologist?->name ?? 'Psikolog' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block uppercase">Security Token</span>
                    <span class="font-mono text-xs text-slate-400 break-all">{{ $note->qr_code_token }}</span>
                </div>
            </div>

            <div class="text-xs text-slate-400">
                🔒 Dokumen ini diterbitkan secara sah oleh Sistem Rekam Medis Psikologi Klinik.
            </div>
        @else
            <div class="w-16 h-16 bg-red-950 text-red-400 border border-red-700/50 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                ✕
            </div>
            <h2 class="text-2xl font-extrabold text-white">DOKUMEN TIDAK VALID</h2>
            <p class="text-xs text-red-400 font-semibold uppercase tracking-widest mt-1 mb-6">Token Keamanan Tidak Ditemukan</p>
            <p class="text-sm text-slate-300 mb-6">QR Code token ini tidak terdaftar di dalam database rekam medis resmi. Dokumen mungkin telah dimodifikasi atau tidak sah.</p>
        @endif

        <div class="mt-6 pt-4 border-t border-slate-700">
            <a href="{{ route('dashboard') }}" class="text-xs text-teal-400 font-bold hover:underline">
                ← Kembali ke Sistem Dashboard
            </a>
        </div>
    </div>
</div>
