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

<div class="max-w-5xl mx-auto p-6 space-y-6">
    <div class="bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <span>⚙️</span> Master Reference Manager
            </h2>
            <p class="text-sm text-slate-400 mt-1">
                Kelola data referensi master sistem: Kategori kasus, Tipe follow up, Metode pembayaran, Intervensi psikologis sesi, dan Diagnosis klinis tambahan.
            </p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 text-sm rounded-xl font-medium flex items-center justify-between">
                <span>✓ {{ session('message') }}</span>
            </div>
        @endif

        <!-- Tab Navigasi Master Data -->
        <div class="flex flex-wrap gap-2 border-b border-slate-700 pb-4">
            <button wire:click="switchGroup('case_category')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'case_category' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                📁 Kategori Kasus
            </button>
            <button wire:click="switchGroup('follow_up_type')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'follow_up_type' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                🔄 Tipe Follow Up
            </button>
            <button wire:click="switchGroup('payment_method')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'payment_method' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                💳 Metode Bayar
            </button>
            <button wire:click="switchGroup('psychological_intervention')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'psychological_intervention' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                🧩 Intervensi Psikologis
            </button>
            <button wire:click="switchGroup('clinical_diagnosis')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'clinical_diagnosis' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                🩺 Diagnosis Tambahan
            </button>
        </div>

        <!-- Form Tambah / Edit Referensi -->
        <div class="bg-slate-900/60 p-5 rounded-xl border border-slate-700/60 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    @if($editingId)
                        <span class="text-amber-400">✏️ Mode Edit Opsi Referensi</span>
                    @else
                        <span class="text-teal-400">➕ Tambah Referensi Baru</span>
                    @endif
                </h3>
                @if($editingId)
                    <button wire:click="cancelEdit" type="button" class="text-xs text-slate-400 hover:text-white transition">
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
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition"
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
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:border-teal-500 transition"
                    >
                    @error('value') <span class="text-rose-400 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Action Button -->
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="w-full {{ $editingId ? 'bg-amber-500 hover:bg-amber-600' : 'bg-teal-500 hover:bg-teal-600' }} text-slate-900 font-semibold text-sm py-2.5 px-4 rounded-xl transition shadow-lg shadow-teal-500/10">
                        {{ $editingId ? 'Perbarui Opsi' : '+ Tambah Opsi' }}
                    </button>
                    @if($editingId)
                        <button type="button" wire:click="cancelEdit" class="bg-slate-700 hover:bg-slate-600 text-white font-semibold text-sm py-2.5 px-3 rounded-xl transition">
                            Batal
                        </button>
                    @endif
                </div>
            </form>

            <!-- Helper Text Berdasarkan Grup -->
            <div class="text-xs text-slate-400 bg-slate-800/80 p-3 rounded-lg border border-slate-700/50">
                {!! $currentMeta['helper'] !!}
            </div>
        </div>

        <!-- Daftar Referensi -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400 px-1">
                <span>Daftar Referensi Aktif & Terdaftar</span>
                <span>Total: {{ $references->count() }} ({{ $references->where('is_active', true)->count() }} aktif)</span>
            </div>

            <div class="divide-y divide-slate-700/50 bg-slate-900/30 rounded-xl border border-slate-700/60 overflow-hidden">
                @forelse($references as $item)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-800/40 transition">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-teal-400">
                                {{ $item->value }}
                            </span>
                            <span class="font-medium text-white text-sm">{{ $item->label }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="toggleActive('{{ $item->id }}')" type="button" class="text-xs font-semibold px-3 py-1 rounded-full border transition {{ $item->is_active ? 'bg-teal-500/10 text-teal-400 border-teal-500/30 hover:bg-teal-500/20' : 'bg-slate-700 text-slate-400 border-slate-600 hover:bg-slate-600' }}">
                                {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                            </button>
                            <button wire:click="edit('{{ $item->id }}')" type="button" class="text-xs text-teal-400 font-semibold px-2.5 py-1 rounded-lg hover:bg-teal-500/10 transition">
                                Edit
                            </button>
                            <button wire:click="delete('{{ $item->id }}')" wire:confirm="Yakin ingin menghapus referensi '{{ addslashes($item->label) }}'?" type="button" class="text-xs text-rose-400 font-semibold px-2 py-1 rounded-lg hover:bg-rose-500/10 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500 text-sm">
                        Belum ada data referensi untuk grup ini. Silakan tambahkan melalui form di atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
