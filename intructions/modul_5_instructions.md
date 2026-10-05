# MODUL 5: PENJADWALAN SESI & CATATAN KLINIS (SESSION MANAGEMENT & ANTI-OVERLAP)

Dokumen ini berisi panduan dan seluruh kode file yang dibutuhkan untuk mengeksekusi **Modul 5** (Penjadwalan Sesi, Validasi Anti-Overlap Waktu, Input Catatan Rekam Medis / Clinical Notes, serta Penguncian Rekam Medis) pada aplikasi Web Psikolog.

---

## 🛠️ LANGKAH EKSEKUSI TERMINAL

Jalankan perintah berikut di terminal proyek `web-psikolog`:

```bash
php artisan make:livewire Sessions/SessionIndex
php artisan make:livewire Sessions/SessionCreate
php artisan make:livewire Sessions/SessionShow
```

---

## 📄 DAFTAR FILE & KODE

### 1. Livewire Component: SessionIndex (Jadwal & Riwayat Sesi)

**Lokasi:** `app/Livewire/Sessions/SessionIndex.php`

```php
<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;
use Livewire\WithPagination;

class SessionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';

    protected $queryString = ['search', 'statusFilter', 'dateFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $sessions = Session::query()
            ->with(['medicalCase.client'])
            ->when($this->search, function ($query) {
                $query->whereHas('medicalCase.client', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('client_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('medicalCase', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('session_date', $this->dateFilter);
            })
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('livewire.sessions.session-index', [
            'sessions' => $sessions
        ])->layout('components.layouts.app');
    }
}
```

---

### 2. Livewire View: SessionIndex

**Lokasi:** `resources/views/livewire/sessions/session-index.blade.php`

```html
<div class="p-6 space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Penjadwalan & Sesi Konseling</h1>
            <p class="text-sm text-slate-400">Kelola jadwal konsultasi, rekam medis sesi, dan status pembayaran</p>
        </div>
        <a href="{{ route('sessions.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Jadwalkan Sesi Baru
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
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama klien, ID klien, atau judul kasus..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <input wire:model.live="dateFilter" type="date" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <select wire:model.live="statusFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Status Sesi</option>
                <option value="scheduled">Scheduled (Terjadwal)</option>
                <option value="confirmed">Confirmed (Dikonfirmasi)</option>
                <option value="in_progress">In Progress (Berlangsung)</option>
                <option value="done">Done (Selesai)</option>
                <option value="cancelled">Cancelled (Batal)</option>
                <option value="no_show">No Show (Klien Tidak Hadir)</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Sesi & Klien</th>
                        <th class="p-4">Kasus / Topik</th>
                        <th class="p-4">Waktu Konseling</th>
                        <th class="p-4">Status Sesi</th>
                        <th class="p-4">Pembayaran</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse ($sessions as $session)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-teal-500/10 border border-teal-500/30 text-teal-400 font-bold flex items-center justify-center text-xs">
                                        #{{ $session->session_number }}
                                    </span>
                                    <div>
                                        <a href="{{ route('clients.show', $session->medicalCase->client->id) }}" class="font-semibold text-teal-400 hover:underline block">
                                            {{ $session->medicalCase->client->full_name }}
                                        </a>
                                        <span class="text-xs text-slate-500 font-mono">{{ $session->medicalCase->client->client_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('cases.show', $session->medicalCase->id) }}" class="font-semibold text-white hover:text-teal-400 block">
                                    {{ $session->medicalCase->title }}
                                </a>
                                <span class="text-xs text-slate-400">{{ $session->medicalCase->category ?? 'Umum' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-white font-medium block">{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</span>
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }} WIB
                                </span>
                            </td>
                            <td class="p-4">
                                @php
                                    $statusClasses = [
                                        'scheduled' => 'bg-slate-700 text-slate-300 border-slate-600',
                                        'confirmed' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'in_progress' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                                        'done' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                        'cancelled' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'no_show' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                    ];
                                @endphp
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border uppercase {{ $statusClasses[$session->status] ?? 'bg-slate-700 text-slate-300' }}">
                                        {{ $session->status }}
                                    </span>
                                    @if($session->is_locked)
                                        <span title="Rekam medis terkunci">🔒</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $paymentClasses = [
                                        'unpaid' => 'text-rose-400',
                                        'paid' => 'text-teal-400 font-semibold',
                                        'waived' => 'text-slate-400',
                                    ];
                                @endphp
                                <span class="text-xs capitalize {{ $paymentClasses[$session->payment_status] ?? 'text-slate-400' }}">
                                    ● {{ $session->payment_status }}
                                </span>
                                <span class="block text-xs font-mono text-slate-300">Rp {{ number_format($session->fee, 0, ',', '.') }}</span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('sessions.show', $session->id) }}" class="p-2 text-slate-400 hover:text-teal-400 inline-block transition" title="Buka Detail Sesi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Belum ada jadwal sesi konsultasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-700">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
```

