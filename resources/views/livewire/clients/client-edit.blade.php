<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">Edit Data Klien</h1>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/10 text-teal-300 border border-teal-500/30">
                    {{ $client->client_code }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Perbarui data identitas pribadi, demografi, dan kontak darurat</p>
        </div>
        <a href="{{ route('clients.show', $client->id) }}" class="px-4 py-2 bg-spruce-900 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-medium rounded-xl transition text-xs sm:text-sm">
            Kembali
        </a>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 rounded-2xl text-xs sm:text-sm font-medium flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="save" class="sanctuary-glass-card border border-teal-500/15 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <!-- Section Data Pribadi -->
        <div>
            <h2 class="font-serif text-lg font-semibold text-teal-300 mb-4 pb-2 border-b border-teal-500/10">1. Identitas Pribadi & Demografi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_client_full_name" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Lengkap *</label>
                    <input id="edit_client_full_name" wire:model="full_name" type="text" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                    @error('full_name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="edit_client_nickname" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Panggilan</label>
                    <input id="edit_client_nickname" wire:model="nickname" type="text" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_client_gender" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jenis Kelamin *</label>
                    <select id="edit_client_gender" wire:model="gender" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                        <option value="female">Perempuan</option>
                        <option value="male">Laki-Laki</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="edit_client_dob" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Lahir</label>
                    <input id="edit_client_dob" wire:model="date_of_birth" type="date" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_client_phone" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor WhatsApp / HP</label>
                    <input id="edit_client_phone" wire:model="phone_number" type="text" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_client_email" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email</label>
                    <input id="edit_client_email" wire:model="email" type="email" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_client_occupation" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pekerjaan</label>
                    <input id="edit_client_occupation" wire:model="occupation" type="text" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_client_nik" class="block text-xs font-semibold uppercase text-slate-400 mb-1">NIK KTP <span class="text-slate-500 lowercase">(opsional)</span></label>
                    <input id="edit_client_nik" wire:model="nik" type="text" placeholder="16 digit NIK" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                    @error('nik') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="edit_client_last_education" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pendidikan Terakhir <span class="text-slate-500 lowercase">(opsional)</span></label>
                    <select id="edit_client_last_education" wire:model="last_education" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                        <option value="">-- Pilih Jenjang Pendidikan --</option>
                        <option value="Tidak / Belum Sekolah">Tidak / Belum Sekolah</option>
                        <option value="SD / Sederajat">SD / Sederajat</option>
                        <option value="SMP / Sederajat">SMP / Sederajat</option>
                        <option value="SMA / SMK / Sederajat">SMA / SMK / Sederajat</option>
                        <option value="Diploma (D1-D4)">Diploma (D1-D4)</option>
                        <option value="Sarjana (S1)">Sarjana (S1)</option>
                        <option value="Magister (S2)">Magister (S2)</option>
                        <option value="Doktoral (S3)">Doktoral (S3)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase text-slate-400 mb-1">Urutan Kelahiran <span class="text-slate-500 lowercase">(opsional)</span></span>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-500 pointer-events-none">Anak ke-</span>
                            <input id="edit_client_birth_order" aria-label="Anak ke-" wire:model="birth_order" type="number" min="1" max="50" placeholder="1" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl pl-16 pr-3 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-500 pointer-events-none">Dari</span>
                            <input id="edit_client_total_siblings" aria-label="Dari jumlah bersaudara" wire:model="total_siblings" type="number" min="1" max="50" placeholder="3 bersaudara" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl pl-12 pr-3 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                        </div>
                    </div>
                    @error('birth_order') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    @error('total_siblings') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="edit_client_address" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Alamat Lengkap</label>
                    <textarea id="edit_client_address" wire:model="address" rows="2" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm"></textarea>
                </div>
                <div class="md:col-span-2 bg-spruce-950/60 p-4 rounded-2xl border border-teal-500/10 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span id="edit_label_difabel" class="block text-sm font-semibold text-white">Status Difabel / Disabilitas</span>
                            <span class="text-xs text-slate-400">Tandai jika klien merupakan penyandang disabilitas fisik, sensorik, intelektual, atau mental</span>
                        </div>
                        <label for="edit_client_is_disabled" class="relative inline-flex items-center cursor-pointer">
                            <input id="edit_client_is_disabled" aria-labelledby="edit_label_difabel" type="checkbox" wire:model.live="is_disabled" class="sr-only peer focus:outline-none">
                            <div class="w-11 h-6 bg-spruce-900 peer-focus:ring-2 peer-focus:ring-teal-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                            <span class="ml-3 text-xs font-semibold text-slate-300 peer-checked:text-teal-300 min-w-20">
                                {{ $is_disabled ? 'Ya (Difabel)' : 'Bukan Difabel' }}
                            </span>
                        </label>
                    </div>

                    @if($is_disabled)
                        <div class="pt-3 border-t border-teal-500/10">
                            <label for="edit_client_disability_description" class="block text-xs font-semibold uppercase text-slate-300 mb-1">
                                Keterangan / Jenis Disabilitas (Free Text)
                            </label>
                            <input 
                                id="edit_client_disability_description"
                                type="text" 
                                wire:model="disability_description" 
                                placeholder="misal: Tunarungu, Tunadaksa (kursi roda), ADHD, Spektrum Autisme, dll." 
                                class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition"
                            >
                            @error('disability_description') <span class="text-rose-400 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section Kontak Darurat -->
        <div>
            <h2 class="font-serif text-lg font-semibold text-teal-300 mb-4 pb-2 border-b border-teal-500/10">2. Kontak Darurat</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="edit_emergency_contact_name" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Kontak</label>
                    <input id="edit_emergency_contact_name" wire:model="emergency_contact_name" type="text" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_emergency_contact_phone" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor Telepon</label>
                    <input id="edit_emergency_contact_phone" wire:model="emergency_contact_phone" type="text" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
                <div>
                    <label for="edit_emergency_relation" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Hubungan / Relasi</label>
                    <input id="edit_emergency_relation" wire:model="emergency_relation" type="text" placeholder="misal: Orang Tua, Pasangan" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition text-sm">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4 gap-3">
            <a href="{{ route('clients.show', $client->id) }}" class="px-5 py-2.5 bg-spruce-900 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-medium rounded-xl transition text-xs sm:text-sm">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-950/60 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
