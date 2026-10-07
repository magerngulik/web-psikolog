@php
    $placeholders = [
        'case_category' => [
            'label_title' => 'Nama Kategori Kasus',
            'label_ph' => 'misal: Trauma Pasca Bencana',
            'value_title' => 'Value Sistem (Key)',
            'value_ph' => 'misal: trauma_bencana',
            'helper' => '💡 <strong>Kategori Kasus:</strong> Digunakan untuk klasifikasi kasus klinis baru saat pendaftaran klien.',
        ],
        'follow_up_type' => [
            'label_title' => 'Label Status Follow Up',
            'label_ph' => 'misal: Rujuk ke Psikiater',
            'value_title' => 'Value Sistem (Key)',
            'value_ph' => 'misal: rujuk_psikiater',
            'helper' => '💡 <strong>Tipe Follow Up:</strong> Digunakan untuk menentukan rekomendasi tindak lanjut penanganan klien setelah sesi selesai.',
        ],
        'payment_method' => [
            'label_title' => 'Nama Metode Pembayaran',
            'label_ph' => 'misal: QRIS / Bank Transfer',
            'value_title' => 'Value Sistem (Key)',
            'value_ph' => 'misal: qris',
            'helper' => '💡 <strong>Metode Pembayaran:</strong> Pilihan saluran pembayaran administrasi pada sesi konsultasi klien.',
        ],
        'psychological_intervention' => [
            'label_title' => 'Nama Intervensi Psikologis',
            'label_ph' => 'misal: Mindfulness-Based Stress Reduction (MBSR)',
            'value_title' => 'Nomor Urut / Kode (Opsional)',
            'value_ph' => 'misal: 17 atau kosongkan untuk otomatis',
            'helper' => '💡 <strong>Intervensi Psikologis:</strong> Opsi ini akan otomatis muncul sebagai checklist pada form rekam medis sesi konsultasi (Point 5) dan lembar cetak RPP PDF. Jika nomor urut dikosongkan, sistem akan mengisinya secara berurutan.',
        ],
        'clinical_diagnosis' => [
            'label_title' => 'Nama Diagnosis / Gejala Klinis',
            'label_ph' => 'misal: Burnout Kronis / Gangguan Kelelahan Mental',
            'value_title' => 'Kode ICD-10 / PPDGJ / Kustom',
            'value_ph' => 'misal: Z73.0 atau F99.1',
            'helper' => '💡 <strong>Diagnosis Tambahan:</strong> Diagnosis klinis kustom ini akan langsung terintegrasi dengan kotak pencarian diagnosis PPDGJ-III / ICD-10 (Point 4) di halaman sesi.',
        ],
    ];
    $currentMeta = $placeholders[$group_key] ?? $placeholders['case_category'];
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="sanctuary-glass-card rounded-3xl shadow-2xl border border-teal-500/15 p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-teal-300">Master Data Configuration</span>
            </div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight mt-1">Master Reference Manager</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">
                Kelola data referensi master sistem: Kategori kasus, Tipe follow up, Metode pembayaran, Intervensi psikologis sesi, dan Diagnosis klinis tambahan.
            </p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-teal-500/10 border border-teal-500/25 text-teal-300 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between">
                <span>✓ {{ session('message') }}</span>
            </div>
        @endif

        <!-- Tab Navigasi Master Data -->
        <div class="flex flex-wrap gap-2 border-b border-teal-500/10 pb-4">
            <button wire:click="switchGroup('case_category')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'case_category' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 font-bold shadow-md shadow-teal-950/40' : 'bg-spruce-900 text-slate-300 hover:bg-spruce-800 border border-teal-500/15' }}">
                📁 Kategori Kasus
            </button>
            <button wire:click="switchGroup('follow_up_type')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'follow_up_type' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 font-bold shadow-md shadow-teal-950/40' : 'bg-spruce-900 text-slate-300 hover:bg-spruce-800 border border-teal-500/15' }}">
                🔄 Tipe Follow Up
            </button>
            <button wire:click="switchGroup('payment_method')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'payment_method' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 font-bold shadow-md shadow-teal-950/40' : 'bg-spruce-900 text-slate-300 hover:bg-spruce-800 border border-teal-500/15' }}">
                💳 Metode Bayar
            </button>
            <button wire:click="switchGroup('psychological_intervention')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'psychological_intervention' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 font-bold shadow-md shadow-teal-950/40' : 'bg-spruce-900 text-slate-300 hover:bg-spruce-800 border border-teal-500/15' }}">
                🧩 Intervensi Psikologis
            </button>
            <button wire:click="switchGroup('clinical_diagnosis')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'clinical_diagnosis' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-950 font-bold shadow-md shadow-teal-950/40' : 'bg-spruce-900 text-slate-300 hover:bg-spruce-800 border border-teal-500/15' }}">
                🩺 Diagnosis Tambahan
            </button>
        </div>

        <!-- Form Tambah / Edit Referensi -->
        <div class="bg-spruce-950/60 p-5 rounded-2xl border border-teal-500/15 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-base font-semibold text-white flex items-center gap-2">
                    @if($editingId)
                        <span class="text-amber-300">✏️ Mode Edit Opsi Referensi</span>
                    @else
                        <span class="text-teal-300">➕ Tambah Referensi Baru</span>
                    @endif
                </h2>
                @if($editingId)
                    <button wire:click="cancelEdit" type="button" class="text-xs text-slate-400 hover:text-white transition font-mono">
                        ✕ Batal Edit
                    </button>
                @endif
            </div>

            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Label Input -->
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">
                        {{ $currentMeta['label_title'] }}
                    </label>
                    <input 
                        type="text" 
                        wire:model="label" 
                        placeholder="{{ $currentMeta['label_ph'] }}" 
                        class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition"
                    >
                    @error('label') <span class="text-rose-400 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Value Input -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">
                        {{ $currentMeta['value_title'] }}
                    </label>
                    <input 
                        type="text" 
                        wire:model="value" 
                        placeholder="{{ $currentMeta['value_ph'] }}" 
                        class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition"
                    >
                    @error('value') <span class="text-rose-400 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Action Button -->
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="w-full {{ $editingId ? 'bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold' : 'bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold' }} text-xs sm:text-sm py-2.5 px-4 rounded-xl transition shadow-lg shadow-teal-950/40">
                        {{ $editingId ? 'Perbarui Opsi' : '+ Tambah Opsi' }}
                    </button>
                    @if($editingId)
                        <button type="button" wire:click="cancelEdit" class="bg-spruce-900 hover:bg-spruce-800 border border-teal-500/20 text-slate-300 font-semibold text-xs sm:text-sm py-2.5 px-3 rounded-xl transition">
                            Batal
                        </button>
                    @endif
                </div>
            </form>

            <!-- Helper Text Berdasarkan Grup -->
            <div class="text-xs text-teal-200/80 bg-spruce-900/60 p-3.5 rounded-xl border border-teal-500/10 leading-relaxed font-sans">
                {!! $currentMeta['helper'] !!}
            </div>
        </div>

        <!-- Daftar Referensi -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 px-1 font-mono">
                <span>Daftar Referensi Aktif & Terdaftar</span>
                <span class="text-teal-300">Total: {{ $references->count() }} ({{ $references->where('is_active', true)->count() }} aktif)</span>
            </div>

            <div class="divide-y divide-teal-500/10 bg-spruce-950/40 rounded-2xl border border-teal-500/15 overflow-hidden">
                @forelse($references as $item)
                    <div class="p-3.5 flex items-center justify-between hover:bg-teal-500/[0.04] transition">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono px-2 py-0.5 rounded-lg bg-spruce-900 border border-teal-500/20 text-teal-300">
                                {{ $item->value }}
                            </span>
                            <span class="font-medium text-white text-xs sm:text-sm">{{ $item->label }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="toggleActive('{{ $item->id }}')" type="button" class="text-xs font-semibold px-3 py-1 rounded-full border transition {{ $item->is_active ? 'bg-teal-500/15 text-teal-300 border-teal-500/30 hover:bg-teal-500/25' : 'bg-spruce-900 text-slate-400 border-teal-500/10 hover:bg-spruce-800' }}">
                                {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                            </button>
                            <button wire:click="edit('{{ $item->id }}')" type="button" class="text-xs text-teal-300 font-semibold px-2.5 py-1 rounded-lg hover:bg-teal-500/10 transition">
                                Edit
                            </button>
                            <button wire:click="delete('{{ $item->id }}')" wire:confirm="Yakin ingin menghapus referensi '{{ addslashes($item->label) }}'?" type="button" class="text-xs text-rose-400 font-semibold px-2 py-1 rounded-lg hover:bg-rose-500/10 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs sm:text-sm">
                        Belum ada data referensi untuk grup ini. Silakan tambahkan melalui form di atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
