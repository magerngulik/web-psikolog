# UI Component Styling & Grid Clearance Guidelines

1. **Fixed-Grid Widget Clearance:**
   - Saat melakukan custom styling pada widget pihak ketiga yang memiliki grid internal berukuran tetap (seperti kalender Flatpickr 7-kolom berukuran ~308px), JANGAN PERNAH menambahkan padding kontainer tanpa mengompensasi lebar luar kontainer.
   - Formula lebar kontainer:
     `width = grid_inner_width + (2 * padding) + (2 * border_width)`
   - Selalu set `box-sizing: border-box` dan pastikan kontainer kalender memiliki lebar eksplisit (misal `328px` untuk padding `0.65rem`) agar seluruh 7 kolom hari memiliki ruang napas yang cukup dan tidak terpotong di tepi kanan (kolom Minggu / "MIN").

2. **Localization Label Fit:**
   - Selalu pastikan lebar kolom hari (`.flatpickr-weekday`) cukup menampung singkatan bahasa Indonesia (Sen, Sel, Rab, Kam, Jum, Sab, Min) tanpa *word-break* atau *overflow hidden*.

3. **Visual Hierarchy & Header Spacing:**
   - Berikan margin/gap yang cukup antara dropdown pemilih bulan dan input angka tahun agar tidak saling bertumpuk atau terlihat sempit.

4. **Strict 7-Column Grid Invariant:**
   - Komponen tanggal kalender wajib mengunci lebar cell harian pada persentase 7 kolom:
     `width: 14.2857143% !important; flex-basis: 14.2857143% !important; max-width: 14.2857143% !important; margin: 0 !important;`
   - Jangan gunakan fixed pixel `max-width` (misal 38px) pada cell hari tanpa persentase lebar, karena pada kontainer 308px dapat menyebabkan 8 cell muat dalam satu baris (38px * 8 = 304px <= 308px) dan merusak perputaran mingguan.

5. **Clean Month Boundaries (No Spilled Outer-Month Dates):**
   - Tanggal dari bulan sebelumnya (`.prevMonthDay`) dan bulan berikutnya (`.nextMonthDay`) harus disembunyikan secara visual (`visibility: hidden !important; pointer-events: none !important; opacity: 0 !important;`) sehingga hanya tanggal bulan aktif yang tampil.
   - Gunakan `visibility: hidden` (bukan `display: none`) untuk hari-hari awal dan akhir parsial agar struktur kolom hari kalender tetap terjaga posisinya.
   - Jika satu baris penuh di akhir kalender (baris ke-6) seluruhnya terdiri dari tanggal bulan berikutnya, baris tersebut harus dipangkas (`display: none`) agar kalender berhenti rapi di baris terakhir bulan aktif.


