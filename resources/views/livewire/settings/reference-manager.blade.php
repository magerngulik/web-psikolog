<div class="max-w-4xl mx-auto p-6 space-y-6">
    <div class="bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-white">Master Reference Manager</h2>
            <p class="text-sm text-slate-400">Kelola opsi dropdown kategori kasus, tipe tindak lanjut, dan metode pembayaran.</p>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 text-sm rounded-xl font-medium">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-2 border-b border-slate-700 pb-4">
            <button wire:click="$set('group_key', 'case_category')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'case_category' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">Kategori Kasus</button>
            <button wire:click="$set('group_key', 'follow_up_type')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'follow_up_type' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">Tipe Follow Up</button>
            <button wire:click="$set('group_key', 'payment_method')" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $group_key === 'payment_method' ? 'bg-teal-500 text-slate-900 shadow-lg shadow-teal-500/20' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">Metode Bayar</button>
        </div>

        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Label Tampilan</label>
                <input type="text" wire:model="label" placeholder="misal: Depresi Ringan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-teal-500 transition">
                @error('label') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Value Sistem</label>
                <input type="text" wire:model="value" placeholder="misal: depresi_ringan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm font-mono focus:outline-none focus:border-teal-500 transition">
                @error('value') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold text-sm py-2.5 px-4 rounded-xl transition shadow-lg shadow-teal-500/20">
                    {{ $editingId ? 'Update Opsi' : '+ Tambah Opsi' }}
                </button>
            </div>
        </form>

        <div class="divide-y divide-slate-700/50">
            @forelse($references as $item)
                <div class="py-3.5 flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-white text-sm">{{ $item->label }}</span>
                        <span class="text-xs text-slate-400 font-mono ml-2">({{ $item->value }})</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="toggleActive('{{ $item->id }}')" type="button" class="text-xs font-semibold px-3 py-1 rounded-full border transition {{ $item->is_active ? 'bg-teal-500/10 text-teal-400 border-teal-500/30' : 'bg-slate-700 text-slate-400 border-slate-600' }}">
                            {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                        </button>
                        <button wire:click="edit('{{ $item->id }}')" type="button" class="text-xs text-teal-400 font-semibold hover:underline">Edit</button>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-slate-500 text-sm">
                    Belum ada data referensi untuk grup ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