---

### 3. Livewire Component: SessionCreate (Form Penjadwalan Sesi & Validasi Anti-Overlap)

**Lokasi:** `app/Livewire/Sessions/SessionCreate.php`

```php
<?php

namespace App\Livewire\Sessions;

use App\Models\MedicalCase;
use App\Models\Session;
use Livewire\Component;

class SessionCreate extends Component
{
    public $medical_case_id;
    public $session_number = 1;
    public $session_date;
    public $start_time = '09:00';
    public $end_time = '10:00';
    public $fee = 250000;
    public $status = 'scheduled';
    public $payment_status = 'unpaid';
    public $payment_method = 'cash';

    public function mount()
    {
        $this->medical_case_id = request()->query('case_id', $this->medical_case_id);
        $this->session_date = date('Y-m-d');
        
        if ($this->medical_case_id) {
            $this->calculateSessionNumber();
        }
    }

    public function updatedMedicalCaseId()
    {
        $this->calculateSessionNumber();
    }

    private function calculateSessionNumber()
    {
        $lastSession = Session::where('medical_case_id', $this->medical_case_id)
            ->max('session_number');

        $this->session_number = ($lastSession ?? 0) + 1;
    }

    protected function rules()
    {
        return [
            'medical_case_id' => 'required|exists:medical_cases,id',
            'session_number' => 'required|integer|min:1',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'fee' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,confirmed,in_progress,done,cancelled,no_show,rescheduled',
            'payment_status' => 'required|in:unpaid,paid,waived',
            'payment_method' => 'required|in:cash,transfer,qris',
        ];
    }

    public function save()
    {
        $this->validate();

        // 🛡️ VALIDASI ANTI-OVERLAP WAKTU
        $overlapCount = Session::where('session_date', $this->session_date)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) {
                $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                    ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                    ->orWhere(function ($q) {
                        $q->where('start_time', '<=', $this->start_time)
                          ->where('end_time', '>=', $this->end_time);
                    });
            })->count();

        if ($overlapCount > 0) {
            $this->addError('start_time', '⚠️ WARNING: Terjadi bentrok jadwal dengan sesi konsultasi lain pada tanggal dan jam tersebut!');
            return;
        }

        $session = Session::create([
            'medical_case_id' => $this->medical_case_id,
            'session_number' => $this->session_number,
            'session_date' => $this->session_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'fee' => $this->fee,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'is_locked' => false,
        ]);

        session()->flash('message', 'Sesi konsultasi berhasil dijadwalkan!');
        return redirect()->route('sessions.show', $session->id);
    }

    public function render()
    {
        $cases = MedicalCase::with('client')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('livewire.sessions.session-create', [
            'cases' => $cases
        ])->layout('components.layouts.app');
    }
}
```

---

### 4. Livewire View: SessionCreate

**Lokasi:** `resources/views/livewire/sessions/session-create.blade.php`

```html
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Jadwalkan Sesi Konseling</h1>
            <p class="text-sm text-slate-400">Pilih kasus medis klien & tentukan tanggal serta jam pertemuan</p>
        </div>
        <a href="{{ route('sessions.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pilih Kasus Medis -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pilih Kasus Medis Klien *</label>
                <select wire:model.live="medical_case_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="">-- Pilih Kasus --</option>
                    @foreach ($cases as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->client->full_name }} — {{ $c->title }} ({{ $c->category ?? 'Umum' }})
                        </option>
                    @endforeach
                </select>
                @error('medical_case_id') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Sesi Ke- -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Sesi Ke- *</label>
                <input wire:model="session_number" type="number" min="1" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition font-bold text-teal-400">
                @error('session_number') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Tanggal Sesi -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Sesi *</label>
                <input wire:model="session_date" type="date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('session_date') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Jam Mulai -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jam Mulai *</label>
                <input wire:model="start_time" type="time" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('start_time') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Jam Selesai -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jam Selesai *</label>
                <input wire:model="end_time" type="time" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                @error('end_time') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Biaya / Fee -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Biaya Konseling (Rp)</label>
                <input wire:model="fee" type="number" min="0" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition font-mono">
                @error('fee') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Status Pembayaran -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Pembayaran</label>
                <select wire:model="payment_status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="unpaid">Unpaid (Belum Bayar)</option>
                    <option value="paid">Paid (Sudah Bayar)</option>
                    <option value="waived">Waived (Gratis/Bebas Biaya)</option>
                </select>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Metode Pembayaran</label>
                <select wire:model="payment_method" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="cash">Tunai / Cash</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            <!-- Status Sesi -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status Sesi Initial</label>
                <select wire:model="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    <option value="scheduled">Scheduled (Terjadwal)</option>
                    <option value="confirmed">Confirmed (Dikonfirmasi Klien)</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20">
                Simpan Jadwal Sesi
            </button>
        </div>
    </form>
</div>
```

