# MODUL 6 SPECIFICATION & CODE DOCUMENTATION

**Nama System:** Internal Practice & Clinical Management System (Web Psikolog)  
**Modul:** Modul 6 - System Settings, Master Data Management & Security Configuration  
**Tech Stack:** Laravel, Livewire 3, Tailwind CSS, SQLite  

---

## 1. DESKRIPSI MODUL 6

Modul 6 berfokus pada pengoperasian tingkat sistem (*system settings*), pengelolaan data referensi dinamis (*master reference manager*), serta proteksi keamanan rekam medis (*security settings*).

### Fitur Utama:
1. **Master Reference Manager (`SystemReferences`)**
   * Pengelolaan opsi *dropdown* dinamis untuk Kategori Kasus, Tipe Follow-Up, dan Metode Pembayaran tanpa perlu mengubah *hardcoded values* di kodingan.
2. **Pengaturan Keamanan & Security Key (`SecuritySettings`)**
   * Manajemen PIN 6-Digit untuk proteksi rekam medis.
   * Fitur *Instant Lock Screen* untuk mengunci aplikasi secara langsung saat meninggalkan perangkat.
3. **Penyelarasan System Routing**
   * Pemasangan middleware proteksi PIN pada seluruh *route* administratif dan pengoperasian.

---

## 2. ARSITEKTUR DATABASE (TABEL TERKAIT)

### 2.1 Tabel `system_references`

