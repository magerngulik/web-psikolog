@echo off
setlocal enabledelayedexpansion
cd /d "%~dp0"
title Web Psikolog - Update dari GitHub

echo ========================================================
echo   Web Psikolog - Update / Tarik Perubahan GitHub
echo   Mode    : Paksa Update (Abaikan Semua Konflik)
echo ========================================================
echo.

rem 1. Periksa ketersediaan Git
where git >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Git tidak ditemukan di sistem.
    echo Pastikan Git sudah terinstall dan terdaftar di PATH Environment.
    echo.
    pause
    exit /b 1
)

rem 2. Pastikan berada di dalam git repository
git rev-parse --is-inside-work-tree >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Folder ini bukan merupakan repositori Git.
    echo.
    pause
    exit /b 1
)

rem 3. Dapatkan nama branch aktif saat ini (default ke main jika tidak terdeteksi)
set CURRENT_BRANCH=main
for /f "delims=" %%b in ('git rev-parse --abbrev-ref HEAD 2^>nul') do (
    set CURRENT_BRANCH=%%b
)
if "%CURRENT_BRANCH%"=="HEAD" set CURRENT_BRANCH=main
if "%CURRENT_BRANCH%"=="" set CURRENT_BRANCH=main

echo [INFO] Branch target      : %CURRENT_BRANCH%
echo [INFO] Menghubungi remote : origin
echo [INFO] Mengambil data terbaru dari GitHub...
echo.

rem 4. Batalkan merge/rebase jika sebelumnya ada proses yang menggantung
git merge --abort >nul 2>nul
git rebase --abort >nul 2>nul

rem 5. Fetch commit terbaru dari remote GitHub
git fetch --all --prune
if %errorlevel% neq 0 (
    echo [WARNING] Fetch all gagal, mencoba fetch origin %CURRENT_BRANCH%...
    git fetch origin %CURRENT_BRANCH%
)
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Gagal mengambil data (fetch) dari GitHub.
    echo Silakan periksa koneksi internet atau izin akses repositori Anda.
    echo.
    pause
    exit /b 1
)

rem 6. Reset hard ke commit terbaru untuk menimpa perubahan lokal & mengatasi konflik
echo.
echo [INFO] Mengabaikan semua perubahan lokal & menyamakan dengan GitHub...
git reset --hard origin/%CURRENT_BRANCH%
if %errorlevel% neq 0 (
    echo [WARNING] Reset ke origin/%CURRENT_BRANCH% gagal, mencoba reset ke FETCH_HEAD...
    git reset --hard FETCH_HEAD
)
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] Gagal melakukan reset ke versi terbaru.
    echo.
    pause
    exit /b 1
)

rem 7. Bersihkan file/folder baru lokal yang tidak terlacak (untracked)
echo.
echo [INFO] Membersihkan file/folder lokal yang tidak terlacak...
git clean -fd

rem 8. Jalankan migrasi dan bersihkan cache Laravel jika PHP & artisan tersedia
where php >nul 2>nul
if %errorlevel% equ 0 (
    if exist artisan (
        echo.
        echo [INFO] Menjalankan migrasi database (jika ada update struktur)...
        php artisan migrate --force
        echo.
        echo [INFO] Membersihkan cache template Blade & aplikasi Laravel...
        php artisan view:clear
        php artisan optimize:clear
    )
)

echo.
echo ========================================================
echo   Update Berhasil!
echo   Versi commit saat ini:
git log -1 --oneline
echo ========================================================
echo.
pause
