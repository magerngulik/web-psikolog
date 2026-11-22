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

rem Menjalankan Chrome setelah jeda 2 detik tanpa PowerShell (agar ringan di RAM kecil)
start "" /b cmd /c "timeout /t 2 /nobreak >nul && (start chrome http://127.0.0.1:8000 2>nul || start http://127.0.0.1:8000)"

rem Menjalankan server Laravel di foreground
php artisan serve
