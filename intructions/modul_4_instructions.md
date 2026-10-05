# MODUL 4: MANAJEMEN KASUS KLINIS (CASE MANAGEMENT)

Dokumen ini berisi panduan dan seluruh kode file yang dibutuhkan untuk mengeksekusi **Modul 4** (Manajemen Kasus Klinis & Diagnosis/Program Terapi) pada aplikasi Web Psikolog.

---

## 🛠️ LANGKAH EKSEKUSI TERMINAL

Jalankan perintah berikut di terminal proyek `web-psikolog`:

```bash
php artisan make:livewire Cases/CaseIndex
php artisan make:livewire Cases/CaseCreate
php artisan make:livewire Cases/CaseShow
```

---

## 📄 DAFTAR FILE & KODE

### 1. Livewire Component: CaseIndex (Daftar & Filtering Kasus)

**Lokasi:** `app/Livewire/Cases/CaseIndex.php`

```php
<?php

namespace App\Livewire\Cases;

use App\Models\MedicalCase;
use Livewire\Component;
use Livewire\WithPagination;

class CaseIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';

    protected $queryString = ['search', 'statusFilter', 'categoryFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $cases = MedicalCase::query()
            ->with(['client'])
            ->withCount('sessions')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('complaint', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function ($q) {
                        $q->where('full_name', 'like', '%' . $this->search . '%')
                          ->orWhere('client_code', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category', $this->categoryFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.cases.case-index', [
            'cases' => $cases
        ])->layout('components.layouts.app');
    }
}
```

---

### 2. Livewire View: CaseIndex

**Lokasi:** `resources/views/livewire/cases/case-index.blade.php`

```html
<div class="p-6 space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Kasus Klinis</h1>
            <p class="text-sm text-slate-400">Daftar rekam medis & program terapi aktif klien</p>
        </div>
        <a href="{{ route('cases.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Buat Kasus Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul kasus, keluhan, atau nama klien..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <select wire:model.live="statusFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Status</option>
                <option value="active">Active (Aktif)</option>
                <option value="on_hold">On Hold (Ditunda)</option>
                <option value="completed">Completed (Selesai)</option>
                <option value="cancelled">Cancelled (Dibatalkan)</option>
            </select>
        </div>
        <div>
            <select wire:model.live="categoryFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Kategori</option>
                <option value="Anxiety">Anxiety / Kecemasan</option>
                <option value="Depression">Depresi</option>
                <option value="Relationship">Hubungan / Pasangan</option>
                <option value="Family">Keluarga</option>
                <option value="Work & Career">Karir / Pekerjaan</option>
                <option value="Personal Growth">Personal Growth</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Klien</th>
                        <th class="p-4">Judul Kasus & Kategori</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Jumlah Sesi</th>
                        <th class="p-4">Tanggal Dibuat</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse ($cases as $case)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-4">
                                <a href="{{ route('clients.show', $case->client->id) }}" class="font-semibold text-teal-400 hover:underline block">
                                    {{ $case->client->full_name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $case->client->client_code }}</span>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('cases.show', $case->id) }}" class="font-semibold text-white hover:text-teal-400 block">
                                    {{ $case->title }}
                                </a>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium bg-slate-700 text-slate-300">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="p-4">
                                @php
                                    $statusClasses = [
                                        'active' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                        'on_hold' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'completed' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                        'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold border uppercase {{ $statusClasses[$case->status] ?? 'bg-slate-700 text-slate-300' }}">
                                    {{ $case->status }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-300">
                                <span class="font-semibold text-white">{{ $case->sessions_count }}</span> Sesi
                            </td>
                            <td class="p-4 text-slate-400 text-xs">
                                {{ $case->created_at->format('d M Y') }}
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('cases.show', $case->id) }}" class="p-2 text-slate-400 hover:text-teal-400 inline-block transition" title="Buka Detail Kasus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Belum ada data kasus medis yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-700">
            {{ $cases->links() }}
        </div>
    </div>
</div>
```

---

### 3. Livewire Component: CaseCreate (Form Buat Kasus Baru)

**Lokasi:** `app/Livewire/Cases/CaseCreate.php`

