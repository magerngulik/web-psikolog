# MODUL 2: SECURITY KEY & LOCK SCREEN PIN 6-DIGIT

Dokumen ini berisi panduan dan seluruh kode file yang dibutuhkan untuk mengeksekusi **Modul 2** pada aplikasi Web Psikolog.

---

## 🛠️ LANGKAH EKSEKUSI TERMINAL

Jalankan perintah berikut di terminal proyek `web-psikolog`:

```bash
php artisan make:model SecurityKey -m
php artisan make:seeder SecurityKeySeeder
php artisan make:middleware CheckSecurityPin
php artisan make:livewire LockScreen
```

---

## 📄 DAFTAR FILE & KODE

### 1. File Migration Security Key
**Lokasi:** `database/migrations/xxxx_xx_xx_xxxxxx_create_security_keys_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('pin_hash');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_keys');
    }
};
```

---

### 2. File Model SecurityKey
**Lokasi:** `app/Models/SecurityKey.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityKey extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];
}
```

---

### 3. File Seeder SecurityKey (Default PIN: `123456`)
**Lokasi:** `database/seeders/SecurityKeySeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityKeySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('security_keys')->updateOrInsert(
            ['is_active' => true],
            [
                'id' => Str::uuid()->toString(),
                'pin_hash' => Hash::make('123456'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
```

> **Catatan:** Jangan lupa daftarkan `SecurityKeySeeder::class` di file `database/seeders/DatabaseSeeder.php`.

---

### 4. File Middleware Keamanan PIN
**Lokasi:** `app/Http/Middleware/CheckSecurityPin.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSecurityPin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->get('pin_unlocked')) {
            return redirect()->route('lock-screen');
        }

        return $next($request);
    }
}
```

---

### 5. Registrasi Middleware di `bootstrap/app.php` (Laravel 11)
**Lokasi:** `bootstrap/app.php`

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'pin.protected' => \App\Http\Middleware\CheckSecurityPin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

### 6. Component Logic LockScreen
**Lokasi:** `app/Livewire/LockScreen.php`

```php
<?php

namespace App\Livewire;

use App\Models\SecurityKey;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class LockScreen extends Component
{
    public $pin = '';
    public $errorMessage = '';

    public function addNumber($num)
    {
        if (strlen($this->pin) < 6) {
            $this->pin .= $num;
        }

        if (strlen($this->pin) === 6) {
            $this->verifyPin();
        }
    }

    public function deleteNumber()
    {
        $this->pin = substr($this->pin, 0, -1);
        $this->errorMessage = '';
    }

    public function verifyPin()
    {
        $activeKey = SecurityKey::where('is_active', true)->first();

        if ($activeKey && Hash::check($this->pin, $activeKey->pin_hash)) {
            session()->put('pin_unlocked', true);
            return redirect()->route('dashboard');
        } else {
            $this->errorMessage = 'PIN Security Key salah. Silakan coba lagi.';
            $this->pin = '';
        }
    }

    public function render()
    {
        return view('livewire.lock-screen')->layout('components.layouts.guest');
    }
}
```

---

### 7. View UI LockScreen (Keypad Virtual)
**Lokasi:** `resources/views/livewire/lock-screen.blade.php`

```html
<div class="min-h-screen bg-slate-900 flex flex-col items-center justify-center p-4 font-sans">
    <div class="w-full max-w-sm bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-2xl text-center">
        <!-- Header / Logo -->
        <div class="mb-6">
            <div class="w-16 h-16 bg-teal-500/10 border border-teal-500/30 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Web Psikolog</h2>
            <p class="text-sm text-slate-400">Masukkan 6-Digit PIN Security Key</p>
        </div>

        <!-- Indicator Bulat PIN -->
        <div class="flex justify-center gap-3 mb-6">
            @for ($i = 0; $i < 6; $i++)
                <div class="w-4 h-4 rounded-full border-2 border-slate-600 flex items-center justify-center transition-all duration-200 {{ strlen($pin) > $i ? 'bg-teal-400 border-teal-400 shadow-lg shadow-teal-500/50' : '' }}"></div>
            @endfor
        </div>

        <!-- Error Alert -->
        @if ($errorMessage)
            <div class="mb-4 text-xs font-semibold text-rose-400 bg-rose-500/10 border border-rose-500/20 py-2 px-3 rounded-lg animate-bounce">
                {{ $errorMessage }}
            </div>
        @endif

        <!-- Keypad Virtual -->
        <div class="grid grid-cols-3 gap-3 mb-4">
            @foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9] as $num)
                <button wire:click="addNumber({{ $num }})" type="button" class="h-14 text-xl font-semibold text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 rounded-xl transition active:scale-95">
                    {{ $num }}
                </button>
            @endforeach
            <div class="h-14"></div>
            <button wire:click="addNumber(0)" type="button" class="h-14 text-xl font-semibold text-white bg-slate-700/50 hover:bg-slate-700 border border-slate-600/50 rounded-xl transition active:scale-95">
                0
            </button>
            <button wire:click="deleteNumber" type="button" class="h-14 text-sm font-semibold text-rose-400 bg-slate-700/50 hover:bg-rose-500/20 border border-slate-600/50 rounded-xl transition active:scale-95 flex items-center justify-center">
                ⌫
            </button>
        </div>

        <p class="text-xs text-slate-500 mt-4">Default PIN awal: <span class="text-slate-300 font-mono">123456</span></p>
    </div>
</div>
```

---

### 8. Routing Web
**Lokasi:** `routes/web.php`

```php
use App\Livewire\LockScreen;
use Illuminate\Support\Facades\Route;

// Guest Lock Screen
Route::get('/lock-screen', LockScreen::class)->name('lock-screen');

// Protected Routes
Route::middleware(['pin.protected'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');
});
```

---

## ⚡ FINISHING

Jalankan perintah ini di terminal setelah memasang semua file:

```bash
php artisan migrate:fresh --seed
```

Akses `http://127.0.0.1:8000`, dan kamu akan disambut oleh Lock Screen PIN 6-Digit!