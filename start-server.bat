@echo off
setlocal
cd /d "%~dp0"
title Web Psikolog - Laravel Server

echo ========================================================
echo   Memulai Server Web Psikolog (Laravel)
echo   Alamat  : http://127.0.0.1:8000
echo   Browser : Google Chrome
echo   Tekan Ctrl + C di jendela ini untuk mematikan server.
echo ========================================================
echo.

rem Penjagaan awal: pastikan folder cache/framework tersedia (aman, tidak menyentuh database/data)
if not exist "bootstrap\cache" mkdir "bootstrap\cache" >nul 2>nul
if not exist "storage\framework\views" mkdir "storage\framework\views" >nul 2>nul
if not exist "storage\framework\cache\data" mkdir "storage\framework\cache\data" >nul 2>nul
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions" >nul 2>nul

rem Hapus file public\hot jika tertinggal (agar mode offline tidak mencari Vite dev server)
if exist "public\hot" del /f /q "public\hot" >nul 2>nul

rem Bersihkan log jika ukurannya terlalu besar (> 20 MB) agar disk tidak melambat
if exist "storage\logs\laravel.log" (
    for %%A in ("storage\logs\laravel.log") do (
        if %%~zA gtr 20971520 (
            echo [INFO] Membersihkan file log lama yang terlalu besar...
            del /f /q "storage\logs\laravel.log" >nul 2>nul
        )
    )
)

rem Cek kesehatan cache bootstrap Laravel; jika rusak/mengganggu, bersihkan otomatis tanpa menyentuh data
php artisan --version >nul 2>nul
if %errorlevel% neq 0 (
    echo [INFO] Terdeteksi cache sistem mengganggu, membersihkan cache secara aman...
    del /f /q "bootstrap\cache\*.php" >nul 2>nul
    del /f /q "storage\framework\views\*.php" >nul 2>nul
    php artisan package:discover --ansi >nul 2>nul
    php artisan optimize:clear >nul 2>nul
    php artisan optimize >nul 2>nul
)

rem Menjalankan Chrome setelah jeda 2 detik tanpa PowerShell (agar ringan di RAM kecil)
start "" /b cmd /c "timeout /t 2 /nobreak >nul && (start chrome http://127.0.0.1:8000 2>nul || start http://127.0.0.1:8000)"

rem Cache config/route/view agar request lebih cepat (dibuat ulang setiap start)
php artisan optimize >nul 2>nul

rem 2 worker agar request tidak antre (cukup untuk CPU 2 core)
set PHP_CLI_SERVER_WORKERS=2

rem Menjalankan server Laravel di foreground
php artisan serve
