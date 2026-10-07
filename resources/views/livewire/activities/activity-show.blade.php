<div class="p-6 sm:p-8 max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono">
                    {{ $activity->event_type_label }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-spruce-900 text-slate-300 border border-teal-500/10 font-mono">
                    {{ $activity->delivery_mode_label }}
                </span>
                <span class="text-xs font-mono text-slate-500">{{ $activity->activity_code }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight mt-2">{{ $activity->title }}</h1>
            <p class="text-sm text-slate-400 mt-1 flex flex-wrap items-center gap-2">
                <span>Penyelenggara:</span>
                @if($activity->organization)
                    <a href="{{ route('activities.index', ['organization_id' => $activity->organization->id]) }}" class="text-teal-400 font-semibold hover:text-teal-300 underline underline-offset-4 transition">
                        {{ $activity->organization->name }}
                    </a>
                @else
                    <span class="text-slate-300 font-medium">{{ $activity->organizer_name ?: 'Mandiri / Umum' }}</span>
                @endif
                <span>•</span>
                <span class="font-mono">📅 {{ \Carbon\Carbon::parse($activity->start_date)->format('d F Y') }}</span>
                @if($activity->start_time)
                    <span>•</span>
                    <span class="font-mono">⏰ {{ \Carbon\Carbon::parse($activity->start_time)->format('H:i') }} {{ $activity->end_time ? '- ' . \Carbon\Carbon::parse($activity->end_time)->format('H:i') . ' WIB' : '' }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('activities.index') }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <a href="{{ route('activities.edit', $activity->id) }}" class="sanctuary-glass border border-teal-500/30 hover:border-teal-400 text-teal-300 hover:text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Kegiatan
            </a>
            <button wire:click="$set('showUploadModal', true)" type="button" class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition text-sm shadow-lg shadow-teal-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                + Tambah Berkas / Foto
            </button>
            <button wire:click="deleteActivity" wire:confirm="Apakah Anda yakin ingin menghapus seluruh data kegiatan ini beserta berkasnya?" type="button" class="sanctuary-glass border border-rose-500/30 hover:border-rose-500 hover:bg-rose-500/10 text-rose-300 hover:text-rose-200 px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2" title="Hapus Kegiatan">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 rounded-2xl text-sm font-medium flex items-center gap-3 backdrop-blur-sm shadow-lg shadow-emerald-950/20" role="alert">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Kolom Kiri: Info Mitra, Status, Administrasi, & Evaluasi -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Card Status & Peran -->
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 space-y-5 shadow-2xl">
                <div class="border-b border-teal-500/10 pb-3 flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        Status & Peran
                    </h2>
                    <span class="text-xs font-mono text-teal-400 uppercase tracking-wider font-semibold">
                        {{ $activity->role_label }}
                    </span>
                </div>

                <!-- Status Acara Pill Selector -->
                <div>
                    <label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2 font-mono">Status Pelaksanaan</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (['scheduled' => 'Terjadwal', 'completed' => 'Selesai', 'cancelled' => 'Batal'] as $st => $lbl)
                            <button wire:click="markStatus('{{ $st }}')" type="button" class="px-3 py-2 rounded-xl text-xs font-semibold tracking-wide transition text-center {{ $activity->status === $st ? 'bg-teal-500 text-slate-950 font-bold shadow-md shadow-teal-500/25 ring-2 ring-teal-400/40' : 'bg-spruce-950/60 text-slate-400 hover:text-white hover:bg-spruce-900 border border-teal-500/10' }}">
                                {{ $lbl }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Info Lokasi & Waktu -->
                <div class="space-y-3 pt-2 border-t border-teal-500/10 text-xs">
                    <div>
                        <span class="text-slate-400 block font-mono uppercase text-[11px]">Tempat / Lokasi / Link:</span>
                        <div class="text-sm font-semibold text-white mt-0.5">
                            {{ $activity->location_venue ?: 'Belum ditentukan' }}
                        </div>
                    </div>
                    @if($activity->target_audience || $activity->estimated_audience)
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-slate-400 block font-mono uppercase text-[11px]">Target Audiens:</span>
                                <div class="text-white font-medium mt-0.5">{{ $activity->target_audience ?: '-' }}</div>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-mono uppercase text-[11px]">Estimasi Peserta:</span>
                                <div class="text-emerald-400 font-mono font-bold mt-0.5">{{ $activity->estimated_audience ?: 0 }} Orang</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Mitra Organisasi & PIC -->
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 space-y-4 shadow-2xl">
                <div class="border-b border-teal-500/10 pb-3 flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Mitra & Narahubung Lapangan
                    </h2>
                </div>

                @if($activity->organization)
                    <div class="bg-spruce-950/70 p-4 rounded-2xl border border-teal-500/15 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="font-bold text-white text-base">{{ $activity->organization->name }}</div>
                            <span class="text-[11px] font-mono text-teal-400 px-2 py-0.5 rounded-full bg-teal-500/10 border border-teal-500/20">
                                {{ $activity->organization->category_label }}
                            </span>
                        </div>
                        @if($activity->organization->city)
                            <div class="text-xs text-slate-400">📍 {{ $activity->organization->city }} — {{ $activity->organization->address }}</div>
                        @endif
                    </div>
                @endif

                <!-- PIC Contact -->
                @php
                    $picName = $activity->event_pic_name ?: ($activity->organization?->pic_name ?: null);
                    $picPhone = $activity->event_pic_phone ?: ($activity->organization?->pic_phone ?: null);
                @endphp
                @if($picName || $picPhone)
                    <div class="p-4 rounded-2xl bg-spruce-950/50 border border-teal-500/10 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-mono uppercase text-slate-400 block">Narahubung (PIC):</span>
                            <div class="text-sm font-bold text-white">{{ $picName ?: 'Panitia Acara' }}</div>
                            <div class="text-xs text-slate-400 font-mono">{{ $picPhone }}</div>
                        </div>
                        @if($picPhone)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $picPhone);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $cleanPhone }}?text=Halo%20{{ urlencode($picName) }},%20saya%20terkait%20kegiatan%20{{ urlencode($activity->title) }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0">
                                <span>💬</span>
                                <span>WhatsApp PIC</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Card Keuangan & SKP Points -->
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 space-y-4 shadow-2xl">
                <div class="border-b border-teal-500/10 pb-3 flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        Honorarium & Poin SKP
                    </h2>
                    @if($activity->skp_points)
                        <span class="text-xs font-mono font-bold text-amber-300 bg-amber-500/10 border border-amber-500/25 px-2.5 py-0.5 rounded-full">
                            {{ $activity->skp_points }} SKP
                        </span>
                    @endif
                </div>

                <div class="bg-spruce-950/70 p-4 rounded-2xl border border-teal-500/15 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-mono text-slate-400 uppercase">Nilai Honorarium:</span>
                        <span class="text-xl font-mono font-bold text-white">Rp {{ number_format($activity->fee, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-2 border-t border-teal-500/10 flex items-center justify-between">
                        <span class="text-xs font-mono text-slate-400 uppercase">Status Pembayaran:</span>
                        <div class="flex items-center gap-1.5">
                            @foreach (['unpaid' => 'Belum Lunas', 'paid' => 'Lunas', 'waived_pro_bono' => 'Pro Bono'] as $pStatus => $pLabel)
                                <button wire:click="markPaymentStatus('{{ $pStatus }}')" type="button" class="px-2.5 py-1 rounded-lg text-xs font-mono font-semibold transition {{ $activity->payment_status === $pStatus ? 'bg-emerald-500 text-slate-950 font-bold shadow' : 'bg-spruce-900 text-slate-400 hover:text-white border border-teal-500/10' }}">
                                    {{ $pLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Catatan Evaluasi & Pokok Materi -->
            <div class="sanctuary-glass-card rounded-3xl p-6 border border-teal-500/15 space-y-4 shadow-2xl">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-3">
                    <h2 class="font-serif text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                        Catatan Evaluasi / Materi
                    </h2>
                </div>
                <textarea wire:model="summary_notes" rows="4" placeholder="Catat poin-poin penting materi, tanggapan peserta, atau hal yang perlu dievaluasi..." class="w-full bg-spruce-950/80 border border-teal-500/20 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 rounded-2xl p-4 text-sm text-white placeholder-slate-500 transition leading-relaxed"></textarea>
                <button wire:click="updateNotes" type="button" class="w-full py-2.5 bg-spruce-900 hover:bg-spruce-800 text-teal-300 hover:text-teal-200 border border-teal-500/25 font-bold rounded-xl text-sm transition">
                    Simpan Catatan
                </button>
            </div>
        </div>

        <!-- Kolom Kanan: Berkas Dokumen & Galeri Foto Dokumentasi -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Dokumen Resmi: Surat Undangan & Sertifikat -->
            <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-4">
                    <h2 class="font-serif text-xl font-bold text-white tracking-tight flex items-center gap-2">
                        <span>📜</span>
                        Berkas Resmi & Administrasi
                    </h2>
                    <span class="text-xs font-mono text-teal-400">Dokumen Digital</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Surat Undangan / Tugas -->
                    <div class="p-5 rounded-2xl bg-spruce-950/70 border border-teal-500/15 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">📄</span>
                            <div>
                                <h3 class="font-serif font-bold text-white text-sm">Surat Undangan / Tugas</h3>
                                <p class="text-[11px] text-slate-400 font-mono">Arsip resmi kegiatan</p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-teal-500/10 space-y-2.5">
                            @forelse($activity->invitationLetters as $invitation)
                                <div class="p-2.5 rounded-xl bg-spruce-900/60 border border-teal-500/15 space-y-2">
                                    <div class="text-xs text-white font-medium truncate" title="{{ $invitation->file_name }}">{{ $invitation->file_name }}</div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-slate-400 font-mono">{{ $invitation->formatted_size }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ $invitation->url }}" target="_blank" class="px-2.5 py-1 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/30 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Buka berkas">
                                                <span>Lihat</span>
                                                <span>↗</span>
                                            </a>
                                            <button wire:click="deleteAttachment('{{ $invitation->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus surat undangan ini?" type="button" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 hover:text-rose-200 border border-rose-500/20 rounded-lg text-xs font-semibold transition flex items-center gap-1" title="Hapus berkas ini">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-xs text-slate-500 italic py-2">
                                    Belum ada surat undangan yang diunggah.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Sertifikat Narasumber -->
                    <div class="p-5 rounded-2xl bg-spruce-950/70 border border-teal-500/15 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🎖️</span>
                            <div>
                                <h3 class="font-serif font-bold text-white text-sm">Sertifikat Narasumber</h3>
                                <p class="text-[11px] text-slate-400 font-mono">Piagam & pengakuan profesi</p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-teal-500/10 space-y-2.5">
                            @forelse($activity->certificates as $certificate)
                                <div class="p-2.5 rounded-xl bg-spruce-900/60 border border-teal-500/15 space-y-2">
                                    <div class="text-xs text-white font-medium truncate" title="{{ $certificate->file_name }}">{{ $certificate->file_name }}</div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="text-slate-400 font-mono">{{ $certificate->formatted_size }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ $certificate->url }}" target="_blank" class="px-2.5 py-1 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Buka sertifikat">
                                                <span>Lihat</span>
                                                <span>↗</span>
                                            </a>
                                            <button wire:click="deleteAttachment('{{ $certificate->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus sertifikat ini?" type="button" class="px-2.5 py-1 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 hover:text-rose-200 border border-rose-500/20 rounded-lg text-xs font-semibold transition flex items-center gap-1" title="Hapus sertifikat ini">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-xs text-slate-500 italic py-2">
                                    Belum ada sertifikat yang diunggah.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Galeri Foto Dokumentasi Kegiatan -->
            <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-4">
                    <div class="flex items-center gap-3">
                        <h2 class="font-serif text-xl font-bold text-white tracking-tight flex items-center gap-2">
                            <span>📸</span>
                            Galeri Foto Dokumentasi
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-teal-500/10 border border-teal-500/25 text-teal-300 font-mono text-xs font-bold">
                            {{ $activity->photos->count() }} Foto
                        </span>
                    </div>
                    <button wire:click="$set('showUploadModal', true)" type="button" class="text-xs text-teal-400 hover:text-teal-300 font-bold flex items-center gap-1 transition">
                        <span>+ Tambah Foto</span>
                    </button>
                </div>

                @if($activity->photos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($activity->photos as $photo)
                            <div class="relative group rounded-2xl overflow-hidden aspect-video border border-teal-500/20 bg-spruce-950 shadow-md">
                                <img src="{{ $photo->url }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300 cursor-pointer" wire:click="openLightbox('{{ $photo->url }}')">
                                <!-- Top Action: Quick Delete Button -->
                                <button wire:click="deleteAttachment('{{ $photo->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus foto dokumentasi ini?" type="button" class="absolute top-2 right-2 p-1.5 rounded-xl bg-slate-950/80 hover:bg-rose-900 text-rose-300 hover:text-white border border-rose-500/30 transition shadow-lg opacity-80 sm:opacity-0 group-hover:opacity-100" title="Hapus foto ini">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <!-- Bottom Bar -->
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-transparent p-3 flex items-center justify-between opacity-90 sm:opacity-0 group-hover:opacity-100 transition duration-200">
                                    <button wire:click="openLightbox('{{ $photo->url }}')" type="button" class="text-xs text-teal-300 hover:text-white font-semibold flex items-center gap-1">
                                        <span>Perbesar</span>
                                        <span>🔍</span>
                                    </button>
                                    <button wire:click="deleteAttachment('{{ $photo->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus foto dokumentasi ini?" type="button" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">
                                        Hapus Foto
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center border border-dashed border-teal-500/20 rounded-2xl space-y-2">
                        <div class="text-3xl">📷</div>
                        <h4 class="font-serif font-bold text-white text-sm">Belum Ada Foto Dokumentasi</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Unggah foto suasana acara, interaksi peserta, atau foto bersama untuk portofolio Anda.</p>
                        <div class="pt-2">
                            <button wire:click="$set('showUploadModal', true)" type="button" class="px-4 py-2 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/30 rounded-xl text-xs font-bold transition">
                                + Unggah Foto Dokumentasi
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Danger Zone: Hapus Seluruh Kegiatan -->
    <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-rose-500/20 bg-rose-950/10 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-serif font-bold text-rose-300 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Hapus Kegiatan Ini
                </h3>
                <p class="text-xs text-slate-400 mt-1 max-w-xl">
                    Jika data kegiatan ini salah diinput atau dibatalkan, Anda dapat menghapusnya. Seluruh berkas lampiran dan riwayat kegiatan ini akan diarsipkan dan dikeluarkan dari perhitungan pendapatan.
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

    <!-- Modal Upload Berkas Cepat -->
    @if ($showUploadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="sanctuary-glass-card rounded-3xl border border-teal-500/30 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-3">
                    <h3 class="text-lg font-serif font-bold text-white">Unggah Berkas / Foto Kegiatan</h3>
                    <button wire:click="$set('showUploadModal', false)" type="button" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form wire:submit.prevent="saveUploadedFiles" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">Tipe Berkas</label>
                        <select wire:model="upload_type" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400">
                            <option value="documentation_photo">Foto Dokumentasi Kegiatan</option>
                            <option value="invitation_letter">Surat Undangan / Surat Tugas</option>
                            <option value="certificate">Sertifikat / Piagam Narasumber</option>
                            <option value="presentation_deck">Materi / Slide Presentasi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">Pilih File</label>
                        <input type="file" wire:model="uploaded_files" multiple class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-500/20 file:text-teal-300 hover:file:bg-teal-500/30 cursor-pointer">
                        <div wire:loading wire:target="uploaded_files" class="text-xs text-teal-400 font-mono mt-1">Mengunggah file...</div>
                        @error('uploaded_files.*') <span class="text-rose-400 text-xs block font-mono mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-teal-500/10">
                        <button wire:click="$set('showUploadModal', false)" type="button" class="px-4 py-2 sanctuary-glass text-slate-300 rounded-xl text-xs font-medium">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 font-bold rounded-xl text-xs">Simpan Berkas</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Lightbox Fullscreen Preview Foto -->
    @if ($selectedPhotoUrl)
        <div class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-lg flex items-center justify-center p-4 cursor-pointer" wire:click="closeLightbox">
            <div class="relative max-w-4xl max-h-[90vh]">
                <img src="{{ $selectedPhotoUrl }}" alt="Dokumentasi Fullsize" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border border-teal-500/30">
                <button wire:click="closeLightbox" type="button" class="absolute -top-4 -right-4 w-10 h-10 rounded-full bg-slate-900 border border-teal-500/40 text-white font-bold flex items-center justify-center hover:bg-slate-800 transition">
                    ✕
                </button>
            </div>
        </div>
    @endif
</div>

