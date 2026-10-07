<div class="p-6 sm:p-8 max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono mb-2">
                <span>Modul Kegiatan</span>
                <span>•</span>
                <span>Edit Informasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">Edit Kegiatan / Seminar</h1>
            <p class="text-sm text-slate-400 mt-1">Perbarui topik, tanggal, status pembayaran, atau unggah berkas dokumentasi tambahan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('activities.show', $activity->id) }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Detail
            </a>
            <button wire:click="deleteActivity" wire:confirm="Apakah Anda yakin ingin menghapus seluruh data kegiatan ini beserta berkasnya?" type="button" class="sanctuary-glass border border-rose-500/30 hover:border-rose-500 hover:bg-rose-500/10 text-rose-300 hover:text-rose-200 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2" title="Hapus Kegiatan">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        </div>
    </div>

    <!-- Global Validation Error Alert -->
    @if ($errors->any())
        <div class="p-5 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-2xl text-sm font-medium flex items-start gap-3 backdrop-blur-sm shadow-lg shadow-rose-950/40" role="alert">
            <svg class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="space-y-1">
                <span class="font-bold text-white block">Gagal memperbarui kegiatan!</span>
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
                    <input wire:model="title" id="title" type="text" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-sans">
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
                    <label for="organization_id" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Mitra Lembaga / Instansi Pengundang
                    </label>
                    <select wire:model="organization_id" id="organization_id" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        <option value="">-- Tanpa Mitra Terdaftar (Mandiri / Umum) --</option>
                        @foreach ($organizations as $org)
                            <option value="{{ $org->id }}">
                                {{ $org->name }} ({{ $org->category_label }}) {{ $org->city ? '— ' . $org->city : '' }}
                            </option>
                        @endforeach
                    </select>
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
                </div>

                <div>
                    <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Tanggal Selesai
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
                        Lokasi / Gedung / Link
                    </label>
                    <input wire:model="location_venue" id="location_venue" type="text" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                </div>
            </div>
        </div>

        <!-- Section 3: Kontak Panitia & Karakteristik Audiens -->
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
                        Nama PIC Lapangan
                    </label>
                    <input wire:model="event_pic_name" id="event_pic_name" type="text" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                </div>

                <div class="md:col-span-2">
                    <label for="event_pic_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        No. WhatsApp PIC Lapangan
                    </label>
                    <input wire:model="event_pic_phone" id="event_pic_phone" type="text" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                </div>

                <div class="md:col-span-2">
                    <label for="target_audience" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Sasaran Audiens
                    </label>
                    <input wire:model="target_audience" id="target_audience" type="text" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                </div>

                <div>
                    <label for="estimated_audience" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Estimasi Peserta
                    </label>
                    <input wire:model="estimated_audience" id="estimated_audience" type="number" min="0" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
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
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="fee" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Honorarium / Biaya (Rp) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-mono text-slate-400">Rp</span>
                        <input wire:model="fee" id="fee" type="number" min="0" step="50000" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono font-bold">
                    </div>
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
                        Poin SKP
                    </label>
                    <input wire:model="skp_points" id="skp_points" type="number" step="0.05" min="0" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-teal-300 text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                </div>

                <div class="md:col-span-4">
                    <label for="summary_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 font-mono">
                        Ringkasan Pokok Materi / Evaluasi Pelaksanaan
                    </label>
                    <textarea wire:model="summary_notes" id="summary_notes" rows="3" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl p-4 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition leading-relaxed"></textarea>
                </div>
            </div>
        </div>

        <!-- Section 5: Kelola Berkas Lampiran & Foto Dokumentasi -->
        <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-6">
            <div class="border-b border-teal-500/10 pb-3 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>
                    5. Berkas Lampiran & Foto Dokumentasi
                </h2>
                <span class="text-xs font-mono text-teal-400">Total: {{ $activity->attachments->count() }} Berkas Tersimpan</span>
            </div>

            <!-- Bagian 5A: Berkas yang Sudah Diunggah Sebelumnya -->
            @if($activity->attachments->count() > 0)
                <div class="p-5 rounded-2xl bg-spruce-950/80 border border-teal-500/20 space-y-4">
                    <div class="flex items-center justify-between border-b border-teal-500/10 pb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 font-mono">Berkas Tersimpan Saat Ini (Klik untuk Hapus jika Salah Unggah)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Surat Undangan yang Sudah Ada -->
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono block mb-2">📄 Surat Undangan / Tugas</span>
                            <div class="space-y-2">
                                @forelse($activity->invitationLetters as $inv)
                                    <div class="p-3 rounded-xl bg-spruce-900/60 border border-teal-500/15 flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs text-white font-medium truncate" title="{{ $inv->file_name }}">{{ $inv->file_name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $inv->formatted_size }}</div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <a href="{{ $inv->url }}" target="_blank" class="px-2.5 py-1 bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 rounded-lg text-xs font-semibold">Lihat ↗</a>
                                            <button wire:click="deleteAttachment('{{ $inv->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus surat undangan ini?" type="button" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 hover:text-rose-200 border border-rose-500/20 rounded-lg text-xs font-semibold flex items-center gap-1 transition" title="Hapus berkas ini">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500 italic p-2 bg-spruce-900/30 rounded-xl">Belum ada surat undangan tersimpan.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Sertifikat yang Sudah Ada -->
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono block mb-2">🎖️ Sertifikat Narasumber</span>
                            <div class="space-y-2">
                                @forelse($activity->certificates as $cert)
                                    <div class="p-3 rounded-xl bg-spruce-900/60 border border-teal-500/15 flex items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs text-white font-medium truncate" title="{{ $cert->file_name }}">{{ $cert->file_name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $cert->formatted_size }}</div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <a href="{{ $cert->url }}" target="_blank" class="px-2.5 py-1 bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 rounded-lg text-xs font-semibold">Lihat ↗</a>
                                            <button wire:click="deleteAttachment('{{ $cert->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus sertifikat ini?" type="button" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 hover:text-rose-200 border border-rose-500/20 rounded-lg text-xs font-semibold flex items-center gap-1 transition" title="Hapus sertifikat ini">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500 italic p-2 bg-spruce-900/30 rounded-xl">Belum ada sertifikat tersimpan.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Foto Dokumentasi yang Sudah Ada -->
                        @if($activity->photos->count() > 0)
                            <div class="md:col-span-2 pt-2 border-t border-teal-500/10">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono block mb-2">📸 Foto Dokumentasi Kegiatan ({{ $activity->photos->count() }} Foto)</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    @foreach($activity->photos as $ph)
                                        <div class="relative group rounded-xl overflow-hidden aspect-video border border-teal-500/20 bg-spruce-950 shadow-sm">
                                            <img src="{{ $ph->url }}" alt="{{ $ph->caption }}" class="w-full h-full object-cover">
                                            <button wire:click="deleteAttachment('{{ $ph->id }}')" wire:confirm="Hapus foto dokumentasi ini?" type="button" class="absolute top-1 right-1 p-1 rounded-lg bg-rose-600/90 text-white hover:bg-rose-600 shadow transition opacity-90 sm:opacity-0 group-hover:opacity-100" title="Hapus foto ini">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Bagian 5B: Unggah Berkas Baru / Tambahan -->
            <div class="border-t border-teal-500/10 pt-4 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-300 font-mono block">Unggah Berkas Baru atau Tambahan (Opsional)</span>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tambah Surat Undangan Baru -->
                    <div class="p-5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-white font-mono block">Unggah Surat Undangan Baru</span>
                            @if($new_invitation_file)
                                <button wire:click="removeNewInvitationFile" type="button" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">✕ Batalkan</button>
                            @endif
                        </div>
                        <input type="file" wire:model="new_invitation_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-500/15 file:text-teal-300 cursor-pointer">
                        <div wire:loading wire:target="new_invitation_file" class="text-xs text-teal-400 font-mono">Mengunggah file...</div>
                        @error('new_invitation_file') <span class="text-rose-400 text-xs block font-mono">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tambah Sertifikat Baru -->
                    <div class="p-5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-white font-mono block">Unggah Sertifikat Baru</span>
                            @if($new_certificate_file)
                                <button wire:click="removeNewCertificateFile" type="button" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">✕ Batalkan</button>
                            @endif
                        </div>
                        <input type="file" wire:model="new_certificate_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/15 file:text-emerald-300 cursor-pointer">
                        <div wire:loading wire:target="new_certificate_file" class="text-xs text-emerald-400 font-mono">Mengunggah file...</div>
                        @error('new_certificate_file') <span class="text-rose-400 text-xs block font-mono">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tambah Foto Baru -->
                    <div class="md:col-span-2 p-5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 space-y-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-white font-mono block">Unggah Foto Dokumentasi Tambahan</span>
                        <input type="file" wire:model="new_photos" multiple accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-500/15 file:text-teal-300 cursor-pointer">
                        <div wire:loading wire:target="new_photos" class="text-xs text-teal-400 font-mono">Memproses file foto...</div>

                        @if(!empty($new_photos))
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-2">
                                @foreach($new_photos as $idx => $photo)
                                    <div class="relative group rounded-xl overflow-hidden aspect-video border border-teal-500/30 bg-spruce-900 shadow">
                                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview baru" class="w-full h-full object-cover">
                                        <button wire:click="removeNewPhoto({{ $idx }})" type="button" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-xs shadow" title="Batalkan foto ini">✕</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('new_photos.*') <span class="text-rose-400 text-xs block font-mono">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('activities.show', $activity->id) }}" class="px-5 py-3 sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white rounded-xl text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-500/20 text-sm flex items-center gap-2">
                <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                <span wire:loading wire:target="save">Menyimpan perubahan...</span>
            </button>
        </div>
    </form>

    <!-- Danger Zone: Hapus Kegiatan -->
    <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-rose-500/25 bg-rose-950/10 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-serif font-bold text-rose-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Zona Bahaya: Hapus Seluruh Kegiatan
                </h3>
                <p class="text-xs text-slate-400 mt-1 max-w-xl">
                    Jika kegiatan ini tidak valid atau keliru dibuat, Anda dapat menghapusnya secara permanen dari agenda dan kalkulasi pendapatan Anda.
                </p>
            </div>
            <button wire:click="deleteActivity" wire:confirm="Apakah Anda yakin ingin menghapus kegiatan '{{ addslashes($activity->title) }}' beserta seluruh berkasnya?" type="button" class="px-5 py-2.5 bg-rose-600/80 hover:bg-rose-600 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-rose-950/50 flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus Kegiatan Ini
            </button>
        </div>
    </div>
</div>

