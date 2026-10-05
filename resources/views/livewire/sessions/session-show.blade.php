<div class="p-6 space-y-6">
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
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Lock Session Warning Banner -->
    @if($session->is_locked)
        <div class="p-4 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-2xl flex items-center gap-3">
            <span class="text-2xl">🔒</span>
            <div>
                <h4 class="font-bold text-sm">Rekam Medis Sesi Terkunci (Locked)</h4>
                <p class="text-xs text-amber-300/80">Dikunci pada {{ \Carbon\Carbon::parse($session->locked_at)->format('d M Y, H:i') }} WIB. Catatan ini bersifat *read-only* demi mematuhi standar rekam medis.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Form Clinical Notes -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-5">
                <h3 class="text-lg font-semibold text-white border-b border-slate-700 pb-3 flex items-center justify-between">
                    <span>📝 Catatan Clinical Notes (Rekam Medis Sesi)</span>
                </h3>

                <!-- Ringkasan Sesi -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Ringkasan Sesi / Dynamic Summary</label>
                    <textarea wire:model="summary" {{ $session->is_locked ? 'disabled' : '' }} rows="3" placeholder="Gambarkan poin-poin utama pembicaraan & kondisi psikologis terkini klien..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                </div>

                <!-- Dinamika Psikologis -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Dinamika Psikologis (Psychological Dynamics)</label>
                    <textarea wire:model="dynamic_notes" {{ $session->is_locked ? 'disabled' : '' }} rows="4" placeholder="Temuan dinamika emosi, kognisi, atau pola perilaku yang teramati..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                </div>

                <!-- Intervensi / Teknik Terapi -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Intervensi & Teknik Terapi yang Digunakan</label>
                    <textarea wire:model="intervention_notes" {{ $session->is_locked ? 'disabled' : '' }} rows="3" placeholder="misal: CBT Thought Record, Progressive Muscle Relaxation, Empathic Reframing..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                </div>

                <!-- Rekomendasi & PR Klien -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Rekomendasi & Homework Klien</label>
                    <textarea wire:model="recommendation" {{ $session->is_locked ? 'disabled' : '' }} rows="3" placeholder="Tugas rumah (homework), latihan mandiri, atau rekomendasi sesi mendatang..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50"></textarea>
                </div>

                @if(!$session->is_locked)
                    <div class="flex items-center justify-between pt-2">
                        <button wire:click="saveNotes" type="button" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-teal-400 font-semibold rounded-xl text-sm transition">
                            Simpan Draft Catatan
                        </button>
                        <button wire:click="lockSession" onclick="confirm('Apakah Anda yakin ingin MENGUNCI rekam medis ini? Setelah dikunci, data tidak bisa diubah kembali.') || event.stopImmediatePropagation()" type="button" class="px-5 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-bold rounded-xl text-sm transition shadow-lg shadow-teal-500/20 flex items-center gap-2">
                            <span>🔒</span> Kunci Rekam Medis (Lock Session)
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Administrative & Transaksi -->
        <div class="space-y-6">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3">Status & Transaksi</h3>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Sesi</label>
                    <select wire:model="status" {{ $session->is_locked ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="no_show">No Show</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Biaya Konseling (Rp)</label>
                    <input wire:model="fee" type="number" min="0" {{ $session->is_locked ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white font-mono focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Pembayaran</label>
                    <select wire:model="payment_status" {{ $session->is_locked ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                        <option value="waived">Waived</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Metode Pembayaran</label>
                    <select wire:model="payment_method" {{ $session->is_locked ? 'disabled' : '' }} class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-sm text-white focus:outline-none focus:border-teal-500 transition disabled:opacity-50">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                @if(!$session->is_locked)
                    <button wire:click="saveNotes" type="button" class="w-full py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold rounded-xl text-xs transition">
                        Update Status & Transaksi
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
