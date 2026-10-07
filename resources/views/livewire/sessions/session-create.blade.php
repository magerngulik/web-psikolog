<div class="p-6 sm:p-8 max-w-4xl mx-auto space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono mb-2">
                <span>Modul Konseling</span>
                <span>•</span>
                <span>Penjadwalan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">Jadwalkan Sesi Konseling</h1>
            <p class="text-sm text-slate-400 mt-1">Pilih kasus medis klien & tentukan tanggal serta jam pertemuan klinis</p>
        </div>
        <div>
            <a href="{{ route('sessions.index') }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <form wire:submit.prevent="save" class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-8">
        <!-- Kasus & Nomor Sesi -->
        <div>
            <div class="border-b border-teal-500/10 pb-3 mb-5">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                    Kasus Medis & Penomoran Sesi
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label for="medical_case_id" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Pilih Kasus Medis Klien <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <select wire:model.live="medical_case_id" id="medical_case_id" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm appearance-none cursor-pointer">
                            <option value="">-- Pilih Kasus Aktif Klien --</option>
                            @foreach ($cases as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->client->full_name }} — {{ $c->title }} ({{ $c->category ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('medical_case_id') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="session_number" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Sesi Ke- <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="session_number" id="session_number" type="number" min="1" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-teal-400 focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono font-bold text-base">
                    @error('session_number') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Waktu Pelaksanaan -->
        <div>
            <div class="border-b border-teal-500/10 pb-3 mb-5">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Jadwal Pertemuan Klinis
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="session_date" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Tanggal Pertemuan <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="session_date" id="session_date" type="date" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm font-mono">
                    @error('session_date') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Jam Mulai <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="start_time" id="start_time" type="time" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm font-mono">
                    @error('start_time') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Jam Selesai <span class="text-rose-400">*</span>
                    </label>
                    <input wire:model="end_time" id="end_time" type="time" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm font-mono">
                    @error('end_time') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Biaya & Status Pembayaran -->
        <div>
            <div class="border-b border-teal-500/10 pb-3 mb-5">
                <h2 class="text-base font-serif font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                    Administrasi Biaya & Status
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label for="fee" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Biaya Konseling (Rp)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-mono text-slate-400">Rp</span>
                        <input wire:model="fee" id="fee" type="number" min="0" step="10000" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl pl-9 pr-3 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono text-sm">
                    </div>
                    @error('fee') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="payment_status" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Status Pembayaran
                    </label>
                    <select wire:model="payment_status" id="payment_status" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm">
                        <option value="unpaid">Unpaid (Belum Bayar)</option>
                        <option value="paid">Paid (Sudah Bayar)</option>
                        <option value="waived">Waived (Bebas Biaya)</option>
                    </select>
                </div>

                <div>
                    <label for="payment_method" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Metode Pembayaran
                    </label>
                    <select wire:model="payment_method" id="payment_method" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm">
                        <option value="cash">Tunai / Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Status Sesi Initial
                    </label>
                    <select wire:model="status" id="status" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm">
                        <option value="scheduled">Scheduled (Terjadwal)</option>
                        <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Moda Pelayanan & Klasifikasi SKP IPK -->
        <div class="pt-2">
            <div class="border-b border-teal-500/10 pb-3 mb-5 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <h2 class="text-base font-serif font-bold text-white">Klasifikasi Logbook & SKP IPK</h2>
                </div>
                <span class="text-xs text-teal-400 font-mono">Akumulasi Otomatis ke Laporan Tahunan</span>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label for="service_modality" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 font-mono">
                        Moda Pelayanan / Jenis Tindakan <span class="text-rose-400">*</span>
                    </label>
                    <select wire:model="service_modality" id="service_modality" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition text-sm">
                        <option value="individual_direct">Tatap Muka Klien Individual Langsung (0.05 SKP)</option>
                        <option value="individual_virtual">Komunikasi Virtual / Video Call Klien (0.05 SKP)</option>
                        <option value="group">Tatap Muka Kelompok / Komunitas (0.10 SKP)</option>
                        <option value="phone">Konsultasi Telepon Suara > 15 Menit (0.02 SKP)</option>
                        <option value="chat_text">Konsultasi Tulisan / Chat > 100 Kata (0.02 SKP)</option>
                        <option value="legal_visum">Tim Visum et Repertum Psikiatrikum (0.02 SKP)</option>
                        <option value="legal_witness">Saksi Ahli di Pengadilan (0.05 SKP)</option>
                        <option value="legal_court_report">Laporan Pemeriksaan Alat Bukti Sidang (0.01 SKP)</option>
                    </select>
                    @error('service_modality') <span class="text-rose-400 text-xs mt-1.5 block font-mono">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-3">
                    <label class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 hover:border-teal-500/30 transition cursor-pointer">
                        <input wire:model="is_high_risk" id="is_high_risk" type="checkbox" class="mt-1 w-5 h-5 rounded bg-spruce-900 border-teal-500/30 text-amber-500 focus:ring-amber-500/50">
                        <div>
                            <span class="text-sm font-semibold text-slate-200">Tugas di Tempat Berisiko Tinggi</span>
                            <p class="text-xs text-slate-400 mt-0.5">Centang jika bertugas di daerah konflik, bencana alam, atau lapas berisiko (Poin tambahan 0.05 SKP)</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-spruce-950/60 border border-teal-500/15 hover:border-teal-500/30 transition cursor-pointer">
                        <input wire:model="generates_report" id="generates_report" type="checkbox" class="mt-1 w-5 h-5 rounded bg-spruce-900 border-teal-500/30 text-teal-400 focus:ring-teal-400/50">
                        <div>
                            <span class="text-sm font-semibold text-slate-200">Menyusun Rekam Pemeriksaan Psikologis (RPP)</span>
                            <p class="text-xs text-slate-400 mt-0.5">Otomatis dihitung poin penyusunan dokumen laporan klinis resmi (Poin 0.01 SKP)</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-teal-500/10">
            <a href="{{ route('sessions.index') }}" class="px-5 py-2.5 sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white rounded-xl text-sm font-medium transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-500/20 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Simpan Jadwal Sesi
            </button>
        </div>
    </form>
</div>
