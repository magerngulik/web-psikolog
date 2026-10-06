<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Jadwalkan Sesi Konseling</h1>
            <p class="text-sm text-slate-400">Pilih kasus medis klien & tentukan tanggal serta jam pertemuan</p>
        </div>
        <a href="{{ route('sessions.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pilih Kasus Medis -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pilih Kasus Medis Klien *</label>
                <select wire:model.live="medical_case_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="">-- Pilih Kasus --</option>
                    @foreach ($cases as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->client->full_name }} — {{ $c->title }} ({{ $c->category ?? 'Umum' }})
                        </option>
                    @endforeach
                </select>
                @error('medical_case_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Sesi Ke- -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Sesi Ke- *</label>
                <input wire:model="session_number" type="number" min="1" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition font-bold text-teal-400">
                @error('session_number') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Tanggal Sesi -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Sesi *</label>
                <input wire:model="session_date" type="date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('session_date') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Jam Mulai -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jam Mulai *</label>
                <input wire:model="start_time" type="time" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('start_time') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Jam Selesai -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jam Selesai *</label>
                <input wire:model="end_time" type="time" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('end_time') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Biaya / Fee -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Biaya Konseling (Rp)</label>
                <input wire:model="fee" type="number" min="0" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition font-mono">
                @error('fee') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Status Pembayaran -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Pembayaran</label>
                <select wire:model="payment_status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="unpaid">Unpaid (Belum Bayar)</option>
                    <option value="paid">Paid (Sudah Bayar)</option>
                    <option value="waived">Waived (Gratis/Bebas Biaya)</option>
                </select>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Metode Pembayaran</label>
                <select wire:model="payment_method" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="cash">Tunai / Cash</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            <!-- Status Sesi -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Sesi Initial</label>
                <select wire:model="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="scheduled">Scheduled (Terjadwal)</option>
                    <option value="confirmed">Confirmed (Dikonfirmasi Klien)</option>
                </select>
            </div>

            <!-- Moda Pelayanan & Klasifikasi SKP IPK -->
            <div class="md:col-span-2 pt-2 border-t border-slate-700/60">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-teal-400 text-xs font-bold uppercase tracking-wider">Klasifikasi Logbook & SKP IPK</span>
                    <span class="text-xs text-slate-500">• Digunakan untuk akumulasi otomatis Logbook Tahunan</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Moda Pelayanan / Jenis Kasus *</label>
                        <select wire:model="service_modality" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                            <option value="individual_direct">Tatap Muka Klien Individual Langsung (0.05 SKP)</option>
                            <option value="individual_virtual">Komunikasi Virtual / Video Call Klien (0.05 SKP)</option>
                            <option value="group">Tatap Muka Kelompok / Komunitas (0.10 SKP)</option>
                            <option value="phone">Konsultasi Telepon Suara > 15 Menit (0.02 SKP)</option>
                            <option value="chat_text">Konsultasi Tulisan / Chat > 100 Kata (0.02 SKP)</option>
                            <option value="legal_visum">Tim Visum et Repertum Psikiatrikum (0.02 SKP)</option>
                            <option value="legal_witness">Saksi Ahli di Pengadilan (0.05 SKP)</option>
                            <option value="legal_court_report">Laporan Pemeriksaan Alat Bukti Sidang (0.01 SKP)</option>
                        </select>
                        @error('service_modality') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col justify-center space-y-3 pt-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input wire:model="is_high_risk" type="checkbox" class="w-5 h-5 rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-900">
                            <div>
                                <span class="text-sm font-medium text-slate-200">Tugas di Tempat Berisiko Tinggi</span>
                                <p class="text-xs text-slate-400">Centang jika bertugas di daerah konflik, bencana, lapas bahaya (0.05 SKP)</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input wire:model="generates_report" type="checkbox" class="w-5 h-5 rounded bg-slate-900 border-slate-700 text-teal-500 focus:ring-teal-500 focus:ring-offset-slate-900">
                            <div>
                                <span class="text-sm font-medium text-slate-200">Menyusun Laporan Pemeriksaan Psikologis (RPP)</span>
                                <p class="text-xs text-slate-400">Otomatis dihitung poin pembuatan laporan resmi (0.01 SKP)</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20">
                Simpan Jadwal Sesi
            </button>
        </div>
    </form>
</div>
