<div class="space-y-6" x-data="{ openDropdown: @entangle('show_icd_dropdown') }" @click.away="openDropdown = false" @keydown.escape.window="openDropdown = false">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-teal-500/10 border border-teal-500/30 text-teal-300">
                    Sesi Ke-{{ $session->session_number }}
                </span>
                <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-white tracking-tight">{{ $session->medicalCase->title }}</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-1">
                Klien: <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="text-teal-300 font-semibold hover:text-teal-200 hover:underline">{{ $session->medicalCase->client->full_name }}</a>
                • Waktu: <span class="text-slate-200 font-mono">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="exportPdf" type="button" class="px-3.5 py-2 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-semibold rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-md shadow-rose-950/40">
                📄 <span>Cetak RPP (PDF)</span>
            </button>
            @if(!$session->is_locked && !$is_locked)
                <button wire:click="saveNotes" type="button" class="px-3.5 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition text-xs sm:text-sm flex items-center gap-1.5 shadow-md shadow-teal-950/40">
                    💾 <span>Simpan Sesi</span>
                </button>
            @endif
            <a href="{{ route('cases.show', $session->medicalCase->id) }}" class="px-3 py-2 bg-spruce-900/80 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-medium rounded-xl transition text-xs sm:text-sm">
                Lihat Kasus
            </a>
            <a href="{{ route('sessions.index') }}" class="px-3 py-2 bg-spruce-900/80 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-medium rounded-xl transition text-xs sm:text-sm">
                Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 rounded-2xl text-xs sm:text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/25 text-rose-300 rounded-2xl text-xs sm:text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Lock Session Warning Banner -->
    @if($session->is_locked || $is_locked)
        <div class="p-4 bg-gradient-to-r from-amber-950/70 via-spruce-900/85 to-amber-950/40 border border-amber-500/30 text-amber-300 rounded-2xl flex items-center gap-3.5 shadow-lg shadow-amber-950/20">
            <span class="text-2xl flex-shrink-0">🔒</span>
            <div>
                <h4 class="font-serif text-sm font-bold text-amber-200">Rekam Medis Sesi Terkunci (Locked Record)</h4>
                <p class="text-xs text-amber-200/80 mt-0.5">
                    Dikunci pada {{ \Carbon\Carbon::parse($session->locked_at ?? now())->format('d M Y, H:i') }} WIB. Catatan ini bersifat <em>read-only</em> demi mematuhi standar integritas etika dan kerahasiaan rekam medis klinis.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Clinical Notes (Alur RPP Lengkap) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-6 border border-teal-500/15">
                <!-- Header Clinical Notes -->
                <div class="border-b border-teal-500/10 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="font-serif text-xl font-semibold text-white flex items-center gap-2">
                            <span>📝</span> Catatan Clinical Notes (Rekam Medis Sesi)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Standar SOAP Klinis, Diagnosis PPDGJ-III / ICD-10 & Intervensi Psikologis</p>
                    </div>
                    @if($sessionNote?->qr_code_token)
                        <div class="flex items-center gap-1.5 text-xs text-teal-300 bg-spruce-950/80 border border-teal-500/25 px-2.5 py-1 rounded-xl">
                            <span>🛡️ QR Token:</span>
                            <span class="font-mono text-teal-400">{{ substr($sessionNote->qr_code_token, 0, 8) }}...</span>
                        </div>
                    @endif
                </div>

                <!-- Bagian 1: Subjective (Keluhan & Masalah) -->
                <div class="bg-spruce-950/50 p-4 sm:p-5 rounded-2xl border border-teal-500/10 space-y-4">
                    <h4 class="font-serif text-sm font-semibold text-teal-300 border-b border-teal-500/10 pb-2 flex items-center gap-2">
                        <span>💬</span> 1. Subjective (S) — Keluhan & Masalah Klien
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="subjective_complaint" class="block font-medium text-slate-300 text-xs mb-1.5">
                                Keluhan Utama Klien
                            </label>
                            <textarea id="subjective_complaint" wire:model="subjective_complaint" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Keluhan utama yang dirasakan klien saat ini, perasaan cemas, gelisah, sedih, dll..." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"></textarea>
                            @error('subjective_complaint') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="subjective_problem" class="block font-medium text-slate-300 text-xs mb-1.5">
                                Pokok Masalah / Pemicu
                            </label>
                            <textarea id="subjective_problem" wire:model="subjective_problem" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Pokok persoalan atau pemicu masalah (misal: relasi keluarga, tekanan pekerjaan, dll)..." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"></textarea>
                            @error('subjective_problem') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Objective (Dinamika Psikologis) -->
                <div class="bg-spruce-950/50 p-4 sm:p-5 rounded-2xl border border-teal-500/10 space-y-3">
                    <h4 class="font-serif text-sm font-semibold text-teal-300 border-b border-teal-500/10 pb-2 flex items-center gap-2">
                        <span>🔍</span> 2. Objective (O) — Dinamika Psikologis
                    </h4>
                    <div>
                        <label for="objective" class="block font-medium text-slate-300 text-xs mb-1.5">
                            Observasi Dinamika Psikologis & Perilaku
                        </label>
                        <textarea id="objective" wire:model="objective" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Hasil observasi dinamika psikologis: afek emosional, kontak mata, alur pikir, mekanisme koping..." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"></textarea>
                        @error('objective') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Bagian 3: Assessment (Metode Checkbox + Analisis Bebas) -->
                <div class="bg-spruce-950/50 p-4 sm:p-5 rounded-2xl border border-teal-500/10 space-y-4">
                    <h4 class="font-serif text-sm font-semibold text-teal-300 border-b border-teal-500/10 pb-2 flex items-center gap-2">
                        <span>📊</span> 3. Assessment (A) — Metode & Analisis Psikologis
                    </h4>
                    
                    <!-- Checkbox Metode Asesmen -->
                    <div>
                        <span class="block font-medium text-slate-300 text-xs mb-2">Metode Asesmen yang Digunakan:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-spruce-950 p-3 rounded-xl border border-teal-500/15">
                            @foreach($availableAssessments as $assessmentItem)
                                <label class="inline-flex items-center text-xs p-1.5 rounded hover:bg-spruce-900 transition cursor-pointer">
                                    <input type="checkbox" wire:model="selected_assessments" value="{{ $assessmentItem }}" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="rounded text-teal-500 focus:ring-teal-400 bg-spruce-900 border-teal-500/30 h-4 w-4">
                                    <span class="ml-2 text-slate-200 font-medium">{{ $assessmentItem }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Free Text Asesmen -->
                    <div>
                        <label for="assessment" class="block font-medium text-slate-300 text-xs mb-1.5">
                            Catatan & Analisis Asesmen (Free Text)
                        </label>
                        <textarea id="assessment" wire:model="assessment" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Tuliskan interpretasi tes psikologis, dinamika kepribadian, atau catatan asesmen khusus..." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"></textarea>
                    </div>
                </div>

                <!-- Bagian 4: Kode Diagnosis PPDGJ-III / ICD-10 & Diagnosis Bebas -->
                <div class="bg-spruce-950/50 p-4 sm:p-5 rounded-2xl border border-teal-500/10 space-y-4">
                    <h4 class="font-serif text-sm font-semibold text-teal-300 border-b border-teal-500/10 pb-2 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span>🩺</span> 4. Diagnosis PPDGJ-III / ICD-10 & Diagnosis Bebas
                        </span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-400 font-normal hidden sm:inline">Katalog resmi & diagnosis kustom</span>
                            <a href="{{ route('settings.references') }}" target="_blank" class="text-xs text-teal-400 hover:text-teal-300 hover:underline transition flex items-center gap-1 font-mono">
                                ⚙️ Master Referensi
                            </a>
                        </div>
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                        <!-- Dropdown Search -->
                        <div class="lg:col-span-2 relative">
                            <label for="icd_search" class="block font-medium text-slate-300 text-xs mb-1.5">
                                Pencarian Diagnosis PPDGJ-III / ICD-10
                            </label>
                            <div class="relative">
                                <input 
                                    id="icd_search"
                                    type="text" 
                                    wire:model.live.debounce.200ms="icd_search" 
                                    @focus="openDropdown = true"
                                    {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                                    placeholder="Ketik kode (misal: F41) atau kata kunci (misal: cemas, depresi, stres)..." 
                                    class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed pr-10"
                                >
                                <button 
                                    type="button" 
                                    wire:click="toggleIcdDropdown" 
                                    aria-label="Buka atau tutup katalog diagnosis ICD-10"
                                    {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-teal-400 hover:text-teal-300"
                                >
                                    ▼
                                </button>
                            </div>

                            <!-- Dropdown Results Menu -->
                            <div 
                                x-show="openDropdown" 
                                x-transition 
                                class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-spruce-950 border border-teal-500/30 rounded-xl shadow-2xl divide-y divide-teal-500/10"
                            >
                                @forelse($filteredDiagnoses as $diag)
                                    <button 
                                        type="button" 
                                        wire:click="selectDiagnosis('{{ $diag['code'] }}', '{{ addslashes($diag['name']) }}')"
                                        class="w-full text-left p-2.5 hover:bg-spruce-900 transition flex items-start justify-between gap-3 text-xs sm:text-sm focus:outline-none focus:bg-spruce-900"
                                    >
                                        <div>
                                            <span class="font-mono font-bold text-teal-300 mr-2 bg-teal-500/15 px-1.5 py-0.5 rounded text-xs border border-teal-500/30">
                                                {{ $diag['code'] }}
                                            </span>
                                            <span class="text-slate-200 font-medium">{{ $diag['name'] }}</span>
                                        </div>
                                        <span class="text-[11px] bg-spruce-900 text-slate-400 px-2 py-0.5 rounded border border-teal-500/10 whitespace-nowrap">
                                            {{ $diag['category'] }}
                                        </span>
                                    </button>
                                @empty
                                    <div class="p-3 text-center text-slate-400 text-xs">
                                        Tidak ditemukan di katalog standar. Anda dapat menuliskannya langsung pada kolom Kode/Deskripsi atau Form Diagnosis Bebas di bawah.
                                    </div>
                                @endforelse
                            </div>

                            <!-- Kode & Deskripsi Terpilih (Editable) -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-3">
                                <div>
                                    <label for="icd10_code" class="block text-xs font-semibold text-slate-400 mb-1">Kode Diagnosis</label>
                                    <input id="icd10_code" type="text" wire:model="icd10_code" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} placeholder="misal: F41.1" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl text-white text-xs font-mono p-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="icd10_description" class="block text-xs font-semibold text-slate-400 mb-1">Deskripsi Diagnosis</label>
                                    <input id="icd10_description" type="text" wire:model="icd10_description" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} placeholder="Deskripsi diagnosis..." class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl text-white text-xs p-2.5 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10">
                                </div>
                            </div>
                        </div>

                        <!-- Durasi Sesi -->
                        <div>
                            <label for="duration_minutes" class="block font-medium text-slate-300 text-xs mb-1.5">
                                Durasi Konseling (Menit)
                            </label>
                            <input id="duration_minutes" type="number" wire:model="duration_minutes" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10">
                        </div>
                    </div>

                    <!-- Free Text Diagnosis Kustom / Catatan Diagnostik Bebas -->
                    <div class="pt-3 border-t border-teal-500/10">
                        <label for="diagnosis_notes" class="block font-medium text-slate-300 text-xs mb-1.5">
                            Diagnosis Kustom / Catatan Diagnosis Bebas (Free Text)
                        </label>
                        <textarea 
                            id="diagnosis_notes"
                            wire:model="diagnosis_notes" 
                            {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                            rows="2" 
                            placeholder="Tuliskan diagnosis yang belum tercantum dalam katalog PPDGJ-III di atas, diagnosis banding, atau formulasi diagnostik kustom lainnya..." 
                            class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"
                        ></textarea>
                    </div>
                </div>

                <!-- Bagian 5: Intervensi Psikologis -->
                <div class="bg-spruce-950/50 p-4 sm:p-5 rounded-2xl border border-teal-500/10 space-y-4">
                    <h4 class="font-serif text-sm font-semibold text-teal-300 border-b border-teal-500/10 pb-2 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span>🧩</span> 5. Intervensi Psikologis yang Diberikan
                        </span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-400 font-normal hidden sm:inline">Pilihan intervensi & kustom</span>
                            <a href="{{ route('settings.references') }}" target="_blank" class="text-xs text-teal-400 hover:text-teal-300 hover:underline transition flex items-center gap-1 font-mono">
                                ⚙️ Master Referensi
                            </a>
                        </div>
                    </h4>
                    
                    <!-- Checkbox Intervensi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 bg-spruce-950 p-3 rounded-xl border border-teal-500/15">
                        @foreach($availableInterventions as $id => $name)
                            <label class="inline-flex items-center text-xs p-1.5 rounded hover:bg-spruce-900 transition cursor-pointer">
                                <input type="checkbox" wire:model="selected_interventions" value="{{ $id }}" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="rounded text-teal-500 focus:ring-teal-400 bg-spruce-900 border-teal-500/30 h-4 w-4">
                                <span class="ml-2 text-slate-200 font-medium">{{ is_numeric($id) ? $id . '. ' : ($id ? "({$id}) " : '') }}{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Free Text Detail Intervensi Tambahan -->
                    <div class="mt-3 pt-3 border-t border-teal-500/10">
                        <label for="intervention_notes" class="block font-medium text-slate-300 text-xs mb-1.5">
                            Catatan / Detail Intervensi Tambahan (Free Text)
                        </label>
                        <textarea 
                            id="intervention_notes"
                            wire:model="intervention_notes" 
                            {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                            rows="3" 
                            placeholder="Tuliskan catatan khusus atau rincian teknik intervensi yang diberikan selama sesi konseling..." 
                            class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"
                        ></textarea>
                    </div>
                </div>

                <!-- Bagian 6: Pesan / Tugas Rumah Klien & Follow-up -->
                <div class="bg-spruce-950/50 p-4 sm:p-5 rounded-2xl border border-teal-500/10 space-y-4">
                    <h4 class="font-serif text-sm font-semibold text-teal-300 border-b border-teal-500/10 pb-2 flex items-center gap-2">
                        <span>📝</span> 6. Pesan / Tugas Rumah & Tindak Lanjut
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Pesan/Tugas Rumah -->
                        <div class="md:col-span-2">
                            <label for="client_message" class="block font-medium text-slate-300 text-xs mb-1.5">
                                Pesan / Tugas Rumah untuk Klien (Homework / Action Plan)
                            </label>
                            <textarea 
                                id="client_message"
                                wire:model="client_message" 
                                {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                                rows="4" 
                                placeholder="Tuliskan tugas rumah untuk klien secara mendalam (misal: latihan relaksasi pernapasan 4-7-8 setiap pagi dan malam, mengisi jurnal pikiran otomatis harian)..." 
                                class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-3 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed"
                            ></textarea>
                        </div>

                        <!-- Status Tindak Lanjut -->
                        <div>
                            <label for="follow_up_status" class="block font-medium text-slate-300 text-xs mb-1.5">
                                Rencana Tindak Lanjut
                            </label>
                            <select id="follow_up_status" wire:model="follow_up_status" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed">
                                <option value="Selesai">Selesai (Terminasi Konseling)</option>
                                <option value="Jadwal Ulang">Perlu Sesi Lanjutan</option>
                                <option value="Rujukan">Dirujuk ke Spesialis Lain (Psikiater/RS)</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-2 leading-relaxed">
                                Tentukan apakah proses konseling diakhiri (*Terminasi*), dijadwalkan kembali, atau memerlukan rujukan medis lain.
                            </p>
                        </div>
                    </div>
                </div>

                @if(!$session->is_locked && !$is_locked)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-teal-500/15">
                        <button wire:click="saveNotes" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-spruce-900 hover:bg-spruce-800 text-teal-300 font-semibold rounded-xl text-xs sm:text-sm transition border border-teal-500/25">
                            💾 Simpan Draft Catatan Sesi
                        </button>
                        <button wire:click="lockSession" onclick="confirm('Apakah Anda yakin ingin MENGUNCI rekam medis ini? Setelah dikunci, data tidak bisa diubah kembali demi mematuhi standar etika rekam medis.') || event.stopImmediatePropagation()" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-teal-950/60 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                            <span>🔒</span> Kunci Rekam Medis (Lock Session)
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Administrative & Transaksi -->
        <div class="space-y-6">
            <!-- Card Status & Transaksi -->
            <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-teal-500/15">
                <h3 class="font-serif text-base font-semibold text-white border-b border-teal-500/10 pb-3">Status & Transaksi</h3>

                <div>
                    <label for="session_status" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Sesi</label>
                    <select id="session_status" wire:model="status" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed">
                        <option value="scheduled">Scheduled (Terjadwal)</option>
                        <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                        <option value="in_progress">In Progress (Berlangsung)</option>
                        <option value="done">Done (Selesai)</option>
                        <option value="cancelled">Cancelled (Dibatalkan)</option>
                        <option value="no_show">No Show (Tidak Hadir)</option>
                    </select>
                </div>

                <div>
                    <label for="session_fee" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Biaya Konseling (Rp)</label>
                    <input id="session_fee" wire:model="fee" type="number" min="0" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed">
                </div>

                <div>
                    <label for="session_payment_status" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Pembayaran</label>
                    <select id="session_payment_status" wire:model="payment_status" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed">
                        <option value="unpaid">Unpaid (Belum Bayar)</option>
                        <option value="paid">Paid (Lunas)</option>
                        <option value="waived">Waived (Bebas Biaya)</option>
                    </select>
                </div>

                <div>
                    <label for="session_payment_method" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Metode Pembayaran</label>
                    <select id="session_payment_method" wire:model="payment_method" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed">
                        <option value="cash">Cash (Tunai)</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                @if(!$session->is_locked && !$is_locked)
                    <button wire:click="saveNotes" type="button" class="w-full py-2.5 bg-spruce-900 hover:bg-spruce-800 text-teal-300 font-semibold rounded-xl text-xs transition border border-teal-500/20">
                        Update Status & Transaksi
                    </button>
                @endif
            </div>

            <!-- Card Klasifikasi Logbook & SKP IPK -->
            <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-teal-500/15">
                <div class="border-b border-teal-500/10 pb-3 flex items-center justify-between">
                    <h3 class="font-serif text-base font-semibold text-white flex items-center gap-2">
                        <span>📊</span> Klasifikasi SKP & Logbook
                    </h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-teal-500/15 text-teal-300 border border-teal-500/30 font-mono">IPK</span>
                </div>

                <div>
                    <label for="session_service_modality" class="block text-xs font-semibold uppercase text-slate-400 mb-1">Moda Pelayanan / Jenis *</label>
                    <select id="session_service_modality" wire:model="service_modality" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition disabled:bg-spruce-950/80 disabled:text-slate-300 disabled:border-teal-500/10 disabled:cursor-not-allowed">
                        <option value="individual_direct">Tatap Muka Klien Langsung (0.05 SKP)</option>
                        <option value="individual_virtual">Komunikasi Virtual / Video Call (0.05 SKP)</option>
                        <option value="group">Tatap Muka Kelompok (0.10 SKP)</option>
                        <option value="phone">Konsultasi Telepon > 15 Menit (0.02 SKP)</option>
                        <option value="chat_text">Konsultasi Tulisan / Chat > 100 Kata (0.02 SKP)</option>
                        <option value="legal_visum">Tim Visum et Repertum (0.02 SKP)</option>
                        <option value="legal_witness">Saksi Ahli di Sidang (0.05 SKP)</option>
                        <option value="legal_court_report">Laporan Bukti Pengadilan (0.01 SKP)</option>
                    </select>
                </div>

                <div class="space-y-3 pt-2 border-t border-teal-500/10">
                    <label for="is_high_risk" class="flex items-start gap-2.5 cursor-pointer">
                        <input id="is_high_risk" wire:model="is_high_risk" type="checkbox" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="mt-0.5 rounded text-amber-500 focus:ring-2 focus:ring-amber-500 bg-spruce-900 border-teal-500/30 h-4 w-4">
                        <span class="text-xs text-slate-300">
                            <strong class="text-slate-200">Tugas Tempat Berisiko Tinggi</strong>
                            <span class="block text-[11px] text-slate-400">Daerah rawan, bencana, lapas bahaya (+0.05 SKP)</span>
                        </span>
                    </label>

                    <label for="generates_report" class="flex items-start gap-2.5 cursor-pointer">
                        <input id="generates_report" wire:model="generates_report" type="checkbox" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="mt-0.5 rounded text-teal-500 focus:ring-2 focus:ring-teal-500 bg-spruce-900 border-teal-500/30 h-4 w-4">
                        <span class="text-xs text-slate-300">
                            <strong class="text-slate-200">Menyusun Laporan RPP</strong>
                            <span class="block text-[11px] text-slate-400">Pemeriksaan psikologis resmi (+0.01 SKP)</span>
                        </span>
                    </label>
                </div>

                @if(!$session->is_locked && !$is_locked)
                    <button wire:click="saveNotes" type="button" class="w-full py-2 bg-spruce-900 hover:bg-spruce-800 text-teal-300 font-semibold rounded-xl text-xs transition border border-teal-500/20">
                        Update Klasifikasi SKP
                    </button>
                @endif
            </div>

            <!-- Card Dokumen Resmi & Ekspor PDF -->
            <div class="sanctuary-glass-card rounded-2xl p-6 shadow-xl space-y-4 border border-teal-500/15">
                <h3 class="font-serif text-base font-semibold text-white border-b border-teal-500/10 pb-3 flex items-center gap-2">
                    <span>📄</span> Dokumen Resmi Sesi
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Dokumen resmi Rekam Pemeriksaan Psikologis (RPP Konseling) memuat seluruh catatan SOAP klinis, diagnosis, intervensi, serta stempel QR Code keabsahan data medis.
                </p>
                <button wire:click="exportPdf" type="button" class="w-full py-2.5 bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-rose-950/50 flex items-center justify-center gap-2">
                    <span>📄</span> Unduh Dokumen RPP (PDF)
                </button>
            </div>
        </div>
    </div>
</div>
