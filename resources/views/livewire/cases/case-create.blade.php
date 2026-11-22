<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight">Buat Kasus Klinis Baru</h1>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">Daftarkan problem atau episode penanganan psikologis untuk klien</p>
        </div>
        <a href="{{ route('cases.index') }}" class="px-4 py-2 bg-white hover:bg-blush-100 border border-blush-200 text-ink-700 font-medium rounded-xl transition text-xs sm:text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="sanctuary-glass-card border border-blush-200 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pilih Klien -->
            <div class="md:col-span-2">
                <label for="case_client_id" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Pilih Klien *</label>
                <select id="case_client_id" wire:model="client_id" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->client_code }})</option>
                    @endforeach
                </select>
                @error('client_id') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Judul Kasus -->
            <div>
                <label for="case_title" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Judul Kasus / Masalah Utama *</label>
                <input id="case_title" wire:model="title" type="text" placeholder="misal: Kecemasan Menghadapi Karir Baru" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                @error('title') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <div class="flex items-center gap-1.5 mb-1">
                    <label for="case_category" class="block text-xs font-semibold uppercase text-ink-500">Kategori Kasus *</label>
                    
                    <!-- Info Icon & Hover Popover / Tooltip -->
                    <div x-data="{ open: false }" class="relative inline-flex items-center">
                        <button 
                            type="button"
                            @mouseenter="open = true" 
                            @mouseleave="open = false" 
                            @focus="open = true" 
                            @blur="open = false"
                            @click="open = !open"
                            aria-label="Informasi Master Kategori Kasus"
                            class="text-ink-500 hover:text-blush-800 focus:outline-none focus:text-blush-700 transition-colors p-0.5 rounded-full cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Tooltip Body -->
                        <div 
                            x-show="open" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="absolute left-0 bottom-full mb-2 z-50 w-72 sm:w-80 p-3 bg-blush-50 border border-blush-200 rounded-xl shadow-2xl text-xs text-ink-700 pointer-events-auto"
                        >
                            <div class="flex items-start gap-2.5">
                                <span class="text-blush-600 text-sm mt-0.5">💡</span>
                                <div class="space-y-1">
                                    <p class="font-semibold text-ink-900">Kustomisasi Kategori Kasus</p>
                                    <p class="text-[11px] leading-relaxed text-ink-700">
                                        Pilihan kategori kasus dikelola terpusat oleh Admin. Untuk menambah atau mengubah opsi kategori klinis, silakan kelola di menu Pengaturan:
                                    </p>
                                    <a href="{{ route('settings.references') }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blush-600 hover:text-blush-800 hover:underline pt-1 border-t border-blush-200">
                                        <span>⚙️ Buka Master Referensi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            </div>
                            <!-- Arrow tip -->
                            <div class="absolute left-2.5 top-full w-2 h-2 bg-blush-50 border-r border-b border-blush-200 transform rotate-45 -translate-y-1"></div>
                        </div>
                    </div>
                </div>

                <select id="case_category" wire:model="category" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    @php
                        $categoryValues = $this->categories->pluck('value')->all();
                    @endphp
                    @if(!empty($category) && !in_array($category, $categoryValues))
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endif
                    @foreach($this->categories as $cat)
                        <option value="{{ $cat->value }}">{{ $cat->label }}</option>
                    @endforeach
                </select>
                @error('category') <span class="text-rose-700 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Keluhan Utama Klien -->
            <div class="md:col-span-2">
                <label for="case_subjective_complaint" class="block text-xs font-semibold uppercase text-ink-500 mb-1">
                    Keluhan Utama Klien
                </label>
                <textarea 
                    id="case_subjective_complaint"
                    wire:model="subjective_complaint" 
                    rows="3" 
                    placeholder="Gambarkan keluhan utama awal yang dirasakan klien saat ini, perasaan cemas, gelisah, sedih, dll..." 
                    class="w-full bg-white border border-blush-200 rounded-xl p-3.5 text-xs sm:text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition leading-relaxed"
                ></textarea>
                @error('subjective_complaint') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Pokok Masalah / Pemicu -->
            <div class="md:col-span-2">
                <label for="case_subjective_problem" class="block text-xs font-semibold uppercase text-ink-500 mb-1">
                    Pokok Masalah / Pemicu
                </label>
                <textarea 
                    id="case_subjective_problem"
                    wire:model="subjective_problem" 
                    rows="3" 
                    placeholder="Gambarkan pokok persoalan atau pemicu masalah (misal: relasi keluarga, tekanan pekerjaan, trauma masa lalu, dll)..." 
                    class="w-full bg-white border border-blush-200 rounded-xl p-3.5 text-xs sm:text-sm text-ink-900 focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition leading-relaxed"
                ></textarea>
                @error('subjective_problem') <span class="text-xs text-rose-700 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Status Kasus -->
            <div>
                <label for="case_status" class="block text-xs font-semibold uppercase text-ink-500 mb-1">Status Awal Kasus</label>
                <select id="case_status" wire:model="status" class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition">
                    <option value="active">Active (Aktif Berjalan)</option>
                    <option value="on_hold">On Hold (Ditunda)</option>
                    <option value="completed">Completed (Selesai)</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold rounded-xl transition shadow-lg shadow-blush-200/50 transform hover:-translate-y-0.5 text-xs sm:text-sm">
                Simpan Kasus Medis
            </button>
        </div>
    </form>
</div>
