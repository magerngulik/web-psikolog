<div class="max-w-4xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <span>👤</span> Profil Psikolog & Tempat Praktik
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Kelola identitas resmi, gelar profesi, nomor SIPA, dan data tempat praktik yang tercantum pada dokumen RPP dan Logbook SKP.
            </p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 text-sm rounded-xl font-medium flex items-center justify-between">
            <span>✓ {{ session('message') }}</span>
        </div>
    @endif

    <!-- Live Preview Card Header & Signature Dokumen -->
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 border border-slate-700/80 rounded-2xl p-5 shadow-xl space-y-3">
        <span class="text-xs font-semibold text-teal-400 uppercase tracking-wider block">
            Pratinjau Kop & Tanda Tangan Dokumen Resmi
        </span>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-700/60 font-mono text-xs text-slate-300 space-y-1.5">
            <div class="text-white font-bold">
                LOGBOOK PSIKOLOG KLINIS TEMPAT PRAKTIK: <span class="text-teal-400 uppercase">{{ $practice_name ?: 'MANDIRI' }}</span>
            </div>
            <div>
                NAMA: <span class="text-white font-semibold uppercase">{{ $this->formatted_name }}</span>
            </div>
            <div>
                SIPA: <span class="text-slate-400">{{ $sipa_number }}</span>
            </div>
            <div class="text-slate-400 pt-1 text-[11px]">
                Titik Tanda Tangan: <span class="text-teal-400">{{ $practice_city ?: 'Selatpanjang' }}</span>, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
        </div>
    </div>

    <!-- Form Pengaturan Profil -->
    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <div>
            <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-2 mb-4 flex items-center gap-2">
                <span>🎓</span> 1. Identitas & Gelar Profesi Psikolog
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Gelar Depan <span class="lowercase text-slate-500">(opsional)</span></label>
                    <input type="text" wire:model="title_prefix" placeholder="misal: dr." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                    @error('title_prefix') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Lengkap *</label>
                    <input type="text" wire:model="name" placeholder="misal: Basirah" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                    @error('name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Gelar Belakang Profesi</label>
                    <input type="text" wire:model="title_suffix" placeholder="misal: S.Psi., M.Psi., Psikolog" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                    @error('title_suffix') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor SIPA / STR Profesi *</label>
                    <input type="text" wire:model="sipa_number" placeholder="misal: 503/123-SIPA/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:border-teal-500 transition">
                    @error('sipa_number') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email Resmi *</label>
                    <input type="email" wire:model="email" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                    @error('email') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-2 mb-4 flex items-center gap-2">
                <span>🏢</span> 2. Data Tempat Praktik & Titimangsa Dokumen
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Tempat Praktik *</label>
                    <input type="text" wire:model="practice_name" placeholder="misal: MANDIRI atau Klinik Psikologi Sejahtera" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                    @error('practice_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kota Domisili Praktik / Titimangsa *</label>
                    <input type="text" wire:model="practice_city" placeholder="misal: Selatpanjang" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                    @error('practice_city') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor Kontak / WhatsApp</label>
                    <input type="text" wire:model="phone" placeholder="misal: 08123456789" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Alamat Lengkap Praktik</label>
                    <textarea wire:model="practice_address" rows="2" placeholder="Alamat kantor / tempat praktik..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 text-sm flex items-center gap-2">
                <span>💾</span> Simpan Profil & Pengaturan
            </button>
        </div>
    </form>
</div>

