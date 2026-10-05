# MODUL 3: MANAJEMEN KLIEN (CLIENT MANAGEMENT)

Dokumen ini berisi panduan dan seluruh kode file yang dibutuhkan untuk mengeksekusi **Modul 3** (Manajemen Data & Rekam Histori Klien) pada aplikasi Web Psikolog.

---

## 🛠️ LANGKAH EKSEKUSI TERMINAL

Jalankan perintah berikut di terminal proyek `web-psikolog`:

```bash
php artisan make:livewire Clients/ClientIndex
php artisan make:livewire Clients/ClientCreate
php artisan make:livewire Clients/ClientShow
```

---

## 📄 DAFTAR FILE & KODE

### 1. Livewire Component: ClientIndex (Daftar & Pencarian Klien)

**Lokasi:** `app/Livewire/Clients/ClientIndex.php`

```php
<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $genderFilter = '';

    protected $queryString = ['search', 'genderFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        $client->delete(); // Soft Delete

        session()->flash('message', 'Data klien berhasil dihapus.');
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                    ->orWhere('client_code', 'like', '%' . $this->search . '%');
            })
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->withCount('cases')
            ->latest()
            ->paginate(10);

        return view('livewire.clients.client-index', [
            'clients' => $clients
        ])->layout('components.layouts.app');
    }
}
```

---

### 2. Livewire View: ClientIndex

**Lokasi:** `resources/views/livewire/clients/client-index.blade.php`

```html
<div class="p-6 space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Klien</h1>
            <p class="text-sm text-slate-400">Kelola data profil dan direktori klien Anda</p>
        </div>
        <a href="{{ route('clients.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20 gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Klien Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if (session()->has('message'))
        <div class="p-4 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, kode klien, atau nomor HP..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition">
        </div>
        <div>
            <select wire:model.live="genderFilter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                <option value="">Semua Jenis Kelamin</option>
                <option value="male">Laki-Laki</option>
                <option value="female">Perempuan</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        <th class="p-4">Kode & Nama Klien</th>
                        <th class="p-4">Kontak</th>
                        <th class="p-4">Gender / Umur</th>
                        <th class="p-4">Total Kasus</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-sm">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-slate-700/30 transition">
                            <td class="p-4">
                                <a href="{{ route('clients.show', $client->id) }}" class="font-semibold text-teal-400 hover:underline block">
                                    {{ $client->full_name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $client->client_code ?? 'NO-CODE' }}</span>
                            </td>
                            <td class="p-4 text-slate-300">
                                <div>{{ $client->phone_number ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $client->email ?? '-' }}</div>
                            </td>
                            <td class="p-4 text-slate-300 capitalize">
                                {{ $client->gender ?? '-' }} 
                                @if($client->date_of_birth)
                                    <span class="text-slate-500 text-xs">({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} th)</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-300 border border-slate-600">
                                    {{ $client->cases_count }} Kasus
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('clients.show', $client->id) }}" class="p-2 text-slate-400 hover:text-teal-400 inline-block transition" title="Lihat Profil">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button wire:click="deleteClient('{{ $client->id }}')" wire:confirm="Apakah Anda yakin ingin menghapus klien ini?" class="p-2 text-slate-400 hover:text-rose-400 inline-block transition" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                Belum ada data klien yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-700">
            {{ $clients->links() }}
        </div>
    </div>
</div>
```

---

### 3. Livewire Component: ClientCreate (Form Tambah Klien)

**Lokasi:** `app/Livewire/Clients/ClientCreate.php`

```php
<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientCreate extends Component
{
    public $full_name;
    public $nickname;
    public $gender = 'female';
    public $date_of_birth;
    public $phone_number;
    public $email;
    public $address;
    public $occupation;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $emergency_relation;

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:male,female,other',
        'date_of_birth' => 'nullable|date',
        'phone_number' => 'nullable|string|max:30',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'occupation' => 'nullable|string|max:100',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:30',
        'emergency_relation' => 'nullable|string|max:50',
    ];

    public function save()
    {
        $validatedData = $this->validate();
        
        // Generate Client Code sederhana (contoh: CLI-202610-001)
        $count = Client::count() + 1;
        $validatedData['client_code'] = 'CLI-' . date('Ym') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $client = Client::create($validatedData);

        session()->flash('message', 'Klien berhasil didaftarkan!');
        return redirect()->route('clients.show', $client->id);
    }

    public function render()
    {
        return view('livewire.clients.client-create')->layout('components.layouts.app');
    }
}
```

---

### 4. Livewire View: ClientCreate

**Lokasi:** `resources/views/livewire/clients/client-create.blade.php`

```html
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Tambah Klien Baru</h1>
            <p class="text-sm text-slate-400">Isi data identitas dan kontak darurat klien</p>
        </div>
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-6">
        <!-- Section Data Pribadi -->
        <div>
            <h3 class="text-lg font-semibold text-teal-400 mb-4 pb-2 border-b border-slate-700">1. Identitas Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Lengkap *</label>
                    <input wire:model="full_name" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                    @error('full_name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Panggilan</label>
                    <input wire:model="nickname" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Jenis Kelamin *</label>
                    <select wire:model="gender" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                        <option value="female">Perempuan</option>
                        <option value="male">Laki-Laki</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Tanggal Lahir</label>
                    <input wire:model="date_of_birth" type="date" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor WhatsApp / HP</label>
                    <input wire:model="phone_number" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email</label>
                    <input wire:model="email" type="email" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Pekerjaan</label>
                    <input wire:model="occupation" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Alamat Lengkap</label>
                    <textarea wire:model="address" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition"></textarea>
                </div>
            </div>
        </div>

        <!-- Section Kontak Darurat -->
        <div>
            <h3 class="text-lg font-semibold text-teal-400 mb-4 pb-2 border-b border-slate-700">2. Kontak Darurat</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nama Kontak</label>
                    <input wire:model="emergency_contact_name" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Nomor Telepon</label>
                    <input wire:model="emergency_contact_phone" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Hubungan / Relasi</label>
                    <input wire:model="emergency_relation" type="text" placeholder="misal: Orang Tua, Pasangan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 transition">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-6 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition shadow-lg shadow-teal-500/20">
                Simpan Data Klien
            </button>
        </div>
    </form>
</div>
```

