<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white">Edit Data Klien</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                    {{ $client->client_code }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">Perbarui data identitas pribadi, demografi, dan kontak darurat</p>
        </div>
        <a href="{{ route('clients.show', $client->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <!-- Section Data Pribadi -->
        <div>
            <h3 class="text-lg font-semibold text-teal-400 mb-4 pb-2 border-b border-slate-700">1. Identitas Pribadi & Demografi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Lengkap *</label>
                    <input wire:model="full_name" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    @error('full_name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Panggilan</label>
                    <input wire:model="nickname" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jenis Kelamin *</label>
                    <select wire:model="gender" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                        <option value="female">Perempuan</option>
                        <option value="male">Laki-Laki</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Lahir</label>
                    <input wire:model="date_of_birth" type="date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor WhatsApp / HP</label>
                    <input wire:model="phone_number" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email</label>
                    <input wire:model="email" type="email" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pekerjaan</label>
                    <input wire:model="occupation" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">NIK KTP <span class="text-slate-500 lowercase">(opsional)</span></label>
                    <input wire:model="nik" type="text" placeholder="16 digit NIK" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white font-mono focus:outline-none focus:border-teal-500 transition">
                    @error('nik') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pendidikan Terakhir <span class="text-slate-500 lowercase">(opsional)</span></label>
                    <select wire:model="last_education" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
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
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Urutan Kelahiran <span class="text-slate-500 lowercase">(opsional)</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-500 pointer-events-none">Anak ke-</span>
                            <input wire:model="birth_order" type="number" min="1" max="50" placeholder="1" class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-16 pr-3 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-slate-500 pointer-events-none">Dari</span>
                            <input wire:model="total_siblings" type="number" min="1" max="50" placeholder="3 bersaudara" class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-12 pr-3 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                        </div>
                    </div>
                    @error('birth_order') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                    @error('total_siblings') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Alamat Lengkap</label>
                    <textarea wire:model="address" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition"></textarea>
                </div>
                <div class="md:col-span-2 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="block text-sm font-semibold text-white">Status Difabel / Disabilitas</span>
                            <span class="text-xs text-slate-400">Tandai jika klien merupakan penyandang disabilitas fisik, sensorik, intelektual, atau mental</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="is_disabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                            <span class="ml-3 text-xs font-semibold text-slate-300 peer-checked:text-teal-400 min-w-20">
                                {{ $is_disabled ? 'Ya (Difabel)' : 'Bukan Difabel' }}
                            </span>
                        </label>
                    </div>

                    @if($is_disabled)
                        <div class="pt-3 border-t border-slate-700/60">
                            <label class="block text-xs font-semibold uppercase text-slate-300 mb-1">
                                Keterangan / Jenis Disabilitas (Free Text)
                            </label>
                            <input 
                                type="text" 
                                wire:model="disability_description" 
                                placeholder="misal: Tunarungu, Tunadaksa (kursi roda), ADHD, Spektrum Autisme, dll." 
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition"
                            >
                            @error('disability_description') <span class="text-rose-400 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section Kontak Darurat -->
        <div>
            <h3 class="text-lg font-semibold text-teal-400 mb-4 pb-2 border-b border-slate-700">2. Kontak Darurat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Kontak</label>
                    <input wire:model="emergency_contact_name" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor Telepon</label>
                    <input wire:model="emergency_contact_phone" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Hubungan / Relasi</label>
                    <input wire:model="emergency_relation" type="text" placeholder="misal: Orang Tua, Pasangan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4 gap-3">
            <a href="{{ route('clients.show', $client->id) }}" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-xl transition text-sm">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 text-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
