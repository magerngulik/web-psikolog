<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-400 animate-pulse"></span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-teal-300">Disaster Recovery & Data Safety</span>
            </div>
            <h1 class="font-serif text-3xl font-semibold text-white tracking-tight mt-1">Backup & Restore Database</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">
                Kelola pencadangan snapshot data klinis, unduh/unggah file SQL, dan atur jadwal backup otomatis untuk keamanan server online.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button wire:click="downloadDirectSql" 
                    type="button" 
                    class="px-4 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-teal-950/60 flex items-center gap-2 transform hover:-translate-y-0.5">
                <span>⬇️</span>
                <span>Download SQL Langsung</span>
            </button>
            <button wire:click="createServerBackup" 
                    type="button" 
                    class="px-4 py-2.5 bg-spruce-900 hover:bg-spruce-800 text-teal-300 border border-teal-500/30 hover:border-teal-400 font-semibold rounded-xl text-xs sm:text-sm transition shadow-md flex items-center gap-2">
                <span>💾</span>
                <span>Buat Backup di Server</span>
            </button>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if (session()->has('success_message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <span class="text-base">✓</span>
                <span>{{ session('success_message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error_message'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm rounded-2xl font-medium flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <span class="text-base">⚠️</span>
                <span>{{ session('error_message') }}</span>
            </div>
        </div>
    @endif

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Schedule Status -->
        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 shadow-xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 font-semibold">Jadwal Otomatis</span>
                <span class="text-lg">⏰</span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                @if ($config['frequency'] !== 'disabled')
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xl font-bold text-white uppercase tracking-tight">{{ $config['frequency'] }}</span>
                @else
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    <span class="text-xl font-bold text-slate-400">NONAKTIF</span>
                @endif
            </div>
            <p class="text-[11px] text-slate-400 mt-1">
                @if ($config['frequency'] === 'monthly')
                    Dijalankan otomatis setiap bulan sekali
                @elseif ($config['frequency'] === 'weekly')
                    Dijalankan otomatis setiap minggu
                @elseif ($config['frequency'] === 'daily')
                    Dijalankan otomatis setiap hari
                @else
                    Backup hanya berjalan manual
                @endif
            </p>
        </div>

        <!-- Card 2: Last Backup Time -->
        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 font-semibold">Backup Terakhir</span>
                <span class="text-lg">🕒</span>
            </div>
            <div class="mt-2">
                @if ($config['last_run_at'])
                    <span class="text-sm font-semibold text-teal-300 block font-mono">
                        {{ \Carbon\Carbon::parse($config['last_run_at'])->translatedFormat('d M Y, H:i') }}
                    </span>
                    <span class="text-[11px] text-slate-400">
                        {{ \Carbon\Carbon::parse($config['last_run_at'])->diffForHumans() }}
                    </span>
                @else
                    <span class="text-sm font-semibold text-slate-400 block font-mono">Belum ada</span>
                    <span class="text-[11px] text-slate-500">Klik tombol di atas untuk membuat</span>
                @endif
            </div>
        </div>

        <!-- Card 3: Storage Used -->
        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 font-semibold">Total Arsip Server</span>
                <span class="text-lg">🗄️</span>
            </div>
            <div class="mt-2">
                <span class="text-xl font-bold text-white font-mono">{{ count($backups) }}</span>
                <span class="text-xs text-slate-400">file</span>
                <span class="text-xs text-teal-400 font-mono ml-1.5">({{ $totalSizeFormatted }})</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Kuota retensi: simpan {{ $config['retention'] }} file</p>
        </div>

        <!-- Card 4: Database Driver -->
        <div class="sanctuary-glass-card rounded-2xl p-5 border border-teal-500/15 shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 font-semibold">Driver Database</span>
                <span class="text-lg">🗃️</span>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono text-xs font-bold uppercase">
                    {{ DB::connection()->getDriverName() }}
                </span>
                <span class="text-xs text-slate-300 font-medium">Koneksi Aktif</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Ekspor format standar ANSI SQL</p>
        </div>
    </div>

    <!-- Grid 2 Kolom: Upload & Restore (Kiri) + Konfigurasi Jadwal (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Upload SQL untuk Restore -->
        <div class="lg:col-span-6 sanctuary-glass-card rounded-3xl p-6 sm:p-7 border border-teal-500/15 shadow-2xl space-y-5">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">📤</span>
                <div>
                    <h2 class="font-serif text-lg font-semibold text-white">Upload File SQL (Restore Database)</h2>
                    <p class="text-xs text-slate-400">Pulihkan seluruh data klinik dari file cadangan SQL eksternal.</p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/25 text-amber-200/90 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1 text-amber-300">
                    <span>⚠️</span> <span>Perhatian Penting</span>
                </div>
                <p>
                    Proses restore akan menimpa data yang ada saat ini. Sebelum pemulihan dieksekusi, sistem secara otomatis akan membuat <strong>snapshot pengaman (safety backup)</strong> dari database saat ini.
                </p>
            </div>

            <form wire:submit.prevent="openRestoreUploaded" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1.5">
                        Pilih File Backup (.sql)
                    </label>
                    <input type="file" 
                           wire:model="sqlFile" 
                           accept=".sql"
                           class="w-full text-xs text-slate-300 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-500/20 file:text-teal-300 hover:file:bg-teal-500/30 file:cursor-pointer bg-spruce-950/80 border border-teal-500/20 rounded-xl p-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-teal-400">
                    @error('sqlFile') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if ($sqlFile)
                    <div class="p-3 bg-spruce-900/80 border border-teal-500/20 rounded-xl flex items-center justify-between text-xs font-mono text-slate-300">
                        <div class="truncate mr-2">
                            📄 <span class="text-teal-300 font-semibold">{{ $sqlFile->getClientOriginalName() }}</span>
                        </div>
                        <span class="text-slate-400 text-[11px] shrink-0">
                            {{ round($sqlFile->getSize() / 1024, 1) }} KB
                        </span>
                    </div>
                @endif

                <button type="submit" 
                        @if(!$sqlFile) disabled @endif
                        class="w-full py-2.5 rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-2 {{ $sqlFile ? 'bg-gradient-to-r from-amber-500 to-rose-500 hover:from-amber-400 hover:to-rose-400 text-slate-950 shadow-lg shadow-amber-950/50 cursor-pointer' : 'bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700' }}">
                    <span>🔄</span>
                    <span>Tinjau & Lanjutkan Pemulihan</span>
                </button>
            </form>
        </div>

        <!-- Kolom Kanan: Pengaturan Jadwal Otomatis & Server Cron -->
        <div class="lg:col-span-6 sanctuary-glass-card rounded-3xl p-6 sm:p-7 border border-teal-500/15 shadow-2xl space-y-5">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">⚙️</span>
                <div>
                    <h2 class="font-serif text-lg font-semibold text-white">Jadwal Backup Otomatis (Online)</h2>
                    <p class="text-xs text-slate-400">Atur seberapa sering sistem online membuat salinan database secara mandiri.</p>
                </div>
            </div>

            <form wire:submit.prevent="saveSchedule" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1.5">Frekuensi Backup</label>
                        <select wire:model="frequency" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                            <option value="monthly">Bulanan (Per Bulan - Rekomendasi)</option>
                            <option value="weekly">Mingguan (Setiap Minggu)</option>
                            <option value="daily">Harian (Setiap Hari)</option>
                            <option value="disabled">Nonaktifkan Otomasi</option>
                        </select>
                        @error('frequency') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1.5">Batas Retensi File</label>
                        <select wire:model="retention" class="w-full bg-spruce-900 border border-teal-500/20 rounded-xl px-4 py-2.5 text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                            <option value="6">Simpan 6 File Terakhir</option>
                            <option value="12">Simpan 12 File Terakhir (1 Tahun jika bulanan)</option>
                            <option value="24">Simpan 24 File Terakhir (2 Tahun)</option>
                            <option value="36">Simpan 36 File Terakhir</option>
                        </select>
                        @error('retention') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 font-bold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-teal-950/60 flex items-center justify-center gap-2">
                    <span>💾</span>
                    <span>Simpan Pengaturan Jadwal</span>
                </button>
            </form>

            <!-- Server Cron Command Guide Box -->
            <div class="pt-3 border-t border-teal-500/10">
                <span class="text-[11px] font-mono font-semibold text-teal-300 uppercase tracking-wider block mb-1.5">
                    💡 Panduan Server Online (cPanel / VPS Cron Job)
                </span>
                <p class="text-xs text-slate-400 mb-2">
                    Agar backup otomatis berjalan di server produksi, pastikan Cron Job berikut ditambahkan ke server hosting Anda:
                </p>
                <div class="p-3 bg-spruce-950 rounded-xl border border-teal-500/20 font-mono text-[11px] text-teal-200 select-all overflow-x-auto">
                    * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat File Backup di Server -->
    <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 border border-teal-500/15 shadow-2xl space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-serif text-xl font-semibold text-white tracking-tight flex items-center gap-2">
                    <span>🗃️</span> <span>Daftar Arsip Backup di Server</span>
                </h2>
                <p class="text-xs text-slate-400">
                    File backup tersimpan aman di direktori privat sistem (tidak dapat diakses publik langsung).
                </p>
            </div>
            <div class="text-xs font-mono text-slate-400">
                Total: <span class="text-teal-300 font-bold">{{ count($backups) }}</span> Arsip
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-teal-500/15">
            <table class="w-full text-left text-xs">
                <thead class="bg-spruce-950/90 text-slate-400 font-mono uppercase text-[10px] tracking-wider border-b border-teal-500/15">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Nama File</th>
                        <th class="py-3.5 px-4 font-semibold">Tipe</th>
                        <th class="py-3.5 px-4 font-semibold">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4 font-semibold">Ukuran</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-teal-500/10">
                    @forelse ($backups as $item)
                        <tr class="hover:bg-spruce-900/40 transition">
                            <td class="py-3 px-4 font-mono text-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">📄</span>
                                    <span class="font-medium text-white">{{ $item['filename'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if ($item['is_safety'])
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        SAFETY SNAPSHOT
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-teal-500/20 text-teal-300 border border-teal-500/30">
                                        DATABASE {{ $item['driver'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-300 font-mono">
                                <div>{{ $item['created_at']->translatedFormat('d M Y, H:i:s') }}</div>
                                <div class="text-[10px] text-slate-500">{{ $item['created_at']->diffForHumans() }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-teal-300 font-semibold">
                                {{ $item['size_formatted'] }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <!-- Unduh -->
                                    <button wire:click="downloadBackupFile('{{ $item['filename'] }}')" 
                                            title="Unduh File SQL ini"
                                            class="px-2.5 py-1.5 rounded-lg bg-spruce-900 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30 transition text-xs font-semibold flex items-center gap-1">
                                        <span>⬇️</span> <span class="hidden sm:inline">Unduh</span>
                                    </button>

                                    <!-- Restore -->
                                    <button wire:click="openRestoreExisting('{{ $item['filename'] }}')" 
                                            title="Pulihkan database dari file ini"
                                            class="px-2.5 py-1.5 rounded-lg bg-spruce-900 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 transition text-xs font-semibold flex items-center gap-1">
                                        <span>🔄</span> <span class="hidden sm:inline">Restore</span>
                                    </button>

                                    <!-- Hapus -->
                                    <button wire:click="deleteBackupFile('{{ $item['filename'] }}')" 
                                            wire:confirm="Yakin ingin menghapus file backup '{{ $item['filename'] }}' dari server?"
                                            title="Hapus file ini"
                                            class="px-2.5 py-1.5 rounded-lg bg-spruce-900 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 transition text-xs font-semibold flex items-center gap-1">
                                        <span>🗑️</span> <span class="hidden sm:inline">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 font-sans">
                                <div class="text-3xl mb-2">📁</div>
                                <div class="font-medium text-slate-300">Belum ada file backup di server.</div>
                                <div class="text-xs text-slate-500 mt-1">Klik tombol "Buat Backup di Server" di atas untuk membuat cadangan pertama.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Restore & Proteksi PIN Keamanan -->
    @if ($showRestoreModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fade-in">
            <div class="sanctuary-glass-card rounded-3xl p-6 sm:p-8 max-w-lg w-full border border-amber-500/30 shadow-2xl space-y-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-2xl">
                            ⚠️
                        </div>
                        <div>
                            <span class="text-[10px] font-mono uppercase font-bold tracking-widest text-amber-400 block">
                                Tindakan Kritis (Destruktif)
                            </span>
                            <h3 class="font-serif text-xl font-bold text-white">Konfirmasi Pemulihan Database</h3>
                        </div>
                    </div>
                    <button wire:click="closeRestoreModal" type="button" class="text-slate-400 hover:text-white text-lg">✕</button>
                </div>

                <div class="p-4 bg-spruce-950/90 rounded-2xl border border-teal-500/20 space-y-2 text-xs text-slate-300">
                    <div class="text-slate-400">Sumber Pemulihan:</div>
                    @if ($restoreSource === 'uploaded')
                        <div class="font-mono text-teal-300 font-bold flex items-center gap-2">
                            <span>📄</span> <span>File Diunggah: {{ $sqlFile ? $sqlFile->getClientOriginalName() : 'File SQL' }}</span>
                        </div>
                    @else
                        <div class="font-mono text-teal-300 font-bold flex items-center gap-2">
                            <span>🗄️</span> <span>Arsip Server: {{ $selectedExistingFile }}</span>
                        </div>
                    @endif
                    <div class="text-[11px] text-slate-400 pt-1 border-t border-teal-500/10">
                        🛡️ <strong>Jaminan Keamanan:</strong> Snapshot darurat database saat ini akan dibuat secara otomatis sesaat sebelum restore dijalankan.
                    </div>
                </div>

                <form wire:submit.prevent="executeRestore" class="space-y-4">
                    <!-- Checkbox persetujuan -->
                    <label class="flex items-start gap-2.5 text-xs text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" wire:model="confirmRestore" class="mt-0.5 rounded bg-spruce-900 border-teal-500/30 text-teal-500 focus:ring-teal-400">
                        <span>Saya mengerti bahwa seluruh data klinik saat ini akan diganti dengan isi dari file backup ini.</span>
                    </label>
                    @error('confirmRestore') <span class="text-xs text-rose-400 block">{{ $message }}</span> @enderror

                    <!-- Input PIN Keamanan 6 Digit -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-300 mb-1">
                            Masukkan PIN Keamanan 6-Digit Anda
                        </label>
                        <input type="password" 
                               maxlength="6" 
                               wire:model="restorePin" 
                               placeholder="••••••" 
                               class="w-full bg-spruce-900 border border-teal-500/30 rounded-xl px-4 py-2.5 text-white font-mono text-base tracking-widest text-center focus:outline-none focus:ring-2 focus:ring-amber-400">
                        @error('restorePin') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button wire:click="closeRestoreModal" 
                                type="button" 
                                class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-rose-600 hover:from-amber-400 hover:to-rose-500 text-slate-950 font-bold text-xs sm:text-sm transition shadow-lg shadow-rose-950/60 flex items-center gap-2">
                            <span>🔄</span>
                            <span>Konfirmasi & Pulihkan Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

