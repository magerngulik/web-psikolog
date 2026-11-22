@php
    $placeholders = [
        'case_category' => [
            'label_title' => 'Nama Kategori Kasus',
            'label_ph' => 'misal: Trauma Pasca Bencana',
            'value_title' => 'Value Sistem (Key) - Opsional',
            'value_ph' => 'misal: trauma_bencana atau kosongkan',
            'helper' => '💡 <strong>Kategori Kasus:</strong> Digunakan untuk klasifikasi kasus klinis baru saat pendaftaran klien. Jika Value Sistem dikosongkan, sistem akan otomatis mengisinya sesuai Nama Kategori.',
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
    <div class="sanctuary-glass-card rounded-3xl shadow-2xl border border-blush-200 p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blush-500"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-blush-700">Master Data Configuration</span>
            </div>
            <h1 class="font-serif text-3xl font-semibold text-ink-900 tracking-tight mt-1">Master Reference Manager</h1>
            <p class="text-xs sm:text-sm text-ink-500 font-sans mt-0.5">
                Kelola data referensi master sistem: Kategori kasus, Tipe follow up, Metode pembayaran, Intervensi psikologis sesi, dan Diagnosis klinis tambahan.
            </p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-blush-100 border border-blush-200 text-blush-700 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between">
                <span>✓ {{ session('message') }}</span>
            </div>
        @endif

        <!-- Tab Navigasi Master Data -->
        <div class="flex flex-wrap gap-2 border-b border-blush-200 pb-4">
            <button wire:click="switchGroup('case_category')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'case_category' ? 'bg-gradient-to-r from-blush-500 to-blush-600 text-white font-bold shadow-md shadow-blush-200/50' : 'bg-white text-ink-700 hover:bg-blush-100 border border-blush-200' }}">
                📁 Kategori Kasus
            </button>
            <button wire:click="switchGroup('follow_up_type')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'follow_up_type' ? 'bg-gradient-to-r from-blush-500 to-blush-600 text-white font-bold shadow-md shadow-blush-200/50' : 'bg-white text-ink-700 hover:bg-blush-100 border border-blush-200' }}">
                🔄 Tipe Follow Up
            </button>
            <button wire:click="switchGroup('payment_method')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'payment_method' ? 'bg-gradient-to-r from-blush-500 to-blush-600 text-white font-bold shadow-md shadow-blush-200/50' : 'bg-white text-ink-700 hover:bg-blush-100 border border-blush-200' }}">
                💳 Metode Bayar
            </button>
            <button wire:click="switchGroup('psychological_intervention')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'psychological_intervention' ? 'bg-gradient-to-r from-blush-500 to-blush-600 text-white font-bold shadow-md shadow-blush-200/50' : 'bg-white text-ink-700 hover:bg-blush-100 border border-blush-200' }}">
                🧩 Intervensi Psikologis
            </button>
            <button wire:click="switchGroup('clinical_diagnosis')" type="button" class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl transition {{ $group_key === 'clinical_diagnosis' ? 'bg-gradient-to-r from-blush-500 to-blush-600 text-white font-bold shadow-md shadow-blush-200/50' : 'bg-white text-ink-700 hover:bg-blush-100 border border-blush-200' }}">
                🩺 Diagnosis Tambahan
            </button>
        </div>

        <!-- Form Tambah / Edit Referensi -->
        <div class="bg-blush-50 p-5 rounded-2xl border border-blush-200 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-base font-semibold text-ink-900 flex items-center gap-2">
                    @if($editingId)
                        <span class="text-amber-800">✏️ Mode Edit Opsi Referensi</span>
                    @else
                        <span class="text-blush-700">➕ Tambah Referensi Baru</span>
                    @endif
                </h2>
                @if($editingId)
                    <button wire:click="cancelEdit" type="button" class="text-xs text-ink-500 hover:text-ink-900 transition font-mono">
                        ✕ Batal Edit
                    </button>
                @endif
            </div>

            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Label Input -->
                <div class="md:col-span-6">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">
                        {{ $currentMeta['label_title'] }}
                    </label>
                    <input 
                        type="text" 
                        wire:model="label" 
                        placeholder="{{ $currentMeta['label_ph'] }}" 
                        class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition"
                    >
                    @error('label') <span class="text-rose-700 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Value Input -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold uppercase text-ink-500 mb-1">
                        {{ $currentMeta['value_title'] }}
                    </label>
                    <input 
                        type="text" 
                        wire:model="value" 
                        placeholder="{{ $currentMeta['value_ph'] }}" 
                        class="w-full bg-white border border-blush-200 rounded-xl px-4 py-2.5 text-ink-900 text-xs sm:text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blush-400 focus:border-blush-200 transition"
                    >
                    @error('value') <span class="text-rose-700 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Action Button -->
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="w-full {{ $editingId ? 'bg-amber-500 hover:bg-amber-400 text-white font-bold' : 'bg-gradient-to-r from-blush-500 to-blush-600 hover:from-blush-600 hover:to-blush-700 text-white font-bold' }} text-xs sm:text-sm py-2.5 px-4 rounded-xl transition shadow-lg shadow-blush-200/50">
                        {{ $editingId ? 'Perbarui Opsi' : '+ Tambah Opsi' }}
                    </button>
                    @if($editingId)
                        <button type="button" wire:click="cancelEdit" class="bg-white hover:bg-blush-100 border border-blush-200 text-ink-700 font-semibold text-xs sm:text-sm py-2.5 px-3 rounded-xl transition">
                            Batal
                        </button>
                    @endif
                </div>
            </form>

            <!-- Helper Text Berdasarkan Grup -->
            <div class="text-xs text-blush-700 bg-white p-3.5 rounded-xl border border-blush-200 leading-relaxed font-sans">
                {!! $currentMeta['helper'] !!}
            </div>
        </div>

        <!-- Daftar Referensi -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs text-ink-500 px-1 font-mono">
                <span>Daftar Referensi Aktif & Terdaftar</span>
                <span class="text-blush-700">Total: {{ $references->count() }} ({{ $references->where('is_active', true)->count() }} aktif)</span>
            </div>

            <div class="divide-y divide-blush-100 bg-blush-50 rounded-2xl border border-blush-200 overflow-hidden">
                @forelse($references as $item)
                    <div class="p-3 sm:p-3.5 flex items-center justify-between hover:bg-blush-50/70 transition gap-3">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <!-- Reorder Controls (▲ / ▼) -->
                            <div class="flex items-center gap-0.5 shrink-0">
                                <button 
                                    wire:click="moveUp('{{ $item->id }}')" 
                                    type="button" 
                                    {{ $loop->first ? 'disabled' : '' }} 
                                    title="{{ $loop->first ? 'Sudah berada di urutan teratas' : 'Pindahkan ke atas' }}"
                                    class="p-1 rounded-lg border transition {{ $loop->first ? 'opacity-20 border-transparent text-slate-600 cursor-not-allowed' : 'border-blush-200 text-ink-700 hover:text-blush-800 hover:bg-blush-200/80 cursor-pointer' }}"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
                                </button>
                                <button 
                                    wire:click="moveDown('{{ $item->id }}')" 
                                    type="button" 
                                    {{ $loop->last ? 'disabled' : '' }} 
                                    title="{{ $loop->last ? 'Sudah berada di urutan terbawah' : 'Pindahkan ke bawah' }}"
                                    class="p-1 rounded-lg border transition {{ $loop->last ? 'opacity-20 border-transparent text-slate-600 cursor-not-allowed' : 'border-blush-200 text-ink-700 hover:text-blush-800 hover:bg-blush-200/80 cursor-pointer' }}"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                            </div>

                            <!-- Position Badge -->
                            <span class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded-md bg-white border border-blush-200 text-blush-600 shrink-0">
                                #{{ $loop->iteration }}
                            </span>

                            <!-- Value & Label -->
                            <span class="text-xs font-mono px-2 py-0.5 rounded-lg bg-white border border-blush-200 text-blush-700 shrink-0">
                                {{ $item->value }}
                            </span>
                            <span class="font-medium text-ink-900 text-xs sm:text-sm truncate">{{ $item->label }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                            <button wire:click="toggleActive('{{ $item->id }}')" type="button" class="text-xs font-semibold px-2.5 sm:px-3 py-1 rounded-full border transition {{ $item->is_active ? 'bg-blush-100 text-blush-700 border-blush-200 hover:bg-blush-200/80' : 'bg-white text-ink-500 border-blush-200 hover:bg-blush-100' }}">
                                {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                            </button>
                            <button wire:click="edit('{{ $item->id }}')" type="button" class="text-xs text-blush-700 font-semibold px-2 sm:px-2.5 py-1 rounded-lg hover:bg-blush-200/80 transition">
                                Edit
                            </button>
                            <button wire:click="delete('{{ $item->id }}')" wire:confirm="Yakin ingin menghapus referensi '{{ addslashes($item->label) }}'?" type="button" class="text-xs text-rose-700 font-semibold px-2 py-1 rounded-lg hover:bg-rose-500/10 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-ink-500 text-xs sm:text-sm">
                        Belum ada data referensi untuk grup ini. Silakan tambahkan melalui form di atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