---

### 5. Livewire Component: ClientShow (Profil & Histori Kasus Klien)

**Lokasi:** `app/Livewire/Clients/ClientShow.php`

```php
<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientShow extends Component
{
    public Client $client;

    public function mount($id)
    {
        $this->client = Client::with(['cases.sessions'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.clients.client-show')->layout('components.layouts.app');
    }
}
```

---

### 6. Livewire View: ClientShow

**Lokasi:** `resources/views/livewire/clients/client-show.blade.php`

```html
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white">{{ $client->full_name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                    {{ $client->client_code }}
                </span>
            </div>
            <p class="text-sm text-slate-400">Pendaftaran: {{ $client->created_at->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-semibold rounded-xl transition text-sm">
                Kembali
            </a>
            <a href="{{ route('cases.create', ['client_id' => $client->id]) }}" class="px-4 py-2 bg-teal-500 hover:bg-teal-600 text-slate-900 font-semibold rounded-xl transition text-sm shadow-lg shadow-teal-500/20">
                + Buat Kasus Baru
            </a>
        </div>
    </div>

    <!-- Client Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Detail Klien -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="text-base font-semibold text-white border-b border-slate-700 pb-3">Informasi Detail</h3>
            
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Jenis Kelamin</span>
                    <span class="text-slate-200 capitalize">{{ $client->gender ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Tanggal Lahir / Umur</span>
                    <span class="text-slate-200">
                        {{ $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('d M Y') : '-' }}
                        @if($client->date_of_birth)
                            ({{ \Carbon\Carbon::parse($client->date_of_birth)->age }} Tahun)
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Telepon / WhatsApp</span>
                    <span class="text-slate-200">{{ $client->phone_number ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Email</span>
                    <span class="text-slate-200">{{ $client->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Pekerjaan</span>
                    <span class="text-slate-200">{{ $client->occupation ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-semibold block">Alamat</span>
                    <span class="text-slate-200">{{ $client->address ?? '-' }}</span>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="pt-4 border-t border-slate-700">
                <h4 class="text-xs font-semibold uppercase text-rose-400 tracking-wider mb-2">Kontak Darurat</h4>
                <p class="text-sm font-semibold text-white">{{ $client->emergency_contact_name ?? '-' }}</p>
                <p class="text-xs text-slate-400">{{ $client->emergency_relation ?? '-' }} • {{ $client->emergency_contact_phone ?? '-' }}</p>
            </div>
        </div>

        <!-- Main History: Cases & Sessions -->
        <div class="md:col-span-2 space-y-4">
            <h3 class="text-lg font-semibold text-white">Riwayat Kasus / Rekam Medis ({{ $client->cases->count() }})</h3>

            @forelse ($client->cases as $case)
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl space-y-4">
                    <div class="flex items-start justify-between border-b border-slate-700/60 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-700 text-slate-300">
                                    {{ $case->category ?? 'Umum' }}
                                </span>
                                <h4 class="text-base font-bold text-white">{{ $case->title }}</h4>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Status: <span class="text-teal-400 font-semibold uppercase">{{ $case->status }}</span></p>
                        </div>
                        <a href="{{ route('cases.show', $case->id) }}" class="text-xs text-teal-400 hover:underline">
                            Lihat Kasus &rarr;
                        </a>
                    </div>

                    <p class="text-sm text-slate-300">{{ Str::limit($case->complaint, 150) }}</p>

                    <!-- Daftar Sesi Singkat -->
                    <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-700/40">
                        <span class="text-xs font-semibold text-slate-400 block mb-2">Riwayat Sesi Konseling:</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($case->sessions as $session)
                                <span class="px-2.5 py-1 bg-slate-800 border border-slate-700 rounded-lg text-xs text-slate-300">
                                    Sesi #{{ $session->session_number }} ({{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }})
                                </span>
                            @empty
                                <span class="text-xs text-slate-500 italic">Belum ada sesi yang dijadwalkan.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center text-slate-500">
                    Klien ini belum memiliki riwayat kasus. Klik tombol "+ Buat Kasus Baru" untuk memulai.
                </div>
            @endforelse
        </div>
    </div>
</div>
```

---

### 7. Update Routing Web

**Lokasi:** `routes/web.php`

Tambahkan route manajemen klien di dalam grup `pin.protected`:

```php
use App\Livewire\Clients\ClientIndex;
use App\Livewire\Clients\ClientCreate;
use App\Livewire\Clients\ClientShow;

Route::middleware(['pin.protected'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');

    // Modul Klien
    Route::get('/clients', ClientIndex::class)->name('clients.index');
    Route::get('/clients/create', ClientCreate::class)->name('clients.create');
    Route::get('/clients/{id}', ClientShow::class)->name('clients.show');
});
```

---

## ⚡ TEST EXECUTION

1. Buka browser dan login PIN **123456**.
2. Masuk ke url `http://127.0.0.1:8000/clients`.
3. Coba tambah klien baru, lalu periksa apakah pendaftaran dan halaman profil histori klien berjalan secara lancar!