```sql
CREATE TABLE system_references (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_key VARCHAR(50) NOT NULL, -- 'case_category', 'follow_up_type', 'payment_method'
    label VARCHAR(255) NOT NULL,     -- Tampilan UI (e.g. "Depresi Ringan")
    value VARCHAR(255) NOT NULL,     -- Value sistem (e.g. "depresi_ringan")
    sort_order INTEGER DEFAULT 1,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 2.2 Tabel `security_keys`

```sql
CREATE TABLE security_keys (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pin_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 3. IMPLEMENTASI KODE (BACKEND & FRONTEND)

### 3.1 Komponen Reference Manager (`app/Livewire/Settings/ReferenceManager.php`)

```php
<?php

namespace App\Livewire\Settings;

use App\Models\SystemReference;
use Livewire\Component;

class ReferenceManager extends Component
{
    public $group_key = 'case_category';
    public $label = '';
    public $value = '';
    public $editingId = null;

    protected $rules = [
        'group_key' => 'required|string',
        'label' => 'required|string|max:255',
        'value' => 'required|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            $ref = SystemReference::findOrFail($this->editingId);
            $ref->update([
                'label' => $this->label,
                'value' => $this->value,
            ]);
            session()->flash('message', 'Opsi berhasil diperbarui!');
        } else {
            SystemReference::create([
                'group_key' => $this->group_key,
                'label' => $this->label,
                'value' => $this->value,
                'sort_order' => SystemReference::where('group_key', $this->group_key)->count() + 1,
            ]);
            session()->flash('message', 'Opsi baru berhasil ditambahkan!');
        }

        $this->reset(['label', 'value', 'editingId']);
    }

    public function edit($id)
    {
        $ref = SystemReference::findOrFail($id);
        $this->editingId = $ref->id;
        $this->group_key = $ref->group_key;
        $this->label = $ref->label;
        $this->value = $ref->value;
    }

    public function toggleActive($id)
    {
        $ref = SystemReference::findOrFail($id);
        $ref->update(['is_active' => !$ref->is_active]);
    }

    public function render()
    {
        return view('livewire.settings.reference-manager', [
            'references' => SystemReference::where('group_key', $this->group_key)
                ->orderBy('sort_order')
                ->get(),
        ])->layout('components.layouts.app');
    }
}
```

### 3.2 View Reference Manager (`resources/views/livewire/settings/reference-manager.blade.php`)

```html
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Master Reference Manager</h2>
        <p class="text-sm text-slate-500 mb-6">Kelola opsi dropdown kategori kasus, tipe tindak lanjut, dan metode pembayaran.</p>

        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex gap-2 mb-6 border-b border-slate-200 pb-4">
            <button wire:click="$set('group_key', 'case_category')" class="px-4 py-2 text-sm font-medium rounded-lg {{ $group_key === 'case_category' ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600' }}">Kategori Kasus</button>
            <button wire:click="$set('group_key', 'follow_up_type')" class="px-4 py-2 text-sm font-medium rounded-lg {{ $group_key === 'follow_up_type' ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600' }}">Tipe Follow Up</button>
            <button wire:click="$set('group_key', 'payment_method')" class="px-4 py-2 text-sm font-medium rounded-lg {{ $group_key === 'payment_method' ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600' }}">Metode Bayar</button>
        </div>

        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Label Tampilan</label>
                <input type="text" wire:model="label" placeholder="misal: Depresi Ringan" class="w-full text-sm border-slate-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Value Sistem</label>
                <input type="text" wire:model="value" placeholder="misal: depresi_ringan" class="w-full text-sm border-slate-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-teal-600 text-white text-sm font-semibold py-2 px-4 rounded-lg hover:bg-teal-700 transition">
                    {{ $editingId ? 'Update Opsi' : '+ Tambah Opsi' }}
                </button>
            </div>
        </form>

        <div class="divide-y divide-slate-100">
            @foreach($references as $item)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-slate-800 text-sm">{{ $item->label }}</span>
                        <span class="text-xs text-slate-400 font-mono ml-2">({{ $item->value }})</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="toggleActive('{{ $item->id }}')" class="text-xs font-medium px-2.5 py-1 rounded-full {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item->is_active ? 'Aktif' : 'Non-aktif' }}
                        </button>
                        <button wire:click="edit('{{ $item->id }}')" class="text-xs text-teal-600 font-medium hover:underline">Edit</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
```

### 3.3 Komponen Security Settings (`app/Livewire/Settings/SecuritySettings.php`)

```php
<?php

namespace App\Livewire\Settings;

use App\Models\SecurityKey;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class SecuritySettings extends Component
{
    public $current_pin = '';
    public $new_pin = '';
    public $new_pin_confirmation = '';

    public function updatePin()
    {
        $this->validate([
            'current_pin' => 'required|digits:6',
            'new_pin' => 'required|digits:6|different:current_pin',
            'new_pin_confirmation' => 'required|same:new_pin',
        ]);

        $activeKey = SecurityKey::where('is_active', true)->first();

        if (!$activeKey || !Hash::check($this->current_pin, $activeKey->pin_hash)) {
            $this->addError('current_pin', 'PIN lama yang Anda masukkan salah.');
            return;
        }

        $activeKey->update([
            'pin_hash' => Hash::make($this->new_pin),
        ]);

        $this->reset(['current_pin', 'new_pin', 'new_pin_confirmation']);
        session()->flash('message', 'PIN Security Key berhasil diperbarui!');
    }

    public function lockNow()
    {
        session()->forget('pin_unlocked');
        return redirect()->route('lock-screen');
    }

    public function render()
    {
        return view('livewire.settings.security-settings')->layout('components.layouts.app');
    }
}
```

### 3.4 View Security Settings (`resources/views/livewire/settings/security-settings.blade.php`)

```html
<div class="max-w-xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Pengaturan PIN & Keamanan</h2>
        <p class="text-sm text-slate-500 mb-6">Ubah PIN 6-Digit untuk memproteksi rekam medis pasien.</p>

        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePin" class="space-y-4 mb-8">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">PIN Saat Ini</label>
                <input type="password" maxlength="6" wire:model="current_pin" class="w-full text-sm border-slate-300 rounded-lg">
                @error('current_pin') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">PIN Baru (6 Digit)</label>
                <input type="password" maxlength="6" wire:model="new_pin" class="w-full text-sm border-slate-300 rounded-lg">
                @error('new_pin') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Konfirmasi PIN Baru</label>
                <input type="password" maxlength="6" wire:model="new_pin_confirmation" class="w-full text-sm border-slate-300 rounded-lg">
                @error('new_pin_confirmation') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full bg-teal-600 text-white font-semibold py-2.5 rounded-lg text-sm hover:bg-teal-700 transition">
                Simpan PIN Baru
            </button>
        </form>

        <hr class="my-6 border-slate-200">

        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-sm font-semibold text-slate-800">Kunci Aplikasi Sekarang</h4>
                <p class="text-xs text-slate-500">Langsung kembali ke Lock Screen PIN.</p>
            </div>
            <button wire:click="lockNow" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-700 transition">
                🔒 Kunci Layar
            </button>
        </div>
    </div>
</div>
```

---

## 4. INTEGRASI ROUTING (`routes/web.php`)

```php
use App\Livewire\Settings\ReferenceManager;
use App\Livewire\Settings\SecuritySettings;

Route::middleware(['pin.protected'])->group(function () {
    // Route Modul 1 - 5 ...
    
    // Modul 6 Route Settings
    Route::get('/settings/references', ReferenceManager::class)->name('settings.references');
    Route::get('/settings/security', SecuritySettings::class)->name('settings.security');
});
```