<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight">Tambah Klien Baru</h1>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">Isi data identitas, demografi, dan kontak darurat pasien</p>
        </div>
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white hover:bg-blush-100 border border-blush-200 text-ink-700 font-medium rounded-xl transition text-xs sm:text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="sanctuary-glass-card border border-blush-200 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <!-- Section Data Pribadi -->
        <div>
            <h2 class="font-serif text-lg font-semibold text-blush-700 mb-4 pb-2 border-b border-blush-200">1. Identitas Pribadi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="client_full_name" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nama Lengkap *</label>
                    <input id="client_full_name" wire:model="full_name" type="text" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                    @error('full_name') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="client_nickname" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nama Panggilan</label>
                    <input id="client_nickname" wire:model="nickname" type="text" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
                <div>
                    <label for="client_gender" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Jenis Kelamin *</label>
                    <select id="client_gender" wire:model="gender" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                        <option value="female">Perempuan</option>
                        <option value="male">Laki-Laki</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="client_dob" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Tanggal Lahir</label>
                    <x-date-picker id="client_dob" wire:model="date_of_birth" max-date="today" placeholder="Pilih tanggal lahir..." />
                </div>
                <div>
                    <label for="client_phone" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nomor WhatsApp / HP</label>
                    <input id="client_phone" wire:model="phone_number" type="text" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
                <div>
                    <label for="client_email" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Email</label>
                    <input id="client_email" wire:model="email" type="email" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
                <div>
                    <label for="client_occupation" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Pekerjaan</label>
                    <input id="client_occupation" wire:model="occupation" type="text" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
                <div>
                    <label for="client_nik" class="block text-xs font-semibold uppercase text-ink-500 mb-1">NIK KTP <span class="text-ink-500 lowercase">(opsional)</span></label>
                    <input id="client_nik" wire:model="nik" type="text" placeholder="16 digit NIK" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 font-mono focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                    @error('nik') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="client_last_education" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Pendidikan Terakhir <span class="text-ink-500 lowercase">(opsional)</span></label>
                    <select id="client_last_education" wire:model="last_education" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
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
                    <span class="block text-xs font-semibold uppercase text-ink-500 mb-1">Urutan Kelahiran <span class="text-ink-500 lowercase">(opsional)</span></span>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-ink-500 pointer-events-none">Anak ke-</span>
                            <input id="client_birth_order" aria-label="Anak ke-" wire:model="birth_order" type="number" min="1" max="50" placeholder="1" class="w-full bg-white border border-blush-200 rounded-xl pl-16 pr-3 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-ink-500 pointer-events-none">Dari</span>
                            <input id="client_total_siblings" aria-label="Dari jumlah bersaudara" wire:model="total_siblings" type="number" min="1" max="50" placeholder="3 bersaudara" class="w-full bg-white border border-blush-200 rounded-xl pl-12 pr-3 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                        </div>
                    </div>
                    @error('birth_order') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                    @error('total_siblings') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="client_address" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Alamat Lengkap</label>
                    <textarea id="client_address" wire:model="address" rows="2" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm"></textarea>
                </div>
                <div class="md:col-span-2 bg-blush-50 p-4 rounded-2xl border border-blush-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span id="label-difabel" class="block text-sm font-semibold text-ink-900">Status Difabel / Disabilitas</span>
                            <span class="text-xs text-ink-500">Tandai jika klien merupakan penyandang disabilitas fisik, sensorik, intelektual, atau mental</span>
                        </div>
                        <label for="client_is_disabled" class="relative inline-flex items-center cursor-pointer">
                            <input id="client_is_disabled" aria-labelledby="label-difabel" type="checkbox" wire:model.live="is_disabled" class="sr-only peer focus:outline-none">
                            <div class="w-11 h-6 bg-white peer-focus:ring-2 peer-focus:ring-blush-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blush-500"></div>
                            <span class="ml-3 text-xs font-semibold text-ink-700 peer-checked:text-blush-700 min-w-20">
                                {{ $is_disabled ? 'Ya (Difabel)' : 'Bukan Difabel' }}
                            </span>
                        </label>
                    </div>

                    @if($is_disabled)
                        <div class="pt-3 border-t border-blush-200">
                            <label for="client_disability_description" class="block text-xs font-semibold uppercase text-ink-700 mb-1">
                                Keterangan / Jenis Disabilitas (Free Text)
                            </label>
                            <input 
                                id="client_disability_description"
                                type="text" 
                                wire:model="disability_description" 
                                placeholder="misal: Tunarungu, Tunadaksa (kursi roda), ADHD, Spektrum Autisme, dll." 
                                class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition"
                            >
                            @error('disability_description') <span class="text-rose-700 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section Kontak Darurat -->
        <div>
            <h2 class="font-serif text-lg font-semibold text-blush-700 mb-4 pb-2 border-b border-blush-200">2. Kontak Darurat</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="emergency_contact_name" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nama Kontak</label>
                    <input id="emergency_contact_name" wire:model="emergency_contact_name" type="text" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
                <div>
                    <label for="emergency_contact_phone" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Nomor Telepon</label>
                    <input id="emergency_contact_phone" wire:model="emergency_contact_phone" type="text" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
                <div>
                    <label for="emergency_relation" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Hubungan / Relasi</label>
                    <input id="emergency_relation" wire:model="emergency_relation" type="text" placeholder="misal: Orang Tua, Pasangan" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition text-sm">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 disabled:opacity-50 text-white font-bold rounded-xl transition shadow-lg shadow-blush-200/50 transform hover:-translate-y-0.5 text-sm">
                <span wire:loading.remove wire:target="save">Simpan Data Klien</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
