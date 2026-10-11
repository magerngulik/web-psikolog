# Roadmap & Rencana Implementasi: Konversi Web Psikolog Menjadi Aplikasi Desktop (Windows & macOS)

Dokumen ini berisi arsitektur, riset perbandingan paket, dan panduan lengkap langkah-demi-langkah untuk mengubah aplikasi **Web Psikolog (Clinical Sanctuary)** menjadi aplikasi desktop mandiri lintas platform (Windows `.exe` dan macOS `.dmg`).

---

## 🏆 Hasil Riset: Paket Terbaik untuk Laravel Desktop

### Pilihan Rekomendasi Resmi: **NativePHP (`nativephp/desktop`)**
* **Repository Resmi:** [github.com/NativePHP/desktop](https://github.com/NativePHP/desktop) (v2.3.1)
* **Pengembang:** Marcel Pociot & BeyondCode (Komunitas resmi ekosistem Laravel).
* **Kompatibilitas:** Mendukung penuh **Laravel 11, 12, dan 13** serta **PHP 8.3 & 8.5**.

### Mengapa NativePHP Paling Ideal untuk Web Psikolog?
1. **Dibuat Khusus untuk Laravel:** Menggunakan kode PHP, Livewire v4, dan Tailwind CSS yang sudah ada tanpa perlu rewrite frontend.
2. **Kompilasi Cross-Platform:**
   * Target **macOS:** Menghasilkan file installer `.dmg` (Apple Silicon & Intel).
   * Target **Windows:** Menghasilkan file installer `.exe` (NSIS Installer & Portable untuk Windows 10/11).
3. **100% Self-Contained & Full Offline:** Pengguna tidak perlu menginstal PHP, Node, Composer, atau web server. Runtime PHP dan SQLite sudah tertanam di dalam aplikasi.
4. **Penyimpanan Database Aman:** Database SQLite disimpan di folder sistem bawaan OS:
   * **Windows:** `%APPDATA%\Web Psikolog\database\database.sqlite`
   * **macOS:** `~/Library/Application Support/Web Psikolog/database/database.sqlite`
5. **Fitur Offline Sudah Siap:** Aplikasi ini sudah menggunakan font lokal dan Vite pre-compiled CSS, sehingga performanya sangat cepat (*zero latency*).

---

## 📊 Matriks Perbandingan Teknologi Alternatif

| Parameter | **NativePHP (`nativephp/desktop`)** ⭐ *(Pilihan Utama)* | **Tauri v2 + PHP Sidecar** | **PHP-Desktop** |
|---|---|---|---|
| **Dukungan Laravel Modern** | ✅ **Sangat Baik (Resmi)** | ⚠️ Perlu konfigurasi manual | ❌ Tidak mendukung |
| **Output Windows & Mac** | ✅ **Ya (.exe & .dmg)** | ✅ **Ya** | ⚠️ Terbatas Windows |
| **Kemudahan Maintenance** | ✅ **Tinggi (PHP & Blade murni)** | ❌ Rendah (Butuh bahasa Rust) | ❌ Sangat rendah |
| **Integrasi Native OS** | ✅ **Lengkap (Menu, Tray, Dialog)** | ⚠️ Terbatas | ❌ Minim |
| **Ukuran Installer Akhir** | 📦 ~80 - 120 MB (Termasuk PHP Engine) | 📦 ~25 - 35 MB | 📦 ~70 MB |
| **Status Maintenance** | ✅ **Sangat Aktif** | ⚠️ Eksperimental | ❌ *Abandoned* |

---

## 🛠️ Langkah-Langkah Eksekusi (Ketika Siap Diimplementasikan)

### Tahap 1: Prasyarat Lingkungan Build
Sebelum menjalankan build desktop, pastikan Node.js terpasang di komputer Mac pengembangan:
```bash
brew install node
```

### Tahap 2: Instalasi Paket di Proyek
```bash
composer require nativephp/desktop
php artisan native:install
```

### Tahap 3: Konfigurasi Identitas Aplikasi
File: `config/nativephp.php`
```php
return [
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),
    'app_id' => 'com.webpsikolog.sanctuary',
    'deep_link_scheme' => 'webpsikolog',
    'author' => 'Basirah, M.Psi., Psikolog',
    'updater' => [
        'enabled' => false,
    ],
];
```

### Tahap 4: Konfigurasi Jendela Aplikasi (Desktop Window)
File: `app/Providers/NativeAppServiceProvider.php`
```php
namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        Window::open()
            ->title('Web Psikolog — Clinical Practice & Records')
            ->width(1366)
            ->height(860)
            ->minWidth(1024)
            ->minHeight(700)
            ->rememberState()
            ->backgroundColor('#080f14')
            ->showDevTools(false);
    }

    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'max_execution_time' => '120',
        ];
    }
}
```

---

## 🚀 Perintah Menjalankan & Membangun Installer

### 1. Menjalankan dalam Mode Desktop Development
Untuk menguji aplikasi langsung di layar desktop Anda:
```bash
php artisan native:serve
```

### 2. Membangun Installer untuk macOS (.dmg)
```bash
php artisan native:build mac
```
*Output tersimpan di:* `dist/mac/` atau `dist/mac-arm64/`

### 3. Membangun Installer untuk Windows (.exe)
```bash
php artisan native:build win
```
*Output tersimpan di:* `dist/win/Web Psikolog Setup 1.0.0.exe`

---

## 📝 Catatan Tambahan untuk Developer
* **Backup & Restore Database:** Fitur backup SQL yang ada di menu `/settings/backup` tetap dapat digunakan untuk mengekspor dan mengimpor data klien di aplikasi desktop.
* **Cetak PDF:** Fitur cetak RPP dan Logbook SKP berbasis `laravel-dompdf` berjalan 100% lokal tanpa memerlukan koneksi jaringan luar.