```php
<?php

namespace App\Livewire\Cases;

use App\Models\Client;
use App\Models\MedicalCase;
use Livewire\Component;

class CaseCreate extends Component
{
    public $client_id;
    public $title;
    public $category = 'Anxiety';
    public $complaint;
    public $goal;
    public $status = 'active';

    public function mount()
    {
        // Terima client_id via query string jika ada (misal dari tombol halaman profil klien)
        $this->client_id = request()->query('client_id', $this->client_id);
    }

    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'title' => 'required|string|max:255',
        'category' => 'required|string|max:100',
        'complaint' => 'nullable|string',
        'goal' => 'nullable|string',
        'status' => 'required|in:active,on_hold,completed,cancelled',
    ];

    public function save()
    {
        $validatedData = $this->validate();

        $case = MedicalCase::create($validatedData);

        session()->flash('message', 'Kasus medis baru berhasil didaftarkan!');
        return redirect()->route('cases.show', $case->id);
    }

    public function render()
    {
        $clients = Client::latest()->get(['id', 'full_name', 'client_code']);

        return view('livewire.cases.case-create', [
            'clients' => $clients
        ])->layout('components.layouts.app');
    }
}
```

---

### 4. Livewire View: CaseCreate

**Lokasi:** `resources/views/livewire/cases/case-create.blade.php`

```html
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Buat Kasus Klinis Baru</h1>
            <p class="text-sm text-slate-400">Daftarkan problem/program intervensi psikologis untuk klien</p>
        </div>
        <a href="{{ route('cases.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pilih Klien -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pilih Klien *</label>
                <select wire:model="client_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->client_code }})</option>
                    @endforeach
                </select>
                @error('client_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Judul Kasus -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Judul Kasus / Masalah Utama *</label>
                <input wire:model="title" type="text" placeholder="misal: Kecemasan Menghadapi Karir Baru" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('title') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Kategori Kasus *</label>
                <select wire:model="category" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="Anxiety">Anxiety / Kecemasan</option>
                    <option value="Depression">Depresi</option>
                    <option value="Relationship">Hubungan / Pasangan</option>
                    <option value="Family">Keluarga</option>
                    <option value="Work & Career">Karir / Pekerjaan</option>
                    <option value="Personal Growth">Personal Growth</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <!-- Keluhan Utama / Symptom Awal -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Keluhan Utama (Initial Complaint)</label>
                <textarea wire:model="complaint" rows="3" placeholder="Gambarkan keluhan awal yang disampaikan oleh klien..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition"></textarea>
            </div>

            <!-- Target Terapi / Goal -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Target Terapi (Therapy Goal)</label>
                <textarea wire:model="goal" rows="3" placeholder="Target intervensi yang ingin dicapai bersama klien..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition"></textarea>
            </div>

            <!-- Status Kasus -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Awal Kasus</label>
                <select wire:model="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="active">Active (Aktif Berjalan)</option>
                    <option value="on_hold">On Hold (Ditunda)</option>
                    <option value="completed">Completed (Selesai)</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20">
                Simpan Kasus Medis
            </button>
        </div>
    </form>
</div>
```

---

### 5. Livewire Component: CaseShow (Detail Kasus & Timeline Sesi Konseling)

**Lokasi:** `app/Livewire/Cases/CaseShow.php`

```php
<?php

namespace App\Livewire\Cases;

use App\Models\MedicalCase;
use Livewire\Component;

class CaseShow extends Component
{
    public MedicalCase $case;
    public $progressNote = '';

    public function mount($id)
    {
        $this->case = MedicalCase::with(['client', 'sessions' => function ($query) {
            $query->orderBy('session_number', 'asc');
        }])->findOrFail($id);

        $this->progressNote = $this->case->progress_note;
    }

    public function updateProgress()
    {
        $this->case->update([
            'progress_note' => $this->progressNote,
        ]);

        session()->flash('message', 'Catatan perkembangan kasus berhasil diperbarui.');
    }

    public function updateStatus($newStatus)
    {
        $this->case->update([
            'status' => $newStatus,
        ]);

        session()->flash('message', 'Status kasus berhasil diubah menjadi ' . strtoupper($newStatus));
    }

    public function render()
    {
        return view('livewire.cases.case-show')->layout('components.layouts.app');
    }
}
```

---

### 6. Livewire View: CaseShow

**Lokasi:** `resources/views/livewire/cases/case-show.blade.php`

