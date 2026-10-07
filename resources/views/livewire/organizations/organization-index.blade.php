<div class="p-6 sm:p-8 max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20 uppercase tracking-wider font-mono mb-2">
                <span>Direktori Mitra</span>
                <span>•</span>
                <span>Organisasi & Lembaga</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">Mitra & Lembaga Penyelenggara</h1>
            <p class="text-sm text-slate-400 mt-1">Daftar sekolah, kampus, komunitas, dan lembaga yang bekerja sama untuk seminar & psikoedukasi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('activities.index') }}" class="sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white px-4 py-2.5 rounded-xl font-medium text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Lihat Kegiatan & Seminar
            </a>
            <button wire:click="openCreateModal" type="button" class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition text-sm shadow-lg shadow-teal-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Mitra Baru
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 rounded-2xl text-sm font-medium flex items-center gap-3 backdrop-blur-sm shadow-lg shadow-emerald-950/20" role="alert">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="sanctuary-glass-card rounded-3xl p-5 border border-teal-500/15 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-96 relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama lembaga, kota, PIC, atau kode..." class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl pl-10 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition placeholder-slate-500 font-sans">
        </div>

        <div class="w-full md:w-auto flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
            <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Kategori:</span>
            @foreach (['all' => 'Semua', 'school' => 'Sekolah', 'university' => 'Kampus', 'community' => 'Komunitas', 'corporate' => 'Korporat', 'government' => 'Pemerintah', 'ngo' => 'LSM'] as $key => $label)
                <button wire:click="$set('categoryFilter', '{{ $key }}')" type="button" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $categoryFilter === $key ? 'bg-teal-500 text-slate-950 font-bold shadow-md shadow-teal-500/20' : 'bg-spruce-950/60 text-slate-400 hover:text-white hover:bg-spruce-900 border border-teal-500/10' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Table of Organizations -->
    <div class="sanctuary-glass-card rounded-3xl border border-teal-500/15 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-spruce-950/90 border-b border-teal-500/15 text-[11px] uppercase tracking-wider text-slate-400 font-mono">
                    <tr>
                        <th class="py-4 px-6">Kode & Lembaga</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Kota / Alamat</th>
                        <th class="py-4 px-6">Kontak PIC</th>
                        <th class="py-4 px-6 text-center">Riwayat Acara</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10 font-sans">
                    @forelse ($organizations as $org)
                        <tr class="hover:bg-spruce-900/40 transition duration-150 group">
                            <td class="py-4 px-6">
                                <div class="font-mono text-xs text-teal-400 font-semibold mb-0.5">{{ $org->org_code }}</div>
                                <div class="font-bold text-white text-base group-hover:text-teal-300 transition">{{ $org->name }}</div>
                                @if($org->notes)
                                    <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5">{{ $org->notes }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold font-mono {{ match($org->category) {
                                    'school' => 'bg-blue-500/15 text-blue-300 border border-blue-500/30',
                                    'university' => 'bg-purple-500/15 text-purple-300 border border-purple-500/30',
                                    'community' => 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30',
                                    'corporate' => 'bg-amber-500/15 text-amber-300 border border-amber-500/30',
                                    'government' => 'bg-teal-500/15 text-teal-300 border border-teal-500/30',
                                    default => 'bg-slate-700/50 text-slate-300 border border-slate-600',
                                } }}">
                                    {{ $org->category_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-300">
                                <div class="font-semibold text-white">{{ $org->city ?: '-' }}</div>
                                <div class="text-slate-400 truncate max-w-xs">{{ $org->address ?: '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs">
                                @if($org->pic_name)
                                    <div class="font-semibold text-slate-200">{{ $org->pic_name }}</div>
                                    @if($org->pic_position)
                                        <div class="text-slate-400 text-[11px]">{{ $org->pic_position }}</div>
                                    @endif
                                    @if($org->pic_phone)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $org->pic_phone);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 font-mono text-[11px] mt-0.5">
                                            <span>💬 WA:</span> <span>{{ $org->pic_phone }}</span>
                                        </a>
                                    @endif
                                @else
                                    <span class="text-slate-500 italic">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('activities.index', ['organization_id' => $org->id]) }}" class="inline-flex items-center px-3 py-1 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/25 font-mono text-xs font-bold transition">
                                    {{ $org->activities_count }} Kegiatan
                                </a>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal('{{ $org->id }}')" type="button" class="p-2 rounded-xl bg-spruce-950/80 hover:bg-spruce-900 text-teal-400 hover:text-teal-300 border border-teal-500/20 transition" title="Edit Mitra">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete('{{ $org->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus data mitra ini?" type="button" class="p-2 rounded-xl bg-spruce-950/80 hover:bg-rose-950/60 text-slate-400 hover:text-rose-400 border border-teal-500/20 hover:border-rose-500/30 transition" title="Hapus Mitra">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mx-auto text-xl mb-3">
                                    🏛️
                                </div>
                                <div class="font-serif font-bold text-white text-base">Belum Ada Mitra / Organisasi</div>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Daftarkan sekolah, kampus, atau komunitas mitra pengundang seminar untuk pencatatan kegiatan yang rapi.</p>
                                <div class="mt-4">
                                    <button wire:click="openCreateModal" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/30 rounded-xl text-xs font-bold transition">
                                        + Tambah Mitra Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($organizations->hasPages())
            <div class="p-4 border-t border-teal-500/10">
                {{ $organizations->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah / Edit Mitra -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="sanctuary-glass-card rounded-3xl border border-teal-500/30 shadow-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-teal-500/10 pb-4">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-teal-400">Formulir Instansi</span>
                        <h2 class="text-xl font-serif font-bold text-white tracking-tight mt-0.5">
                            {{ $editingId ? 'Edit Data Mitra / Lembaga' : 'Tambah Mitra / Lembaga Baru' }}
                        </h2>
                    </div>
                    <button wire:click="closeModal" type="button" class="text-slate-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">
                                Nama Lembaga / Instansi / Komunitas <span class="text-rose-400">*</span>
                            </label>
                            <input wire:model="name" type="text" placeholder="misal: SMA Negeri 1 Pekanbaru" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                            @error('name') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">
                                Kategori Instansi <span class="text-rose-400">*</span>
                            </label>
                            <select wire:model="category" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                                <option value="school">Sekolah (SD/SMP/SMA)</option>
                                <option value="university">Perguruan Tinggi / Universitas</option>
                                <option value="community">Komunitas Masyarakat</option>
                                <option value="corporate">Korporat / Swasta / Perusahaan</option>
                                <option value="government">Instansi Pemerintah / Dinas</option>
                                <option value="ngo">LSM / Yayasan</option>
                                <option value="other">Umum / Lainnya</option>
                            </select>
                            @error('category') <span class="text-rose-400 text-xs mt-1 block font-mono">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">
                                Kota / Wilayah
                            </label>
                            <input wire:model="city" type="text" placeholder="misal: Pekanbaru / Selatpanjang" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">
                                Alamat Lengkap
                            </label>
                            <input wire:model="address" type="text" placeholder="misal: Jl. Sudirman No. 45" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                        </div>
                    </div>

                    <!-- Kontak PIC -->
                    <div class="pt-2 border-t border-teal-500/10">
                        <div class="text-xs font-bold uppercase tracking-wider text-teal-400 font-mono mb-3">Informasi Kontak PIC (Penanggung Jawab)</div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1">Nama PIC</label>
                                <input wire:model="pic_name" type="text" placeholder="misal: Ibu Siti Rahma" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1">Jabatan PIC</label>
                                <input wire:model="pic_position" type="text" placeholder="misal: Guru BK / Ketua HR" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1">No. WhatsApp / HP</label>
                                <input wire:model="pic_phone" type="text" placeholder="misal: 081234567890" class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition font-mono">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 font-mono">
                            Catatan Tambahan
                        </label>
                        <textarea wire:model="notes" rows="2" placeholder="Catatan kerja sama, karakteristik audiens, atau preferensi lembaga..." class="w-full bg-spruce-950/80 border border-teal-500/20 rounded-xl p-3 text-white text-sm focus:outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-teal-500/10">
                        <button wire:click="closeModal" type="button" class="px-5 py-2.5 sanctuary-glass border border-teal-500/20 hover:border-teal-500/40 text-slate-300 hover:text-white rounded-xl text-sm font-medium transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl transition shadow-lg shadow-teal-500/20 text-sm">
                            {{ $editingId ? 'Simpan Perubahan' : 'Simpan Mitra Baru' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

