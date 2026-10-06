<div class="p-6 space-y-6" x-data="{ openDropdown: @entangle('show_icd_dropdown') }" @click.away="openDropdown = false">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-teal-500/10 border border-teal-500/30 text-teal-400">
                    Sesi Ke-{{ $session->session_number }}
                </span>
                <h1 class="text-2xl font-bold text-white">{{ $session->medicalCase->title }}</h1>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Klien: <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="text-teal-400 font-semibold hover:underline">{{ $session->medicalCase->client->full_name }}</a>
                • Waktu: <span class="text-slate-200 font-mono">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="exportPdf" type="button" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-xl transition text-sm flex items-center gap-1.5 shadow">
                📄 <span>Cetak RPP (PDF)</span>
            </button>
            @if(!$session->is_locked && !$is_locked)
                <button wire:click="saveNotes" type="button" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white font-semibold rounded-xl transition text-sm flex items-center gap-1.5 shadow">
                    💾 <span>Simpan Sesi</span>
                </button>
            @endif
            <a href="{{ route('cases.show', $session->medicalCase->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
                Lihat Kasus
            </a>
            <a href="{{ route('sessions.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
                Kembali
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Lock Session Warning Banner -->
    @if($session->is_locked || $is_locked)
        <div class="p-4 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-2xl flex items-center gap-3">
            <span class="text-2xl">🔒</span>
            <div>
                <h4 class="font-bold text-sm">Rekam Medis Sesi Terkunci (Locked)</h4>
                <p class="text-xs text-amber-300/80">
                    Dikunci pada {{ \Carbon\Carbon::parse($session->locked_at ?? now())->format('d M Y, H:i') }} WIB. Catatan ini bersifat <em>read-only</em> demi mematuhi standar kerahasiaan dan integritas rekam medis klinis.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Clinical Notes (Alur RPP Lengkap) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
                <!-- Header Clinical Notes -->
                <div class="border-b border-slate-700 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <span>📝</span> Catatan Clinical Notes (Rekam Medis Sesi)
                        </h3>
                        <p class="text-xs text-slate-400">Standar SOAP Klinis, Diagnosis PPDGJ-III / ICD-10 & Intervensi Psikologis</p>
                    </div>
                    @if($sessionNote?->qr_code_token)
                        <div class="flex items-center gap-1.5 text-xs text-teal-400 bg-teal-950/60 border border-teal-800/60 px-2.5 py-1 rounded-lg">
                            <span>🛡️ QR Token:</span>
                            <span class="font-mono">{{ substr($sessionNote->qr_code_token, 0, 8) }}...</span>
                        </div>
                    @endif
                </div>

                <!-- Bagian 1: Subjective (Keluhan & Masalah) -->
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/80 space-y-4">
                    <h4 class="text-sm font-bold text-teal-300 border-b border-slate-700/60 pb-2 flex items-center gap-2">
                        <span>💬</span> 1. Subjective (S) — Keluhan & Masalah Klien
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                                Keluhan Utama Klien
                            </label>
                            <textarea wire:model="subjective_complaint" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Keluhan utama yang dirasakan klien saat ini, perasaan cemas, gelisah, sedih, dll..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                            @error('subjective_complaint') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                                Pokok Masalah / Pemicu
                            </label>
                            <textarea wire:model="subjective_problem" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Pokok persoalan atau pemicu masalah (misal: relasi keluarga, tekanan pekerjaan, dll)..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                            @error('subjective_problem') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Objective (Dinamika Psikologis) -->
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/80 space-y-3">
                    <h4 class="text-sm font-bold text-teal-300 border-b border-slate-700/60 pb-2 flex items-center gap-2">
                        <span>🔍</span> 2. Objective (O) — Dinamika Psikologis
                    </h4>
                    <div>
                        <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                            Observasi Dinamika Psikologis
                        </label>
                        <textarea wire:model="objective" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Hasil observasi dinamika psikologis: afek emosional, kontak mata, alur pikir, mekanisme koping..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                        @error('objective') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Bagian 3: Assessment (Metode Checkbox + Analisis Bebas) -->
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/80 space-y-4">
                    <h4 class="text-sm font-bold text-teal-300 border-b border-slate-700/60 pb-2 flex items-center gap-2">
                        <span>📊</span> 3. Assessment (A) — Metode & Analisis Psikologis
                    </h4>
                    
                    <!-- Checkbox Metode Asesmen -->
                    <div>
                        <label class="block font-semibold text-slate-300 text-xs mb-2">Metode Asesmen yang Digunakan:</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-slate-900 p-3 rounded-xl border border-slate-700">
                            @foreach($availableAssessments as $assessmentItem)
                                <label class="inline-flex items-center text-xs p-1.5 rounded hover:bg-slate-800 transition cursor-pointer">
                                    <input type="checkbox" wire:model="selected_assessments" value="{{ $assessmentItem }}" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="rounded text-teal-500 focus:ring-teal-500 bg-slate-800 border-slate-700 h-4 w-4">
                                    <span class="ml-2 text-slate-200 font-medium">{{ $assessmentItem }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Free Text Asesmen -->
                    <div>
                        <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                            Catatan & Analisis Asesmen (Free Text)
                        </label>
                        <textarea wire:model="assessment" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} rows="3" placeholder="Tuliskan interpretasi tes psikologis, dinamika kepribadian, atau catatan asesmen khusus..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                    </div>
                </div>

                <!-- Bagian 4: Kode Diagnosis PPDGJ-III / ICD-10 & Diagnosis Bebas -->
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/80 space-y-4">
                    <h4 class="text-sm font-bold text-teal-300 border-b border-slate-700/60 pb-2 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span>🩺</span> 4. Diagnosis PPDGJ-III / ICD-10 & Diagnosis Bebas
                        </span>
                        <span class="text-xs text-slate-400 font-normal">Katalog resmi & diagnosis kustom</span>
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                        <!-- Dropdown Search -->
                        <div class="lg:col-span-2 relative">
                            <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                                Pencarian Diagnosis PPDGJ-III / ICD-10
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.200ms="icd_search" 
                                    @focus="openDropdown = true"
                                    {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                                    placeholder="Ketik kode (misal: F41) atau kata kunci (misal: kecemasan, depresi, stres)..." 
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition disabled:opacity-50 pr-10"
                                >
                                <button 
                                    type="button" 
                                    wire:click="toggleIcdDropdown" 
                                    {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-white"
                                >
                                    ▼
                                </button>
                            </div>

                            <!-- Dropdown Results Menu -->
                            <div 
                                x-show="openDropdown" 
                                x-transition 
                                class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-slate-900 border border-slate-700 rounded-xl shadow-2xl divide-y divide-slate-800"
                            >
                                @forelse($filteredDiagnoses as $diag)
                                    <button 
                                        type="button" 
                                        wire:click="selectDiagnosis('{{ $diag['code'] }}', '{{ addslashes($diag['name']) }}')"
                                        class="w-full text-left p-2.5 hover:bg-slate-800 transition flex items-start justify-between gap-3 text-sm"
                                    >
                                        <div>
                                            <span class="font-mono font-bold text-teal-400 mr-2 bg-slate-800 px-1.5 py-0.5 rounded text-xs border border-slate-700">
                                                {{ $diag['code'] }}
                                            </span>
                                            <span class="text-slate-200 font-medium">{{ $diag['name'] }}</span>
                                        </div>
                                        <span class="text-xs bg-slate-800 text-slate-400 px-2 py-0.5 rounded border border-slate-700/60 whitespace-nowrap">
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
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Kode Diagnosis</label>
                                    <input type="text" wire:model="icd10_code" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} placeholder="misal: F41.1" class="w-full bg-slate-900 border border-slate-700 rounded-xl text-white text-xs font-mono p-2.5">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-400 mb-1">Deskripsi Diagnosis</label>
                                    <input type="text" wire:model="icd10_description" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} placeholder="Deskripsi diagnosis..." class="w-full bg-slate-900 border border-slate-700 rounded-xl text-white text-xs p-2.5">
                                </div>
                            </div>
                        </div>

                        <!-- Durasi Sesi -->
                        <div>
                            <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                                Durasi Konseling (Menit)
                            </label>
                            <input type="number" wire:model="duration_minutes" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        </div>
                    </div>

                    <!-- Free Text Diagnosis Kustom / Catatan Diagnostik Bebas -->
                    <div class="pt-3 border-t border-slate-700/60">
                        <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                            Diagnosis Kustom / Catatan Diagnosis Bebas (Free Text)
                        </label>
                        <textarea 
                            wire:model="diagnosis_notes" 
                            {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                            rows="2" 
                            placeholder="Tuliskan diagnosis yang belum tercantum dalam katalog PPDGJ-III di atas, diagnosis banding, atau formulasi diagnostik kustom lainnya..." 
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"
                        ></textarea>
                    </div>
                </div>

                <!-- Bagian 5: Intervensi Psikologis -->
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/80 space-y-4">
                    <h4 class="text-sm font-bold text-teal-300 border-b border-slate-700/60 pb-2 flex items-center gap-2">
                        <span>🧩</span> 5. Intervensi Psikologis yang Diberikan
                    </h4>
                    
                    <!-- Checkbox Intervensi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 bg-slate-900 p-3 rounded-xl border border-slate-700">
                        @foreach($availableInterventions as $id => $name)
                            <label class="inline-flex items-center text-xs p-1.5 rounded hover:bg-slate-800 transition cursor-pointer">
                                <input type="checkbox" wire:model="selected_interventions" value="{{ $id }}" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="rounded text-teal-500 focus:ring-teal-500 bg-slate-800 border-slate-700 h-4 w-4">
                                <span class="ml-2 text-slate-200 font-medium">{{ $id }}. {{ $name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <!-- Free Text Detail Intervensi Tambahan -->
                    <div class="mt-3 pt-3 border-t border-slate-700/60">
                        <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                            Catatan / Detail Intervensi Tambahan (Free Text)
                        </label>
                        <textarea 
                            wire:model="intervention_notes" 
                            {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                            rows="3" 
                            placeholder="Tuliskan catatan khusus atau rincian teknik intervensi yang diberikan selama sesi konseling..." 
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"
                        ></textarea>
                    </div>
                </div>

                <!-- Bagian 6: Pesan / Tugas Rumah Klien & Follow-up -->
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/80 space-y-4">
                    <h4 class="text-sm font-bold text-teal-300 border-b border-slate-700/60 pb-2 flex items-center gap-2">
                        <span>📝</span> 6. Pesan / Tugas Rumah & Tindak Lanjut
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Pesan/Tugas Rumah -->
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                                Pesan / Tugas Rumah untuk Klien (Homework / Action Plan)
                            </label>
                            <textarea 
                                wire:model="client_message" 
                                {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }}
                                rows="4" 
                                placeholder="Tuliskan tugas rumah untuk klien secara mendalam (misal: latihan relaksasi pernapasan 4-7-8 setiap pagi dan malam, mengisi jurnal pikiran otomatis harian)..." 
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"
                            ></textarea>
                        </div>

                        <!-- Status Tindak Lanjut -->
                        <div>
                            <label class="block font-semibold text-slate-300 text-xs mb-1.5">
                                Rencana Tindak Lanjut
                            </label>
                            <select wire:model="follow_up_status" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                                <option value="Selesai">Selesai (Terminasi Konseling)</option>
                                <option value="Jadwal Ulang">Perlu Sesi Lanjutan</option>
                                <option value="Rujukan">Dirujuk ke Spesialis Lain (Psikiater/RS)</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-2">
                                Tentukan apakah proses konseling diakhiri (*Terminasi*), dijadwalkan kembali, atau memerlukan rujukan medis lain.
                            </p>
                        </div>
                    </div>
                </div>

                @if(!$session->is_locked && !$is_locked)
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-700">
                        <button wire:click="saveNotes" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-teal-400 font-semibold rounded-xl text-sm transition">
                            💾 Simpan Draft Catatan Sesi
                        </button>
                        <button wire:click="lockSession" onclick="confirm('Apakah Anda yakin ingin MENGUNCI rekam medis ini? Setelah dikunci, data tidak bisa diubah kembali demi mematuhi standar etika rekam medis.') || event.stopImmediatePropagation()" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-bold rounded-xl text-sm transition shadow-lg shadow-teal-500/20 flex items-center justify-center gap-2">
                            <span>🔒</span> Kunci Rekam Medis (Lock Session)
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Administrative & Transaksi -->
        <div class="space-y-6">
            <!-- Card Status & Transaksi -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3">Status & Transaksi</h3>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Sesi</label>
                    <select wire:model="status" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        <option value="scheduled">Scheduled (Terjadwal)</option>
                        <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                        <option value="in_progress">In Progress (Berlangsung)</option>
                        <option value="done">Done (Selesai)</option>
                        <option value="cancelled">Cancelled (Dibatalkan)</option>
                        <option value="no_show">No Show (Tidak Hadir)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Biaya Konseling (Rp)</label>
                    <input wire:model="fee" type="number" min="0" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white font-mono focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Pembayaran</label>
                    <select wire:model="payment_status" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        <option value="unpaid">Unpaid (Belum Bayar)</option>
                        <option value="paid">Paid (Lunas)</option>
                        <option value="waived">Waived (Bebas Biaya)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Metode Pembayaran</label>
                    <select wire:model="payment_method" {{ ($session->is_locked || $is_locked) ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        <option value="cash">Cash (Tunai)</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                @if(!$session->is_locked && !$is_locked)
                    <button wire:click="saveNotes" type="button" class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-teal-400 font-semibold rounded-xl text-xs transition">
                        Update Status & Transaksi
                    </button>
                @endif
            </div>

            <!-- Card Dokumen Resmi & Ekspor PDF -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3 flex items-center gap-2">
                    <span>📄</span> Dokumen Resmi Sesi
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Dokumen resmi Rekam Pemeriksaan Psikologis (RPP Konseling) memuat seluruh catatan SOAP klinis, diagnosis, intervensi, serta stempel QR Code keabsahan data medis.
                </p>
                <button wire:click="exportPdf" type="button" class="w-full py-3 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-rose-600/20 flex items-center justify-center gap-2">
                    <span>📄</span> Unduh Dokumen RPP (PDF)
                </button>
            </div>
        </div>
    </div>
</div>