---

### 5. Livewire Component: SessionShow (Detail Clinical Notes & Session Lock)

**Lokasi:** `app/Livewire/Sessions/SessionShow.php`

```php
<?php

namespace App\Livewire\Sessions;

use App\Models\Session;
use Livewire\Component;

class SessionShow extends Component
{
    public Session $session;

    public $summary = '';
    public $dynamic_notes = '';
    public $intervention_notes = '';
    public $recommendation = '';
    public $status;
    public $payment_status;
    public $payment_method;
    public $fee;

    public function mount($id)
    {
        $this->session = Session::with(['medicalCase.client'])->findOrFail($id);

        $this->summary = $this->session->summary;
        $this->dynamic_notes = $this->session->dynamic_notes;
        $this->intervention_notes = $this->session->intervention_notes;
        $this->recommendation = $this->session->recommendation;
        $this->status = $this->session->status;
        $this->payment_status = $this->session->payment_status;
        $this->payment_method = $this->session->payment_method;
        $this->fee = $this->session->fee;
    }

    public function saveNotes()
    {
        if ($this->session->is_locked) {
            session()->flash('error', '⚠️ Rekam medis sesi ini sudah TERKUNCI dan tidak dapat diubah lagi.');
            return;
        }

        $this->session->update([
            'summary' => $this->summary,
            'dynamic_notes' => $this->dynamic_notes,
            'intervention_notes' => $this->intervention_notes,
            'recommendation' => $this->recommendation,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'fee' => $this->fee,
        ]);

        session()->flash('message', 'Catatan rekam medis sesi berhasil diperbarui!');
    }

    public function lockSession()
    {
        if ($this->session->is_locked) {
            return;
        }

        $this->session->update([
            'is_locked' => true,
            'locked_at' => now(),
            'status' => 'done',
        ]);

        $this->status = 'done';
        session()->flash('message', '🔒 Sesi konsultasi & Catatan Rekam Medis RESMI DIKUNCI!');
    }

    public function render()
    {
        return view('livewire.sessions.session-show')->layout('components.layouts.app');
    }
}
```

---

### 6. Livewire View: SessionShow

**Lokasi:** `resources/views/livewire/sessions/session-show.blade.php`

```html
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
```

---

### 7. Update Routing Web

**Lokasi:** `routes/web.php`

Tambahkan route manajemen sesi di dalam grup `pin.protected`:

```php
use App\Livewire\Sessions\SessionIndex;
use App\Livewire\Sessions\SessionCreate;
use App\Livewire\Sessions\SessionShow;

Route::middleware(['pin.protected'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');

    // Modul Klien (Modul 3)
    Route::get('/clients', App\Livewire\Clients\ClientIndex::class)->name('clients.index');
    Route::get('/clients/create', App\Livewire\Clients\ClientCreate::class)->name('clients.create');
    Route::get('/clients/{id}', App\Livewire\Clients\ClientShow::class)->name('clients.show');

    // Modul Kasus Medis (Modul 4)
    Route::get('/cases', App\Livewire\Cases\CaseIndex::class)->name('cases.index');
    Route::get('/cases/create', App\Livewire\Cases\CaseCreate::class)->name('cases.create');
    Route::get('/cases/{id}', App\Livewire\Cases\CaseShow::class)->name('cases.show');

    // Modul Sesi Konseling (Modul 5)
    Route::get('/sessions', SessionIndex::class)->name('sessions.index');
    Route::get('/sessions/create', SessionCreate::class)->name('sessions.create');
    Route::get('/sessions/{id}', SessionShow::class)->name('sessions.show');
});
```

---

## ⚡ TEST EXECUTION

1. Buka browser dan login via PIN **123456**.
2. Buka URL `http://127.0.0.1:8000/sessions`.
3. Coba **Jadwalkan Sesi Baru** untuk kasus yang sudah ada di Modul 4.
4. Tes **Anti-Overlap Validation**: Coba buat sesi lain di tanggal dan rentang jam yang sama persis, pastikan peringatan bentrok jadwal muncul.
5. Isi **Clinical Notes** (Ringkasan, Dinamika Psikologis, Intervensi, dan Homework) di halaman detail sesi.
6. Klik tombol **🔒 Kunci Rekam Medis (Lock Session)** dan pastikan seluruh input berubah menjadi *disabled/read-only*.