# Panduan Aktivasi Lisensi Perangkat (Hardware-Locked License Guide)
**Web Psikolog — Clinical Practice & Records**
*Versi Dokumen: 1.0 | Revisi: Oktober 2026*

---

## Daftar Isi
1. [Pengantar & Konsep Lisensi Beli Putus](#1-pengantar--konsep-lisensi-beli-putus)
2. [Panduan untuk Klien (Pembeli / Psikolog)](#2-panduan-untuk-klien-pembeli--psikolog)
   - [Langkah 1: Membuka Aplikasi di Laptop](#langkah-1-membuka-aplikasi-di-laptop)
   - [Langkah 2: Mengirimkan Kode Perangkat (Machine ID)](#langkah-2-mengirimkan-kode-perangkat-machine-id)
   - [Langkah 3: Memasukkan Kunci Lisensi](#langkah-3-memasukkan-kunci-lisensi)
   - [Langkah 4: Konfirmasi Teraktivasi](#langkah-4-konfirmasi-teraktivasi)
3. [Panduan untuk Developer / Admin (Penerbit Lisensi)](#3-panduan-untuk-developer--admin-penerbit-lisensi)
   - [Prasyarat Private Key](#prasyarat-private-key)
   - [Perintah Generate Lisensi](#perintah-generate-lisensi)
   - [Format Komunikasi ke Klien](#format-komunikasi-ke-klien)
4. [Tanya Jawab Umum (FAQ & Troubleshooting)](#4-tanya-jawab-umum-faq--troubleshooting)

---

## 1. Pengantar & Konsep Lisensi Beli Putus

Aplikasi **Web Psikolog** mengadopsi model lisensi **Beli Putus (One-Time Lifetime Purchase)** yang terkunci pada perangkat keras fisik (*Hardware-Locked*).

### Karakteristik Utama:
- **100% Offline (Air-Gapped Ready):** Proses aktivasi dan verifikasi sama sekali tidak memerlukan koneksi internet pada laptop klinik.
- **Terkunci ke Perangkat Fisik (Machine ID):** Satu lisensi hanya sah untuk satu laptop (terikat pada UUID motherboard dan prosesor). Jika file aplikasi dipindahkan ke laptop lain, aplikasi otomatis menolak dan meminta lisensi baru.
- **Kriptografi Asimetris Ed25519:** Menggunakan standar kriptografi kurva eliptis modern (Edwards-curve Digital Signature Algorithm). Kunci privat (*Private Key*) hanya dipegang oleh Developer, sedangkan aplikasi klien hanya memegang *Public Key* pemeriksa. Mustahil bagi pihak luar untuk memalsukan lisensi.

---

## 2. Panduan untuk Klien (Pembeli / Psikolog)

### Langkah 1: Membuka Aplikasi di Laptop
1. Pasang aplikasi melalui file installer yang diberikan (`.dmg` untuk macOS atau `.exe` untuk Windows).
2. Buka aplikasi **Web Psikolog**.
3. Jika perangkat baru pertama kali dipasang, sistem akan langsung menampilkan **Layar Aktivasi Lisensi**.

```
+---------------------------------------------------------------+
|  Web Psikolog — Aktivasi Lisensi Perangkat                    |
|  Status: Hardware-Locked Security • Beli Putus                |
+---------------------------------------------------------------+
|  1. KODE PERANGKAT LAPTOP ANDA (Machine ID)                   |
|     [ WP-A4B0-9F44-4888-BF8B ]  [ Salin ]                     |
|                                                               |
|     [ QR Code WA ]  Scan QR di samping pakai kamera HP/WA     |
|                     untuk kirim kode secara instan.           |
|                                                               |
|  2. KUNCI LISENSI                                             |
|     [ Masukkan kode WP-LIC-... di sini                      ] |
|                                                               |
|     [ Tombol: Aktifkan Aplikasi Sekarang -> ]                 |
+---------------------------------------------------------------+
```

---

### Langkah 2: Mengirimkan Kode Perangkat (Machine ID)
Klien memiliki 2 pilihan mudah untuk mengirimkan Machine ID ke Admin:

#### Opsi A: Scan QR Code via Smartphone (Direkomendasikan — Paling Cepat & Bebas Typo)
1. Buka kamera smartphone atau pemindai QR di WhatsApp HP Anda.
2. Arahkan kamera ke gambar **QR Code** yang tertera di layar laptop.
3. HP Anda akan otomatis membuka percakapan WhatsApp ke nomor Admin dengan draf pesan:
   > *"Halo Admin Web Psikolog, saya ingin aktivasi lisensi: Nama: [Nama Anda], Machine ID: WP-A4B0-9F44-4888-BF8B"*
4. Isi nama lengkap Anda dan tekan tombol **Kirim** di WhatsApp.

#### Opsi B: Salin Teks Manual
1. Klik tombol **"📋 Salin"** di sebelah kode Machine ID.
2. Kirimkan teks tersebut melalui chat WhatsApp atau Email ke Admin.

---

### Langkah 3: Memasukkan Kunci Lisensi
1. Admin akan membalas pesan Anda dengan memberikan **Kunci Lisensi Resmi** (diawali dengan `WP-LIC-...`).
2. Di layar laptop Anda, tempelkan (*Paste*) kode tersebut ke kotak input **"Kunci Lisensi"**.
   *(Tersedia tombol pintas "Tempel dari Clipboard" atau muat file `.lic`).*
3. Klik tombol hijau: **"Aktifkan Aplikasi Sekarang →"**.

---

### Langkah 4: Konfirmasi Teraktivasi
1. Aplikasi akan memverifikasi tanda tangan digital secara offline (< 0.1 detik).
2. Muncul notifikasi sukses:
   > *"Selamat datang! Lisensi aplikasi Web Psikolog telah aktif seumur hidup."*
3. Layar aktivasi akan tertutup selamanya, dan Anda langsung diarahkan ke layar PIN Vault / Dashboard.
4. Anda dapat mengecek status lisensi kapan saja di menu:
   **Pengaturan > Profil Psikolog & Tempat Praktik > Status Lisensi Perangkat**.

---

## 3. Panduan untuk Developer / Admin (Penerbit Lisensi)

Sebagai pemilik produk dan pemegang hak cipta, Anda memiliki alat bantu mandiri untuk menerbitkan lisensi bagi setiap pembeli.

### Prasyarat Private Key
Pastikan file `.env` di laptop kerja Anda memiliki baris:
```env
APP_LICENSE_PRIVATE_KEY="26nHhGS5AGNiQwR8kVXEey2jdydB6YbeZ18woMgL4mjJJpXfIvxquV/SM6GbztQ9syzqFTbLFEOLm0fDQFYQgg=="
LICENSE_SUPPORT_WHATSAPP="628xxxxxxxxxx"
```
*(Catatan: Kunci privat ini bersifat sangat rahasia. NativePHP telah dikonfigurasi untuk secara otomatis menghapus kunci ini dari file installer klien saat proses build produksi).*

---

### Perintah Generate Lisensi
Ketika Anda menerima chat dari pembeli berisi Machine ID, jalankan perintah berikut di terminal:

```bash
php artisan license:generate {MACHINE_ID} --client="{NAMA_KLIEN}"
```

#### Contoh Eksekusi:
```bash
php artisan license:generate WP-A4B0-9F44-4888-BF8B --client="Basirah, M.Psi., Psikolog"
```

#### Output Terminal:
```text
======================================================================
           WEB PSIKOLOG — GENERATOR KUNCI LISENSI RESMI               
======================================================================
 Nama Klien  : Basirah, M.Psi., Psikolog
 Machine ID  : WP-A4B0-9F44-4888-BF8B
 Tipe Lisensi: LIFETIME (Beli Putus)
 Tgl Rilis   : 12 October 2026
----------------------------------------------------------------------
KUNCI LISENSI AKTIVASI (Kirimkan kode ini ke WhatsApp Klien):

WP-LIC-eyJtIjoiV1AtQTRCMC05RjQ0LTQ4ODgtQkY4QiIsImMiOiJCYXNpcmFoLCBNLlBzaS4sIFBzaWtvbG9nIiwidCI6ImxpZmV0aW1lIiwiaSI6IjIwMjYtMTAtMTIiLCJzIjoiTTlpZUhnb3B6b3llZDhidSt6T2FUMVZ6aVRmSXhBakFvSStcL2oxSWdjRmRLQnFhOXNwck5KZ01Oa1ozeEpETlY2c0ZrYXpUd2ROdExYc25OQm8xWEFBPT0ifQ==

======================================================================
✓ Kunci lisensi Ed25519 berhasil di-generate secara matematis.
```

---

### Format Komunikasi ke Klien
Salin baris kode lisensi di atas dan kirimkan ke pembeli melalui WhatsApp:

> **Template Pesan WhatsApp untuk Pembeli:**
> 
> *Halo Ibu/Bapak [Nama Klien],*
> 
> *Terima kasih telah mempercayakan Web Psikolog untuk mendukung administrasi praktik klinis Anda.*
> 
> *Berikut adalah Kunci Lisensi Resmi (Beli Putus / Lifetime) untuk perangkat laptop Anda:*
> 
> ```
> [PASTE KODE WP-LIC-... DI SINI]
> ```
> 
> *Cara Aktivasi:*
> 1. Salin seluruh kode di atas.
> 2. Buka aplikasi Web Psikolog di laptop Anda.
> 3. Paste kode ke kolom **Kunci Lisensi**, lalu klik tombol **Aktifkan Aplikasi Sekarang**.
> 4. Aplikasi akan langsung aktif permanen.
> 
> *Salam hangat,*  
> *Tim Pengembang Web Psikolog*

---

## 4. Tanya Jawab Umum (FAQ & Troubleshooting)

### Q1: Apakah klien bisa menggunakan kunci lisensi ini di laptop lain?
**Tidak.** Setiap lisensi ditandatangani secara kriptografis khusus untuk Machine ID laptop tersebut. Jika dicopy ke laptop kedua, aplikasi laptop kedua membaca Machine ID yang berbeda dan akan menolak lisensi tersebut.

### Q2: Bagaimana jika klien menginstal ulang sistem operasi (format Windows/macOS)?
Selama komponen motherboard atau prosesor laptop tidak diganti, Hardware UUID fisik mesin umumnya tetap sama. Klien cukup memasukkan kembali kode lisensi yang lama. Jika terjadi perubahan hardware signifikan, klien dapat menghubungi Anda untuk diterbitkan lisensi baru sesuai kebijakan Anda.

### Q3: Bagaimana jika muncul error "Kunci Lisensi rusak atau tidak valid"?
Penyebab paling umum adalah kode lisensi terpotong saat di-copy dari WhatsApp. Pastikan klien menyalin string lengkap dari awalan `WP-LIC-` sampai karakter terakhir.

### Q4: Apakah data rekam medis pasien aman saat aktivasi?
**Sangat aman.** Proses aktivasi hanya memverifikasi hak pakai software pada perangkat. Seluruh database SQLite rekam medis, asesmen SOAP, dan data klien tetap berada di laptop fisik psikolog tanpa pernah dikirimkan ke internet.
