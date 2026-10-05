<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Tambah Klien Baru</h1>
            <p class="text-sm text-slate-400">Isi data identitas dan kontak darurat klien</p>
        </div>
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <!-- Section Data Pribadi -->
        <div>
            <h3 class="text-lg font-semibold text-teal-400 mb-4 pb-2 border-b border-slate-700">1. Identitas Pribadi</h3>
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
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Alamat Lengkap</label>
                    <textarea wire:model="address" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition"></textarea>
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
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20">
                Simpan Data Klien
            </button>
        </div>
    </form>
</div>

