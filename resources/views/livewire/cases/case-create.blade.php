<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Buat Kasus Klinis Baru</h1>
            <p class="text-sm text-slate-400">Daftarkan problem/program intervensi psikologis untuk klien</p>
        </div>
        <a href="{{ route('cases.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pilih Klien -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pilih Klien *</label>
                <select wire:model="client_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->client_code }})</option>
                    @endforeach
                </select>
                @error('client_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Judul Kasus -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Judul Kasus / Masalah Utama *</label>
                <input wire:model="title" type="text" placeholder="misal: Kecemasan Menghadapi Karir Baru" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('title') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kategori Kasus *</label>
                <select wire:model="category" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="Anxiety">Anxiety / Kecemasan</option>
                    <option value="Depression">Depresi</option>
                    <option value="Relationship">Hubungan / Pasangan</option>
                    <option value="Family">Keluarga</option>
                    <option value="Work & Career">Karir / Pekerjaan</option>
                    <option value="Personal Growth">Personal Growth</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <!-- Keluhan Utama Klien -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">
                    Keluhan Utama Klien
                </label>
                <textarea 
                    wire:model="subjective_complaint" 
                    rows="4" 
                    placeholder="Gambarkan keluhan utama awal yang dirasakan klien saat ini, perasaan cemas, gelisah, sedih, dll..." 
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3.5 text-sm text-white focus:outline-none focus:border-teal-500 transition leading-relaxed"
                ></textarea>
                @error('subjective_complaint') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Pokok Masalah / Pemicu -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">
                    Pokok Masalah / Pemicu
                </label>
                <textarea 
                    wire:model="subjective_problem" 
                    rows="4" 
                    placeholder="Gambarkan pokok persoalan atau pemicu masalah (misal: relasi keluarga, tekanan pekerjaan, trauma masa lalu, dll)..." 
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3.5 text-sm text-white focus:outline-none focus:border-teal-500 transition leading-relaxed"
                ></textarea>
                @error('subjective_problem') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Status Kasus -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Awal Kasus</label>
                <select wire:model="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="active">Active (Aktif Berjalan)</option>
                    <option value="on_hold">On Hold (Ditunda)</option>
                    <option value="completed">Completed (Selesai)</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20">
                Simpan Kasus Medis
            </button>
        </div>
    </form>
</div>
