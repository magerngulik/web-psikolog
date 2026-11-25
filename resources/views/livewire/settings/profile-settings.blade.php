<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blush-500"></span>
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-blush-700">Pengaturan Praktik</span>
        </div>
        <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight mt-1">Profil Psikolog & Tempat Praktik</h1>
        <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">
            Kelola identitas resmi, gelar profesi, nomor SIPPK, dan data tempat praktik yang tercantum pada dokumen RPP dan Logbook SKP.
        </p>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-blush-100 border border-blush-200 text-blush-700 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between">
            <span>✓ {{ session('message') }}</span>
        </div>
    @endif

    <!-- Live Preview Card Header & Signature Dokumen -->
    <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl border border-blush-200 space-y-3">
        <span class="text-xs font-mono font-semibold text-blush-700 uppercase tracking-wider block">
            Pratinjau Kop & Tanda Tangan Dokumen Resmi (Live Preview)
        </span>
        <div class="bg-blush-50 p-4 rounded-xl border border-blush-200 font-mono text-xs text-ink-700 space-y-1.5">
            <div class="text-ink-900 font-bold">
                LOGBOOK PSIKOLOG KLINIS TEMPAT PRAKTIK: <span class="text-blush-700 uppercase">{{ $practice_name ?: 'MANDIRI' }}</span>
            </div>
            <div>
                NAMA: <span class="text-ink-900 font-semibold uppercase">{{ $this->formatted_name }}</span>
            </div>
            <div>
                SIPPK: <span class="text-ink-500">{{ $sipa_number }}</span>
            </div>
            <div class="text-ink-500 pt-1 text-[11px] flex items-center justify-between">
                <span>Titik Tanda Tangan: <span class="text-blush-700 font-medium">{{ $practice_city ?: 'Selatpanjang' }}</span>, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                @if($signature_photo)
                    <span class="text-emerald-700 text-[10px] bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-200">Preview TTD Baru</span>
                @elseif($existing_signature_path)
                    <span class="text-blush-600 text-[10px] bg-blush-100 px-2 py-0.5 rounded border border-blush-200">TTD Tersimpan Aktif</span>
                @else
                    <span class="text-ink-500 text-[10px] italic">TTD Manual (Belum Diunggah)</span>
                @endif
            </div>

            <!-- Tanda Tangan Visual Preview (2x Ukuran) -->
            <div class="pt-2 border-t border-blush-200 flex items-center gap-5">
                <div class="w-56 h-28 bg-white/5 border border-dashed border-blush-200 rounded-xl flex items-center justify-center overflow-hidden p-2">
                    @if ($signature_photo)
                        <img src="{{ $signature_photo->temporaryUrl() }}" class="max-h-full max-w-full object-contain filter drop-shadow" alt="TTD Sementara">
                    @elseif ($existing_signature_path)
                        <img src="{{ \App\Models\User::find($user_id)?->signature_base64 }}" class="max-h-full max-w-full object-contain filter drop-shadow" alt="TTD Aktif">
                    @else
                        <span class="text-xs text-ink-500 font-sans text-center">Kolom TTD Basah</span>
                    @endif
                </div>
                <div class="text-xs font-sans text-ink-500 space-y-1">
                    <span class="text-ink-900 font-semibold block text-sm underline">{{ $this->formatted_name }}</span>
                    <span>SIPPK: {{ $sipa_number }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pengaturan Profil -->
    <form wire:submit.prevent="save" class="sanctuary-glass-card border border-blush-200 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <div>
            <h2 class="font-serif text-lg font-semibold text-blush-700 border-b border-blush-200 pb-2 mb-4 flex items-center gap-2">
                <span>🎓</span> 1. Identitas & Gelar Profesi Psikolog
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Gelar Depan <span class="lowercase text-ink-500 font-normal">(opsional)</span></label>
                    <input type="text" wire:model="title_prefix" placeholder="misal: dr." class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('title_prefix') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nama Lengkap *</label>
                    <input type="text" wire:model="name" placeholder="misal: Basirah" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('name') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Gelar Belakang Profesi</label>
                    <input type="text" wire:model="title_suffix" placeholder="misal: S.Psi., M.Psi., Psikolog" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('title_suffix') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nomor SIPPK / STR Profesi *</label>
                    <input type="text" wire:model="sipa_number" placeholder="misal: 503/123-SIPPK/2026" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('sipa_number') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Email Resmi *</label>
                    <input type="email" wire:model="email" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('email') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div>
            <h2 class="font-serif text-lg font-semibold text-blush-700 border-b border-blush-200 pb-2 mb-4 flex items-center gap-2">
                <span>🏢</span> 2. Data Tempat Praktik & Titimangsa Dokumen
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nama Tempat Praktik *</label>
                    <input type="text" wire:model="practice_name" placeholder="misal: MANDIRI atau Klinik Psikologi Sejahtera" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('practice_name') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Kota Domisili Praktik / Titimangsa *</label>
                    <input type="text" wire:model="practice_city" placeholder="misal: Selatpanjang" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @error('practice_city') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nomor Kontak / WhatsApp</label>
                    <input type="text" wire:model="phone" placeholder="misal: 08123456789" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">Alamat Lengkap Praktik</label>
                    <textarea wire:model="practice_address" rows="2" placeholder="Alamat kantor / tempat praktik..." class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition leading-relaxed"></textarea>
                </div>
            </div>
        </div>

        <!-- 3. Tanda Tangan Digital Resmi Psikolog -->
        <div>
            <h2 class="font-serif text-lg font-semibold text-blush-700 border-b border-blush-200 pb-2 mb-4 flex items-center gap-2">
                <span>✍️</span> 3. Tanda Tangan Digital Resmi Psikolog
            </h2>

            <div class="space-y-4">
                <p class="text-xs text-ink-500 font-sans leading-relaxed">
                    Unggah foto atau scan tanda tangan Anda untuk dicantumkan secara otomatis pada dokumen resmi seperti <strong>Rekam Pemeriksaan Psikologis (RPP Konseling)</strong> dan <strong>Logbook SKP IPK Tahunan</strong>.
                    Disarankan menggunakan gambar tanda tangan berlatar belakang <strong>transparan (PNG)</strong> atau latar belakang putih bersih.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    <!-- Area Upload -->
                    <div class="md:col-span-7 space-y-3">
                        <label class="block text-xs font-semibold uppercase text-ink-500">Pilih File Tanda Tangan (PNG, JPG, WEBP — Maks. 2MB)</label>
                        
                        <div class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-blush-200 hover:border-blush-200 rounded-2xl bg-white transition group cursor-pointer">
                            <input type="file" wire:model="signature_photo" accept="image/png,image/jpeg,image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="text-center space-y-2 pointer-events-none">
                                <div class="w-12 h-12 rounded-xl bg-blush-100 border border-blush-200 text-blush-700 flex items-center justify-center mx-auto group-hover:scale-105 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-blush-700 group-hover:underline">Klik untuk pilih file tanda tangan</span>
                                    <p class="text-[11px] text-ink-500 mt-0.5">atau drag & drop file ke area ini</p>
                                </div>
                            </div>

                            <!-- Uploading Indicator -->
                            <div wire:loading wire:target="signature_photo" class="absolute inset-0 bg-blush-50 rounded-2xl flex items-center justify-center z-20">
                                <div class="flex items-center gap-2 text-blush-700 text-xs font-mono">
                                    <svg class="animate-spin h-4 w-4 text-blush-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Mengunggah file tanda tangan...</span>
                                </div>
                            </div>
                        </div>

                        @error('signature_photo') 
                            <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Area Pratinjau Tanda Tangan -->
                    <div class="md:col-span-5 bg-blush-50 border border-blush-200 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-semibold text-blush-700 uppercase tracking-wider">Pratinjau TTD</span>
                            @if ($existing_signature_path || $signature_photo)
                                <span class="text-[10px] text-emerald-700 font-mono">✓ Siap Digunakan</span>
                            @else
                                <span class="text-[10px] text-ink-500 font-mono">Kosong</span>
                            @endif
                        </div>

                        <div class="h-48 bg-blush-50 rounded-xl border border-blush-200 flex items-center justify-center p-4 relative overflow-hidden group">
                            @if ($signature_photo)
                                <img src="{{ $signature_photo->temporaryUrl() }}" class="max-h-full max-w-full object-contain filter drop-shadow-md" alt="Preview TTD Baru">
                                <span class="absolute bottom-1 right-2 text-[9px] bg-blush-100 text-blush-700 px-1.5 py-0.5 rounded font-mono">File Baru Terpilih</span>
                            @elseif ($existing_signature_path)
                                <img src="{{ \App\Models\User::find($user_id)?->signature_base64 }}" class="max-h-full max-w-full object-contain filter drop-shadow-md" alt="Preview TTD Tersimpan">
                            @else
                                <div class="text-center p-4">
                                    <span class="text-2xl block mb-1 opacity-40">🖋️</span>
                                    <p class="text-[11px] text-ink-500">Belum ada tanda tangan digital yang diunggah.</p>
                                </div>
                            @endif
                        </div>

                        @if ($existing_signature_path)
                            <div class="pt-1 flex items-center justify-between">
                                <span class="text-[11px] text-ink-500">Tanda tangan aktif di dokumen</span>
                                <button type="button" 
                                        wire:click="deleteSignature" 
                                        wire:confirm="Yakin ingin menghapus tanda tangan digital ini? Dokumen akan kembali menyisakan kolom kosong untuk tanda tangan manual."
                                        class="px-2.5 py-1 bg-rose-500/15 hover:bg-rose-500/25 border border-rose-200 text-rose-700 rounded-lg text-xs font-medium transition active:scale-95 flex items-center gap-1">
                                    <span>🗑️</span>
                                    <span>Hapus Tanda Tangan</span>
                                </button>
                            </div>
                        @elseif ($signature_photo)
                            <div class="pt-1">
                                <span class="text-[11px] text-amber-800 block">⚠️ File baru dipilih, klik tombol <strong>"Simpan Profil Praktik"</strong> di bawah untuk menerapkan.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold rounded-xl transition shadow-lg shadow-blush-200/50 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                Simpan Profil Praktik
            </button>
        </div>
    </form>

    <!-- Informasi Lisensi Perangkat & Hardware Lock -->
    <div class="sanctuary-glass-card border border-blush-200 rounded-3xl p-6 sm:p-7 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-blush-200">
            <div class="flex items-center gap-2">
                <span class="text-lg">🛡️</span>
                <h2 class="font-serif text-base sm:text-lg font-semibold text-ink-900">Status Lisensi Perangkat</h2>
            </div>
            @if ($activeLicense)
                <span class="px-2.5 py-1 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-emerald-700 font-mono text-xs font-semibold">
                    ✓ TERAKTIVASI (LIFETIME)
                </span>
            @else
                <span class="px-2.5 py-1 rounded-full bg-amber-500/15 border border-amber-200 text-amber-800 font-mono text-xs font-semibold">
                    BELUM TERAKTIVASI
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-sans">
            <div class="bg-blush-50 p-3.5 rounded-2xl border border-blush-200">
                <span class="text-[10px] text-ink-500 block font-mono uppercase">Machine ID Laptop</span>
                <span class="font-mono text-blush-700 font-bold text-xs select-all mt-0.5 block">{{ $machineId }}</span>
            </div>
            <div class="bg-blush-50 p-3.5 rounded-2xl border border-blush-200">
                <span class="text-[10px] text-ink-500 block font-mono uppercase">Pemilik Terdaftar</span>
                <span class="text-ink-900 font-medium text-xs mt-0.5 block truncate">{{ $activeLicense?->client_name ?? 'Praktik Mandiri' }}</span>
            </div>
            <div class="bg-blush-50 p-3.5 rounded-2xl border border-blush-200">
                <span class="text-[10px] text-ink-500 block font-mono uppercase">Jenis Lisensi</span>
                <span class="text-blush-700 font-medium text-xs mt-0.5 block">Beli Putus (Offline Ed25519)</span>
            </div>
        </div>

        <p class="text-[11px] text-ink-500 font-sans leading-relaxed">
            Lisensi ini terkunci secara kriptografis ke motherboard/prosesor laptop ini. Jika Anda memindahkan data ke perangkat baru, hubungi developer untuk penerbitan lisensi baru.
        </p>
    </div>
</div>
