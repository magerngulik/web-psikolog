# 🧠 Web Psikolog — Internal Practice & Clinical Management System

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-4E5BA6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![SQLite](https://img.shields.io/badge/Database-SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://sqlite.org)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.new)
[![Tests](https://img.shields.io/badge/Tests-39%20Passed-emerald?style=for-the-badge&logo=phpunit&logoColor=white)](#-pengujian--kualitas-kode)

---

## 📖 Tentang Proyek

**Web Psikolog** adalah aplikasi web manajemen praktik psikologi internal, analitik klinis, dan rekam medis digital berbasis **Laravel 13** dan **Livewire 4**. Aplikasi ini dirancang khusus untuk mempermudah praktisi psikolog dalam mengelola direktori klien, kasus medis, penjadwalan sesi konseling dengan **validasi anti-overlap waktu**, penyusunan **Rekam Pemeriksaan Psikologis (RPP KONSELING)** lengkap dengan formulir **Keluhan & Masalah (Subjective)**, **Objective (Dinamika Psikologis)**, **Assessment (Checkbox Metode & Analisis Free Text)**, **Diagnosis PPDGJ-III / ICD-10 Searchable Dropdown & Diagnosis Bebas (Free Text)**, **Intervensi Psikologis**, ekspor PDF resmi dengan **QR Code Token Security Verification**, hingga penguncian dokumen rekam medis yang terlindungi oleh **PIN Security Key 6-Digit**.

---

## 🛠️ Tech Stack & Arsitektur

| Komponen | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Framework Backend** | Laravel 13.x (PHP 8.5) | Routing, Eloquent ORM, Middleware, Service Container |
| **Frontend Reactive** | Livewire 4.x | Komponen SPA reaktif tanpa perlu JavaScript API terpisah |
| **UI Styling** | Tailwind CSS 4.x | Responsive Dark-Mode UI design dengan statistik & chart analitik |
| **Database Engine** | SQLite | Database lokal ringan dengan UUID Primary Keys & Soft Deletes |
| **PDF & QR Engine** | DomPDF & Simple QrCode | Generation dokumen RPP resmi & QR Verification Token |
| **Keamanan Data** | Custom PIN Middleware & QR Security Token | Encrypted Hash PIN 6-Digit, Session Guard, & Verification Token |
| **Test Suite** | PHPUnit / Laravel Testing | 39 Test Suites (83 Assertions) Lulus 100% |

---

## 🚀 Fitur Utama

### 1. 📜 Clinical RPP Generator Engine & Ekspor PDF (Modul 7)
- **Editor RPP Konseling (`/rpp/{appointmentId}`)**:
  - **Subjective (S)**: Form terpisah untuk **Keluhan Klien** dan **Masalah Klien**.
  - **Objective (O)**: Pencatatan hasil observasi & **Dinamika Psikologis**.
  - **Assessment (A)**: Checkbox 4 Metode (*Paper & Pencil Test*, *Inventory*, *Observasi*, *Wawancara*) + **Analisis Klinis Free Text**.
  - **🩺 PPDGJ-III / ICD-10 Searchable Dropdown & Free Text**: Pencarian interaktif kode diagnosis resmi serta textarea khusus untuk **Diagnosis Kustom / Catatan Diagnosis Bebas**.
  - **Intervensi Psikologis**: Checkbox intervensi utama + textarea catatan khusus/rincian teknik intervensi.
  - **Pesan / Tugas Rumah Klien**: Area ketik lapang (`rows="5"`) untuk *Homework / Action Plan*.
- **Cetak Dokumentasi RPP PDF**: Layout cetak presisi A4 dengan Tanda Tangan Digital Psikolog & SIPA.
- **🔒 Validation Token QR Code Security**: QR Code unik di dokumen cetak yang dapat divalidasi keabsahannya secara online via `/verify-rpp/{token}`.

### 2. 📊 Dashboard Analytics & Financial Summary (Modul 7)
- **Kartu Metrik Real-Time (`/`)**: Total Sesi Bulan Ini, Revenue Bulanan, Revenue Tahunan, & Alert **Unclosed Notes** (Notifikasi Rekam Medis Belum Dikunci).
- **🩺 Top 5 Distribusi Diagnosa ICD-10**: Grafik batang frekuensi diagnosa terbanyak.
- **📈 Workload Chart Psikolog**: Beban kerja sesi per hari dalam seminggu.
- **Aksi Cepat RPP**: Tabel daftar sesi terbaru dengan tautan cepat langsung ke RPP Generator.

### 3. 🔑 Lock Screen & Proteksi Keamanan PIN 6-Digit (Modul 2 & 6)
- **Virtual Keypad Lock Screen (`/lock-screen`)**: Seluruh halaman aplikasi dilindungi oleh PIN 6-digit (Default PIN: `123456`).
- **Middleware Guard (`pin.protected`)**: Memastikan akses tanpa sesi `pin_unlocked` selalu di-redirect ke halaman Lock Screen.
- **Pengaturan PIN (`/settings/security`)**: Fitur ubah PIN dengan verifikasi PIN lama dan **Instant Lock Screen (`🔒`)** untuk mengunci aplikasi secara langsung.

### 4. 👥 Manajemen Data Klien / Pasien (Modul 3)
- **Auto-Generate Kode Klien**: Format unik otomatis `CLI-YYYYMM-XXX`.
- **Direktori & Pencarian Real-Time (`/clients`)**: Pencarian nama, kode klien, atau HP, filter jenis kelamin, paginasi, dan *Soft Delete*.
- **Pendaftaran Klien (`/clients/create`)**: Pencatatan identitas pribadi lengkap & kontak darurat (nama, hp, relasi).
- **Profil & Rekam Histori (`/clients/{id}`)**: Menampilkan detail profil, kontak darurat, serta daftar riwayat kasus & sesi konseling.

### 5. 📋 Manajemen Kasus Klinis / Rekam Medis (Modul 4)
- **Auto-Generate Kode Kasus**: Format unik otomatis `CAS-YYYYMM-XXX`.
- **Manajemen Program Terapi (`/cases`)**: Penautan kasus ke klien, pencatatan keluhan awal (*Initial Complaint*), dan target terapi (*Therapy Goal*).
- **Pengubahan Status Kasus**: Toggle status *Active*, *On Hold*, *Completed*, atau *Cancelled*.
- **Evaluasi Perkembangan Global (*Progress Note*)**: Catatan ringkasan perkembangan progres terapi klien secara berkesinambungan.

### 6. 📅 Penjadwalan Sesi Konseling & Catatan Klinis (Modul 5)
- **🛡️ Validasi Anti-Overlap Waktu**: Secara otomatis mendeteksi dan **mencegah bentrokan jadwal** jika ada sesi lain pada tanggal & rentang jam yang sama.
- **Pencatatan Clinical Notes (`/sessions/{id}`)**: Ringkasan sesi, dinamika psikologis, intervensi terapi, serta rekomendasi.
- **🔒 Penguncian Sesi (Lock Session)**: Mengunci dokumen rekam medis menjadi *read-only* demi mematuhi standar etika & hukum rekam medis.
- **Manajemen Transaksi**: Pengelolaan tarif konseling, status pembayaran (*Unpaid, Paid, Waived*), dan metode pembayaran.

### 7. 🗂️ Master Reference Manager (Modul 6)
- Pengelolaan opsi *dropdown* dinamis untuk Kategori Kasus, Tipe Follow-Up, dan Metode Pembayaran via GUI (`/settings/references`).

---

## 🔄 Alur Kerja Sistem (System Workflow)

```mermaid
flowchart TD
    A["🔑 Lock Screen (/lock-screen)"] -->|Masukkan PIN 6-Digit| B["📊 Dashboard Analytics (/)"]
    
    B --> C["👥 Manajemen Klien (/clients)"]
    B --> D["📋 Kasus Klinis (/cases)"]
    B --> E["📅 Sesi Konseling (/sessions)"]
    B --> F["📜 RPP Generator Engine (/rpp/{id})"]
    B --> G["⚙️ Settings (/settings)"]
    
    C -->|Tambah Klien Baru| C1["ClientCreate (Auto Code: CLI-YYYYMM-XXX)"]
    C1 -->|Lihat Profil Klien| C2["ClientShow (Profil & Histori Kasus)"]
    
    C2 -->|Buat Kasus Baru| D1["CaseCreate (Auto Code: CAS-YYYYMM-XXX)"]
    D -->|Kasus Baru| D1
    D1 -->|Lihat Detail Kasus| D2["CaseShow (Progress Note & Timeline Sesi)"]
    
    D2 -->|+ Jadwalkan Sesi Baru| E1["SessionCreate"]
    E -->|+ Jadwalkan Sesi Baru| E1
    
    E1 -->|Cek Bentrok Jadwal| E2{"Terjadi Overlap Waktu?"}
    E2 -- Ya --> E3["⚠️ Tampilkan Peringatan Bentrok"]
    E3 --> E1
    E2 -- Tidak --> E4["Simpan Jadwal Sesi"]
    
    E4 --> F1["RppGenerator (Keluhan, Masalah, Dinamika, PPDGJ-III, Free Text Diagnosis, Asesmen, Intervensi)"]
    F1 -->|Cetak Dokumen| F2["🖨️ Export PDF (DomPDF + QR Code Security)"]
    F2 -->|Scan QR Token| F3["🔍 Public Verification Page (/verify-rpp/{token})"]
```

---

## ⚡ Panduan Instalasi & Jalankan Sistem

### Prerequisites
- PHP `>= 8.3` (Disarankan PHP 8.5)
- Composer `>= 2.x`

### Langkah-Langkah

1. **Clone Repository**:
   ```bash
   git clone <repository-url>
   cd web-psikolog
   ```

2. **Install Composer Dependencies**:
   ```bash
   composer install
   ```

3. **Setup Environment File**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Jalankan Database Migration & Seeding**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Test Suite (Opsional)**:
   ```bash
   php artisan test
   ```

6. **Jalankan Local Web Server**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser pada `http://127.0.0.1:8000`.

---

## 🔑 Kredensial Awal

- **PIN Security Key Default**: `123456`
  *(Dapat diubah kapan saja via menu **Keamanan PIN** di `/settings/security`)*

---

## 🧪 Pengujian & Kualitas Kode

Seluruh fungsi aplikasi dilindungi oleh pengujian otomatis (*Automated Feature Testing*):

```bash
php artisan test
```

**Hasil Pengujian:**
- **39 Test Suites**
- **83 Assertions**
- **Status:** `100% PASSED`

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT License](LICENSE).
