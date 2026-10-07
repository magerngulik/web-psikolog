<div class="p-6 sm:p-8 max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono mb-2">
                <span>Modul Kegiatan</span>
                <span>•</span>
                <span>Formulir Pendaftaran</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">Jadwalkan Kegiatan / Seminar</h1>
            <p class="text-sm text-slate-400 mt-1">Lengkapi data topik, mitra pengundang, waktu, tempat, honorarium, dan lampiran berkas resmi</p>
        </div>
        <div>
            <a href="{{ route('activities.index') }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Global Validation Error Alert -->
    @if ($errors->any())
        <div class="p-5 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-2xl text-sm font-medium flex items-start gap-3 backdrop-blur-sm shadow-lg shadow-rose-950/40" role="alert">
            <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="space-y-1">
                <span class="font-bold text-white block">Gagal menyimpan kegiatan!</span>
                <p class="text-xs text-rose-300/90">Mohon periksa kembali isian form berikut:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5 mt-1 font-mono">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">
        <!-- Section 1: Informasi Dasar & Mitra -->
        <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-6">
            <div class="border-b border-teal-500/10 pb-3">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>
                    1. Topik Acara & Mitra Penyelenggara
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Judul / Topik Kegiatan <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="title" id="title" type="text" placeholder="misal: Seminar Manajemen Stres & Regulasi Emosi Siswa Menjelang Ujian" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-slate-500 font-sans">
                    @error('title') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="event_type" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Jenis Kegiatan <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="event_type" id="event_type" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="seminar">Seminar</option>
                        <option value="webinar">Webinar (Online)</option>
                        <option value="workshop">Workshop / Pelatihan Praktik</option>
                        <option value="psychoeducation">Psikoedukasi Komunitas / Publik</option>
                        <option value="talkshow">Talkshow / Diskusi Panel</option>
                        <option value="training">Training & Human Capital</option>
                        <option value="other">Kegiatan Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Peran Psikolog <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="role" id="role" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="keynote_speaker">Narasumber / Pembicara Utama</option>
                        <option value="co_speaker">Co-Speaker / Pemateri Pendamping</option>
                        <option value="facilitator">Fasilitator / Trainer</option>
                        <option value="moderator">Moderator</option>
                        <option value="assessor">Asesor / Penguji</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="organization_id" class="text-xs font-bold uppercase tracking-wider text-slate-400 font-mono">
                            Mitra Lembaga / Instansi Pengundang
                        </label>
                        <button wire:click="openNewOrgModal" type="button" class="text-xs text-teal-400 hover:text-teal-300 font-bold flex items-center gap-1 transition">
                            <span>+ Tambah Mitra Baru</span>
                        </button>
                    </div>
                    <select wire:model.live="organization_id" id="organization_id" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="">-- Pilih Lembaga Mitra (Sekolah, Komunitas, Kampus, Perusahaan) --</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org->id }}">
                                {{ $org->name }} ({{ $org->category_label }}) {{ $org->city ? '— ' . $org->city : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Pilih mitra yang sudah terdaftar atau klik "+ Tambah Mitra Baru" jika instansi belum ada di daftar.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Waktu, Lokasi & Moda Pelaksanaan -->
        <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-6">
            <div class="border-b border-teal-500/10 pb-3">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    2. Waktu, Tempat & Moda Pertemuan
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Tanggal Mulai <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="start_date" id="start_date" type="date" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                    @error('start_date') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Tanggal Selesai <span class="text-slate-500 lowercase">(opsional)</span>
                    </label>
                    <input wire:model="end_date" id="end_date" type="date" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                </div>

                <div>
                    <label for="start_time" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Jam Mulai
                    </label>
                    <input wire:model="start_time" id="start_time" type="time" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Jam Selesai
                    </label>
                    <input wire:model="end_time" id="end_time" type="time" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                </div>

                <div>
                    <label for="delivery_mode" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Moda Pertemuan <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="delivery_mode" id="delivery_mode" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="offline">Tatap Muka (Offline)</option>
                        <option value="online">Virtual / Online (Zoom/GMeet)</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label for="location_venue" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Lokasi / Gedung / Tautan Zoom
                    </label>
                    <input wire:model="location_venue" id="location_venue" type="text" placeholder="misal: Aula Utama SMAN 1 / Link Zoom Webinar" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                </div>
            </div>
        </div>

        <!-- Section 3: Kontak Panitia & Target Audiens -->
        <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-6">
            <div class="border-b border-teal-500/10 pb-3">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    3. Kontak Panitia & Karakteristik Audiens
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2">
                    <label for="event_pic_name" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Nama PIC / Narahubung Lapangan
                    </label>
                    <input wire:model="event_pic_name" id="event_pic_name" type="text" placeholder="misal: Bpk. Ahmad (Ketua Panitia)" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                </div>

                <div class="md:col-span-2">
                    <label for="event_pic_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        No. WhatsApp PIC Lapangan
                    </label>
                    <input wire:model="event_pic_phone" id="event_pic_phone" type="text" placeholder="misal: 081234567890" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                </div>

                <div class="md:col-span-2">
                    <label for="target_audience" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Sasaran Audiens
                    </label>
                    <input wire:model="target_audience" id="target_audience" type="text" placeholder="misal: Siswa Kelas XII & Orang Tua Siswa" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                </div>

                <div>
                    <label for="estimated_audience" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Estimasi Peserta
                    </label>
                    <input wire:model="estimated_audience" id="estimated_audience" type="number" min="0" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                    @error('estimated_audience') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Status Pelaksanaan <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="status" id="status" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="scheduled">Terjadwal (Scheduled)</option>
                        <option value="in_progress">Sedang Berlangsung</option>
                        <option value="completed">Selesai (Completed)</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    @error('status') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 4: Administrasi, Honorarium & Poin SKP -->
        <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-6">
            <div class="border-b border-teal-500/10 pb-3 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>
                    4. Administrasi Honorarium & Nilai SKP
                </h2>
                <span class="text-xs text-teal-400 font-mono">Otomatis masuk akumulasi keuangan Dashboard jika Lunas</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="fee" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Honorarium / Biaya (Rp)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-mono text-slate-400">Rp</span>
                        <input wire:model="fee" id="fee" type="number" min="0" step="50000" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono font-bold">
                    </div>
                    @error('fee') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="payment_status" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Status Pembayaran <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="payment_status" id="payment_status" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                        <option value="unpaid">Belum Lunas (Unpaid)</option>
                        <option value="paid">Lunas (Paid)</option>
                        <option value="waived_pro_bono">Pro Bono / Sukarela</option>
                    </select>
                    @error('payment_status') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="payment_method" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Metode Pembayaran
                    </label>
                    <select wire:model="payment_method" id="payment_method" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Tunai / Cash</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div>
                    <label for="skp_points" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Poin SKP <span class="text-slate-500 lowercase">(wadah fleksibel)</span>
                    </label>
                    <input wire:model="skp_points" id="skp_points" type="number" step="0.05" min="0" placeholder="misal: 1.0" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-teal-300 text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                    @error('skp_points') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-4">
                    <label for="summary_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Ringkasan Pokok Materi / Evaluasi Pelaksanaan
                    </label>
                    <textarea wire:model="summary_notes" id="summary_notes" rows="3" placeholder="Catat poin-poin penting materi yang disampaikan, dinamika tanya-jawab peserta, atau tindak lanjut..." class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl p-4 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition leading-relaxed"></textarea>
                </div>
            </div>
        </div>

        <!-- Section 5: Unggah Berkas & Dokumentasi Digital -->
        <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-6">
            <div class="border-b border-teal-500/10 pb-3">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>
                    5. Unggah Berkas & Galeri Dokumentasi Digital
                </h2>
                <p class="text-xs text-slate-400 mt-1">Upload surat undangan resmi, sertifikat narasumber, dan foto dokumentasi kegiatan</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Surat Undangan -->
                <div class="p-5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📄</span>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-white font-mono">Surat Undangan / Surat Tugas</div>
                                <div class="text-[11px] text-slate-400">Format: PDF, Word, atau Gambar (Maks 10 MB)</div>
                            </div>
                        </div>
                        @if($invitation_file)
                            <button wire:click="removeInvitationFile" type="button" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">✕ Batalkan</button>
                        @endif
                    </div>
                    <input type="file" wire:model="invitation_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-500/15 file:text-teal-300 hover:file:bg-teal-500/25 cursor-pointer">
                    <div wire:loading wire:target="invitation_file" class="text-xs text-teal-400 font-mono">Mengunggah file surat...</div>
                    @error('invitation_file') <span class="text-rose-400 text-xs block font-mono">{{ $message }}</span> @enderror
                </div>

                <!-- Sertifikat Narasumber -->
                <div class="p-5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🎖️</span>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-white font-mono">Sertifikat / Piagam Narasumber</div>
                                <div class="text-[11px] text-slate-400">Format: PDF atau Gambar (Maks 10 MB)</div>
                            </div>
                        </div>
                        @if($certificate_file)
                            <button wire:click="removeCertificateFile" type="button" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">✕ Batalkan</button>
                        @endif
                    </div>
                    <input type="file" wire:model="certificate_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/15 file:text-emerald-300 hover:file:bg-emerald-500/25 cursor-pointer">
                    <div wire:loading wire:target="certificate_file" class="text-xs text-emerald-400 font-mono">Mengunggah file sertifikat...</div>
                    @error('certificate_file') <span class="text-rose-400 text-xs block font-mono">{{ $message }}</span> @enderror
                </div>

                <!-- Multi-Upload Foto Dokumentasi -->
                <div class="md:col-span-2 p-5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">📸</span>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-white font-mono">Foto Dokumentasi Kegiatan (Galeri)</div>
                                <div class="text-[11px] text-slate-400">Pilih satu atau beberapa foto kegiatan (JPG, PNG, WebP — Maks 5 MB per foto)</div>
                            </div>
                        </div>
                        <label class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 rounded-xl text-xs font-bold cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Pilih Foto Tambahan
                            <input type="file" wire:model="photos" multiple accept="image/*" class="hidden">
                        </label>
                    </div>

                    <div wire:loading wire:target="photos" class="text-xs text-teal-400 font-mono">
                        Memproses pratinjau foto...
                    </div>

                    <!-- Grid Preview Foto yang Diunggah -->
                    @if(!empty($photos))
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-2">
                            @foreach ($photos as $index => $photo)
                                <div class="relative group rounded-2xl overflow-hidden aspect-video border border-teal-500/30 bg-spruce-900 shadow-md">
                                    <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto" class="w-full h-full object-cover">
                                    <button wire:click="removePhoto({{ $index }})" type="button" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600/90 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-lg text-xs" title="Hapus foto ini">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('photos.*') <span class="text-rose-400 text-xs block font-mono">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('activities.index') }}" class="px-5 py-3 sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white rounded-xl text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-500/20 text-sm flex items-center gap-2">
                <span wire:loading.remove wire:target="save">Simpan Kegiatan & Berkas</span>
                <span wire:loading wire:target="save">Menyimpan kegiatan...</span>
            </button>
        </div>
    </form>

    <!-- Modal Quick Add Mitra -->
    @if ($showNewOrgModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="sanctuary-glass-card rounded-3xl border border-teal-500/30 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-3">
                    <h3 class="text-lg font-serif font-bold text-white">Tambah Mitra Lembaga Cepat</h3>
                    <button wire:click="$set('showNewOrgModal', false)" type="button" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">
                            Nama Lembaga <span class="text-rose-400">*</span>
                        </label>
                        <input wire:model="newOrgName" type="text" placeholder="misal: SMA Negeri 1 Pekanbaru" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400">
                        @error('newOrgName') <span class="text-rose-400 text-xs block font-mono mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">Kategori</label>
                            <select wire:model="newOrgCategory" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs">
                                <option value="school">Sekolah</option>
                                <option value="university">Universitas</option>
                                <option value="community">Komunitas</option>
                                <option value="corporate">Korporat</option>
                                <option value="government">Pemerintah</option>
                                <option value="ngo">LSM / Yayasan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">Kota</label>
                            <input wire:model="newOrgCity" type="text" placeholder="misal: Pekanbaru" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">Nama PIC</label>
                            <input wire:model="newOrgPicName" type="text" placeholder="misal: Ibu Rina (BK)" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">No. WhatsApp PIC</label>
                            <input wire:model="newOrgPicPhone" type="text" placeholder="misal: 081234567890" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-xs font-mono">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-teal-500/10">
                    <button wire:click="$set('showNewOrgModal', false)" type="button" class="px-4 py-2 sanctuary-glass text-slate-300 rounded-xl text-xs font-medium">Batal</button>
                    <button wire:click="saveNewOrg" type="button" class="px-5 py-2 bg-teal-500 text-slate-950 font-bold rounded-xl text-xs">Simpan Mitra</button>
                </div>
            </div>
        </div>
    @endif
</div>