```html
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-700 text-slate-300">
                    {{ $case->category ?? 'Umum' }}
                </span>
                <h1 class="text-2xl font-bold text-white">{{ $case->title }}</h1>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Klien: <a href="{{ route('clients.show', $case->client->id) }}" class="text-teal-400 font-semibold hover:underline">{{ $case->client->full_name }}</a>
                ({{ $case->client->client_code }})
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('cases.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
                Kembali
            </a>
            <!-- Direct Shortcut ke Modul 5: Jadwal Sesi -->
            <a href="{{ route('sessions.create', ['case_id' => $case->id]) }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition text-sm shadow-lg shadow-teal-500/20">
                + Jadwalkan Sesi Baru
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Info Kasus & Status -->
        <div class="space-y-6">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3">Ringkasan Kasus</h3>
                
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block mb-1">Status Kasus</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach (['active', 'on_hold', 'completed', 'cancelled'] as $st)
                            <button wire:click="updateStatus('{{ $st }}')" type="button" class="px-2.5 py-1 rounded-lg text-xs font-semibold capitalize transition {{ $case->status === $st ? 'bg-teal-500 text-slate-900 shadow-md' : 'bg-slate-700 text-slate-400 hover:bg-slate-600' }}">
                                {{ $st }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Keluhan Awal</span>
                    <p class="text-sm text-slate-300 mt-1 bg-slate-900/50 p-3 rounded-xl border border-slate-700/50">{{ $case->complaint ?: 'Tidak ada catatan keluhan awal.' }}</p>
                </div>

                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Target Terapi (Goal)</span>
                    <p class="text-sm text-slate-300 mt-1 bg-slate-900/50 p-3 rounded-xl border border-slate-700/50">{{ $case->goal ?: 'Belum ditetapkan target terapi spesifik.' }}</p>
                </div>
            </div>

            <!-- Form Progress Note Global -->
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                <h3 class="text-base font-semibold text-white">Evaluasi Perkembangan Global</h3>
                <textarea wire:model="progressNote" rows="5" placeholder="Catat ringkasan kemajuan/progres klien dari seluruh rangkaian terapi di sini..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-teal-500 transition"></textarea>
                <button wire:click="updateProgress" type="button" class="w-full py-2 bg-slate-700 hover:bg-slate-600 text-teal-400 font-semibold rounded-xl text-sm transition">
                    Simpan Evaluasi
                </button>
            </div>
        </div>

        <!-- Main Timeline Sesi Konseling -->
        <div class="md:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Timeline Sesi Konseling ({{ $case->sessions->count() }})</h3>
            </div>

            <div class="space-y-4">
                @forelse ($case->sessions as $session)
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 shadow-xl transition hover:border-slate-600 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-400 font-bold flex items-center justify-center text-sm">
                                    #{{ $session->session_number }}
                                </span>
                                <div>
                                    <h4 class="text-sm font-bold text-white">Sesi Ke-{{ $session->session_number }}</h4>
                                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }} • {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-slate-700 text-slate-300">
                                    {{ $session->status }}
                                </span>
                                @if($session->is_locked)
                                    <span class="text-teal-400 text-xs flex items-center gap-1 font-medium bg-teal-500/10 px-2 py-1 rounded-lg border border-teal-500/20">
                                        🔒 Locked
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Summary Catatan Klinis Singkat -->
                        @if($session->summary)
                            <div class="text-sm text-slate-300">
                                <span class="text-xs font-semibold text-slate-500 uppercase block">Ringkasan Sesi:</span>
                                {{ Str::limit($session->summary, 160) }}
                            </div>
                        @else
                            <p class="text-xs text-slate-500 italic">Belum ada catatan ringkasan klinis yang diisi untuk sesi ini.</p>
                        @endif

                        <div class="flex justify-end pt-2">
                            <a href="{{ route('sessions.show', $session->id) }}" class="text-xs text-teal-400 hover:underline font-semibold flex items-center gap-1">
                                Kelola Catatan & Biaya Sesi &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center text-slate-500">
                        Belum ada sesi konsultasi yang dijadwalkan untuk kasus ini. Klik tombol "+ Jadwalkan Sesi Baru" di kanan atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
```

---

### 7. Update Routing Web

**Lokasi:** `routes/web.php`

Tambahkan route manajemen kasus di dalam grup `pin.protected`:

```php
use App\Livewire\Cases\CaseIndex;
use App\Livewire\Cases\CaseCreate;
use App\Livewire\Cases\CaseShow;

Route::middleware(['pin.protected'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');

    // Modul Klien
    Route::get('/clients', App\Livewire\Clients\ClientIndex::class)->name('clients.index');
    Route::get('/clients/create', App\Livewire\Clients\ClientCreate::class)->name('clients.create');
    Route::get('/clients/{id}', App\Livewire\Clients\ClientShow::class)->name('clients.show');

    // Modul Kasus Medis (Modul 4)
    Route::get('/cases', CaseIndex::class)->name('cases.index');
    Route::get('/cases/create', CaseCreate::class)->name('cases.create');
    Route::get('/cases/{id}', CaseShow::class)->name('cases.show');
});
```

---

## ⚡ TEST EXECUTION

1. Buka browser dan pastikan login via PIN **123456**.
2. Buka URL `http://127.0.0.1:8000/cases`.
3. Coba **Buat Kasus Baru**, tautkan ke salah satu klien yang sudah dibuat di Modul 3.
4. Cek halaman **Case Detail** untuk melihat *Timeline Sesi* dan form evaluasi perkembangan global.