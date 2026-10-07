<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight">Buat Kasus Klinis Baru</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">Daftarkan problem atau episode penanganan psikologis untuk klien</p>
        </div>
        <a href="{{ route('cases.index') }}" class="px-4 py-2 bg-spruce-900 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-medium rounded-xl transition text-xs sm:text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="sanctuary-glass-card border border-teal-500/15 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pilih Klien -->
            <div class="md:col-span-2">
                <label for="case_client_id" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pilih Klien *</label>
                <select id="case_client_id" wire:model="client_id" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->client_code }})</option>
                    @endforeach
                </select>
                @error('client_id') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Judul Kasus -->
            <div>
                <label for="case_title" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Judul Kasus / Masalah Utama *</label>
                <input id="case_title" wire:model="title" type="text" placeholder="misal: Kecemasan Menghadapi Karir Baru" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                @error('title') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label for="case_category" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kategori Kasus *</label>
                <select id="case_category" wire:model="category" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
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
                <label for="case_subjective_complaint" class="block text-xs font-semibold uppercase text-slate-400 mb-1">
                    Keluhan Utama Klien
                </label>
                <textarea 
                    id="case_subjective_complaint"
                    wire:model="subjective_complaint" 
                    rows="3" 
                    placeholder="Gambarkan keluhan utama awal yang dirasakan klien saat ini, perasaan cemas, gelisah, sedih, dll..." 
                    class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition leading-relaxed"
                ></textarea>
                @error('subjective_complaint') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Pokok Masalah / Pemicu -->
            <div class="md:col-span-2">
                <label for="case_subjective_problem" class="block text-xs font-semibold uppercase text-slate-400 mb-1">
                    Pokok Masalah / Pemicu
                </label>
                <textarea 
                    id="case_subjective_problem"
                    wire:model="subjective_problem" 
                    rows="3" 
                    placeholder="Gambarkan pokok persoalan atau pemicu masalah (misal: relasi keluarga, tekanan pekerjaan, trauma masa lalu, dll)..." 
                    class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition leading-relaxed"
                ></textarea>
                @error('subjective_problem') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Status Kasus -->
            <div>
                <label for="case_status" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Awal Kasus</label>
                <select id="case_status" wire:model="status" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition">
                    <option value="active">Active (Aktif Berjalan)</option>
                    <option value="on_hold">On Hold (Ditunda)</option>
                    <option value="completed">Completed (Selesai)</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-950/60 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                Simpan Kasus Medis
            </button>
        </div>
    </form>
</div>
