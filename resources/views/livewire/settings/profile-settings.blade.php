<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-teal-300">Pengaturan Praktik</span>
        </div>
        <h1 class="font-serif text-3xl font-semibold text-white tracking-tight mt-1">Profil Psikolog & Tempat Praktik</h1>
        <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">
            Kelola identitas resmi, gelar profesi, nomor SIPA, dan data tempat praktik yang tercantum pada dokumen RPP dan Logbook SKP.
        </p>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between">
            <span>✓ {{ session('message') }}</span>
        </div>
    @endif

    <!-- Live Preview Card Header & Signature Dokumen -->
    <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl border border-teal-500/20 space-y-3">
        <span class="text-xs font-mono font-semibold text-teal-300 uppercase tracking-wider block">
            Pratinjau Kop & Tanda Tangan Dokumen Resmi (Live Preview)
        </span>
        <div class="bg-spruce-950/90 p-4 rounded-xl border border-teal-500/20 font-mono text-xs text-slate-300 space-y-1.5">
            <div class="text-white font-bold">
                LOGBOOK PSIKOLOG KLINIS TEMPAT PRAKTIK: <span class="text-teal-300 uppercase">{{ $practice_name ?: 'MANDIRI' }}</span>
            </div>
            <div>
                NAMA: <span class="text-white font-semibold uppercase">{{ $this->formatted_name }}</span>
            </div>
            <div>
                SIPA: <span class="text-slate-400">{{ $sipa_number }}</span>
            </div>
            <div class="text-slate-400 pt-1 text-[11px]">
                Titik Tanda Tangan: <span class="text-teal-300 font-medium">{{ $practice_city ?: 'Selatpanjang' }}</span>, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>
    </div>

    <!-- Form Pengaturan Profil -->
    <form wire:submit.prevent="save" class="sanctuary-glass-card border border-teal-500/15 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <div>
            <h2 class="font-serif text-lg font-semibold text-teal-300 border-b border-teal-500/10 pb-2 mb-4 flex items-center gap-2">
                <span>🎓</span> 1. Identitas & Gelar Profesi Psikolog
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Gelar Depan <span class="lowercase text-slate-500 font-normal">(opsional)</span></label>
                    <input type="text" wire:model="title_prefix" placeholder="misal: dr." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('title_prefix') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Lengkap *</label>
                    <input type="text" wire:model="name" placeholder="misal: Basirah" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Gelar Belakang Profesi</label>
                    <input type="text" wire:model="title_suffix" placeholder="misal: S.Psi., M.Psi., Psikolog" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('title_suffix') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor SIPA / STR Profesi *</label>
                    <input type="text" wire:model="sipa_number" placeholder="misal: 503/123-SIPA/2026" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('sipa_number') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email Resmi *</label>
                    <input type="email" wire:model="email" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('email') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div>
            <h2 class="font-serif text-lg font-semibold text-teal-300 border-b border-teal-500/10 pb-2 mb-4 flex items-center gap-2">
                <span>🏢</span> 2. Data Tempat Praktik & Titimangsa Dokumen
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Tempat Praktik *</label>
                    <input type="text" wire:model="practice_name" placeholder="misal: MANDIRI atau Klinik Psikologi Sejahtera" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('practice_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kota Domisili Praktik / Titimangsa *</label>
                    <input type="text" wire:model="practice_city" placeholder="misal: Selatpanjang" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    @error('practice_city') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor Kontak / WhatsApp</label>
                    <input type="text" wire:model="phone" placeholder="misal: 08123456789" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Alamat Lengkap Praktik</label>
                    <textarea wire:model="practice_address" rows="2" placeholder="Alamat kantor / tempat praktik..." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition leading-relaxed"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-950/60 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                Simpan Profil Praktik
            </button>
        </div>
    </form>
</div>
